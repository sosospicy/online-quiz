<?php

namespace App\Controllers;

use App\Libraries\UserException;
use App\Models\QuestionModel;
use App\Models\ExamModel;
use App\Models\UserModel;

class Exam extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    // exam list
    public function index()
    {
        if (ENVIRONMENT === 'production') {
            // check subscription status again to avoid past_due or other abnormal subscription
            $userModel = new UserModel();
            $subscription_status = $userModel->getSubscriptionStatus();
            if ($subscription_status != 'active' && $subscription_status != 'trialing') {
                $session = session();
                $session->set('is_paid', FALSE);
                return redirect()->to('/subscription');
            }
        }

        $session = session();
        $examModel = new ExamModel();
        $data = [
            'title' => 'Exams',
            'list' => $examModel->where([
                'created_by' => $session->id,
                'is_active' => 1,
            ])->orderBy('id', 'DESC')
                ->findAll()
        ];
        echo view('exam/list', $data);
    }

    // question list
    public function create()
    {
        // load questions
        // 暂时为一次性加载
        // 题库量大的话，后期优化为分批次加载
        $questModel = new QuestionModel();
        $data = [
            'title' => 'Create Exam',
            'list' => $questModel->where('is_active', 1)
                ->findAll()
        ];

        echo view('exam/create', $data);
    }

    // store an exam
    public function store()
    {
        try {
            $title = $this->request->getPost('title');
            $questions = $this->request->getPost('questions');
            if (empty($questions)) {
                throw new UserException('no question selected');
            }
            foreach ($questions as $k => $qid) {
                $questions[$k] = (int) $qid;
                if (!$questions[$k]) {
                    throw new UserException('invalid question');
                }
            }

            $session = session();
            $examModel = new ExamModel();
            $exam = [
                'code' => uniqid(substr($session->id, -3)),
                'question_list' => json_encode($questions),
                'created_by' => $session->id,
            ];
            $exam['title'] = empty($title) ? 'exam-' . $exam['code'] : $title;

            $saveResult = $examModel->insert($exam);
            if (!$saveResult) {
                throw new UserException('operation failed');
            }

            return $this->respondCreated([
                'errCode' => 0,
            ]);
        } catch (UserException $e) {
            return $this->respondCreated([
                'errCode' => 2000,
                'errMsg' => $e->getMessage()
            ]);
        } catch (\Throwable $th) {
            return $this->respondCreated([
                'errCode' => 5000,
                'errMsg' => 'server error'
            ]);
        }
    }

    // export all questions
    public function export()
    {
        try {
            $questionModel = new QuestionModel();
            $questions = $questionModel->load();
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . "\r\n" . $th->getTraceAsString());
            $questions = [];
        }

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('exam/pdf_view', ['title' => 'Training Material', 'questions' => $questions,]));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}
