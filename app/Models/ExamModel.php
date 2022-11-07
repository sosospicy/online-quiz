<?php 
namespace App\Models;  
use CodeIgniter\Model;
  
class ExamModel extends Model{
    protected $table = 'exams';
    
    protected $allowedFields = [
        'id', 
        'code', 
        'title',
        'question_list', 
        'created_by', 
        'is_active', 
        'created_at'
    ];
}