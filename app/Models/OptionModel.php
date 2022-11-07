<?php 
namespace App\Models;  
use CodeIgniter\Model;
  
class OptionModel extends Model{
    protected $table = 'options';
    
    protected $allowedFields = [
        'id', 
        'question_id', 
        'content', 
        'is_right', 
        'updated_at'
    ];
}