<?php 
namespace App\Models;

use App\Libraries\UserException;
use CodeIgniter\Model;
  
class EmailVerificationModel extends Model{
    protected $table = 'email_verification';
    
    protected $allowedFields = [
        'id',
        'code',
        'data',
        'created_at',
        'is_verified'
    ];

    public function newVerification($data) {
        $uniqid = md5(uniqid());
        $insert = [
            'code' => $uniqid,
            'data' => json_encode($data)
        ];
        if ($this->insert($insert)) {
            return $uniqid;
        }
        return FALSE; // failed
    }

    public function verify($code, $email) {
        $record = $this->where('code', $code)->first();
        if (!$record ) {
            throw new UserException('Unknown verification link.');
        }
        if ($record['is_verified'] == 1) {
            throw new UserException('Already signed up. Please sign in directly.');
        }
        $data = json_decode($record['data'], TRUE);
        if ($email !== $data['email']) {
            throw new UserException('Email not match.');
        } 

        $this->update($record['id'], ['is_verified' => 1]);
        return $data;
    }
}