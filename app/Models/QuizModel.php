<?php 
namespace App\Models;  
use CodeIgniter\Model;
  
class QuizModel extends Model{
    protected $table = 'quizzes';
    
    protected $allowedFields = [
        'id', 
        'exam_code', 
        'score', 
        'failed_question_list', 
        'submitted_by', 
        'submitted_at'
    ];
}