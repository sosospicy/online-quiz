<?php 
namespace App\Models;

use App\Libraries\UserException;
use CodeIgniter\Model;
  
class QuestionModel extends Model{
    protected $table = 'questions';
    
    protected $allowedFields = [
        'id',
        'type',
        'category_id',
        'title',
        'created_by',
        'created_at',
        'updated_at',
        'active',
        'score'
    ];


    /**
     * 加载题库
     * @param array $question_list 题目ID列表 默认为空，加载所有题目
     * @return array $data 题目及选项数据
     */
    public function load($question_list = [])
    {
        // 加载试题及选项
        $query = $this->where('is_active', 1);
        if (!empty($question_list)) {
            $query->whereIn('id', $question_list);
        }
        $questions = $query->findAll();
        if (empty($questions)) {
            throw new UserException('exam load failed(1)');
        }

        $query_options = new OptionModel();
        if (!empty($question_list)) {
            $query_options->whereIn('question_id', $question_list);
        }
        $options = $query_options->findAll();
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
}