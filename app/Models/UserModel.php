<?php

namespace App\Models;

use App\Libraries\UserException;
use CodeIgniter\Model;
use Exception;

class UserModel extends Model
{
    protected $table = 'users';

    protected $allowedFields = [
        'reference_id',
        'source',
        'name',
        'email',
        'password',
        'created_at',
        'customer_id',
        'subscription_id',
        'subscription_status',
    ];

    // sign in with third party (google, facebook ..) account
    public function signInWithThirdPartyAccount($account, $source)
    {
        // check if user exists
        $user = $this->where('email', $account['email'])->first();
        // add user
        if (!$user) {
            if (!$this->insert([
                'name' => $account['name'],
                'email' => $account['email'],
                'source' => $source,
                'reference_id' => $account['id'],
            ])) {
                throw new Exception("User save failed.");
            }
            $user = $this->where('email', $account['email'])->first();

        } else {
            // check source and reference_id
            if ($user['source'] !== $source || $user['reference_id'] !== $account['id']) {
                throw new UserException("Email's already been used.");
            }
        }
        
        if(!$user) {
            throw new Exception("Server error");
        }
        return $user;
    }


    // sign in with traditional password
    public function signInWithPassword($account)
    {
        $user = $this->where('email', $account['email'])->first();
        if (!$user) {
            throw new UserException('Email does not exist.');
        }

        $authenticatePassword = password_verify($account['password'], $user['password']);
        if (!$authenticatePassword) {
            throw new UserException('Password is incorrect.');
        }

        return $user;
    }

    // for webhook
    public function updateSubscriptionStatus($subscription_id, $customer_id, $subscription_status)
    {
        $user = $this->where([
            'customer_id' => $customer_id,
            'subscription_id' => $subscription_id,
        ])->first();
        if (!$user) {
            return;
        }
        if ($user['subscription_status'] != $subscription_status) {
            $this->update($user->id, [
                'subscription_status' => $subscription_status
            ]);
        }
    }

    public function getSubscriptionStatus()
    {
        $user = $this->where('id', session()->id)->first();
        return empty($user) ? FALSE : $user['subscription_status'];
    }
}
