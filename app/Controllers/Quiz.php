<?php

namespace App\Controllers;

use App\Models\OptionModel;
use App\Models\QuestionModel;
use App\Models\ExamModel;
use App\Models\QuizModel;
use App\Libraries\UserException;

class Quiz extends BaseController {
    use \CodeIgniter\API\ResponseTrait;

    /** 加载指定考卷的题目数据
     * @param string $exam_code 考卷exam.code
     * @return array $data 题目和选项
    */ 
    private function loadExam($exam_code) {
        // 加载考卷
        $examModel = new ExamModel();
        $exam = $examModel->where('code', $exam_code)->first();
        if (empty($exam)) {
            throw new UserException('exam not found');
        }

        // 加载试题及选项
        $question_list = json_decode($exam['question_list']);
        $questionModel = new QuestionModel();
        $questions = $questionModel->where('is_active', 1)
            ->whereIn('id', $question_list)
            ->findAll();
        if (empty($questions)) {
            throw new UserException('exam load failed(1)');
        }

        $optionModel = new OptionModel();
        $options = $optionModel->whereIn('question_id', $question_list)->findAll();
        if (empty($questions)) {
            throw new UserException('exam load failed(2)');
        }

        // 选项放到题目数据中
        $data = [];
        foreach ($questions as $key => $question_item) {
            $data[$question_item['id']] = [
                'id' => $question_item['id'],
                'type' => $question_item['type'],
                'title' => $question_item['title'],
                'score' => $question_item['score'],
                'options' => []
            ];
        }
        foreach ($options as $option_item) {
            if (!isset($data[$option_item['question_id']])) {
                continue;
            }
            $data[$option_item['question_id']]['options'][] = [
                'id' => $option_item['id'],
                'content' => $option_item['content'],
                'is_right' => $option_item['is_right'],
            ];
        }

        return $data;
    }

    /** 检查当前用户是否已参加过测试
     * @param string $exam_code
     * @return array [bool isDone, array quizRecord]
     */ 
    private function checkDone($exam_code) {
        $quizModel = new QuizModel();
        $quiz_record = $quizModel->where('exam_code', $exam_code)->where('submitted_by', session()->id)->first();
        return [!empty($quiz_record), $quiz_record];
    }

    // 考试界面
    public function index($exam_code = null) {
        $questions = [];
        $error = '';
        if (!$exam_code) {
            $error = 'Seems like you have a broken link. <br/>Please check again.';
        } else {
            // 检查是否已有考试记录
            list($is_done, $quiz_record) = $this->checkDone($exam_code);
            if ($is_done) {
                $error = 'You have already taken this quiz and got '. $quiz_record['score'].' points.';
            } else {
                try {
                    $questions = $this->loadExam($exam_code);
                    if (empty($questions)) {
                        $error = 'No data';
                    } else {
                        shuffle($questions);
                    }

                } catch (UserException $e) {
                    $error = $e->getMessage();

                } catch (\Throwable $th) {
                    log_message('error', __METHOD__ . ' '. $th->getMessage());
                    $error = 'Exam load error.';
                }
            }
        }
        
        
        echo view('quiz/quiz', [
            'title' => 'Exam',
            'without_menu' => TRUE,
            'error' => $error,
            'exam_id' => $exam_code,
            'questions' => $questions
        ]);
    }

    // 交卷 打分 记录
    public function handIn() {
        $exam_code = $this->request->getPost('exam_id');
        $answers = $this->request->getPost('answers');
        
        try {
            // 检查是否已有考试记录
            list($is_done,) = $this->checkDone($exam_code);
            if ($is_done) {
                throw new UserException("You have already taken this quiz.");
            }

            if (!$exam_code) {
                throw new UserException("Invalid exam");
            } 
            $questions = $this->loadExam($exam_code, ['with_right_answer' => true]);
            if (empty($questions)) {
                throw new UserException("Exam not found");
            }

            $get_score = 0; // 得分
            $total_score = 0; // 题目总分
            $failed_questions = []; // 错题ID
            $right_answer_list = []; // 正确答案 返回前端
            foreach ($questions as $question_id => $question) {
                $total_score += (int) $question['score'];
                
                // 用户提交答案
                $answer = isset($answers[$question_id]) ? $answers[$question_id] : false;
                if ($answer === false) {
                    // 未提交题目答案视为错误
                    $failed_questions[] = $question_id;
                    continue;
                }
                // 用户答案整理为字符串
                if (is_array($answer)) {
                    sort($answer, SORT_NUMERIC);
                    $user_answer = implode('|', $answer);
                } else {
                    $user_answer = $answer;
                }

                // 正确答案 整理为字符串
                $right_answer = '';
                switch ($question['type']) {
                    case 'radio':
                    case 'checkbox': 
                        # 单选和多选正确答案
                        $right_options = [];
                        foreach($question['options'] as $option) {
                            if ($option['is_right'] == 1) {
                                $right_options[] = $option['id'];
                            }
                        }
                        sort($right_options, SORT_NUMERIC);
                        $right_answer = implode('|', $right_options);
                        $right_answer_list[$question_id] = $right_options;
                        
                        break;
                    case 'textarea':
                        // 简答题正确答案
                        $right_answer = $question['options'][0]['content'];
                        $right_answer_list[$question_id] = $right_answer;

                        break;
                    default:
                        # code...
                        break;
                }
                
                // 答案对比及评分
                if ($user_answer === $right_answer) {
                    $get_score += (int) $question['score'];
                } else {
                    $failed_questions[] = $question_id;
                }
                log_message('error', "user answer: $user_answer; right_answer: $right_answer; score: $get_score");
            }

            // 得分处理为百分制
            $final_score = $total_score > 0 ? round(100 * $get_score / $total_score) : 100;

            // 记录
            $quizModel = new QuizModel();
            $session = session();
            $record_result = $quizModel->insert([
                'exam_code' => $exam_code,
                'score' => $final_score,
                'failed_question_list' => json_encode($failed_questions),
                'submitted_by' => $session->id,
            ]);
            if (!$record_result) {
                throw newUserException("quiz submit failed");
            }


            return $this->respondCreated([
                'errCode' => 0,
                'final_score' => $final_score,
                'right_answer_list' => $right_answer_list,
                'failed_questions' => $failed_questions,
            ]);

        } catch (UserException $e) {
            return $this->respondCreated([
                'errCode' => 2000,
                'errMsg' => $e->getMessage()
            ]);
        } catch (\Throwable $th) {
            log_message('error', 'exception:'. $th->getMessage(). ' '. $th->getTraceAsString());
            return $this->respondCreated([
                'errCode' => 5000,
                'errMsg' => 'server error'
            ]);
        }
        
    }
}