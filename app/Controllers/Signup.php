<?php 
namespace App\Controllers;

use App\Libraries\UserException;
use App\Models\EmailVerificationModel;

class Signup extends BaseController
{
    public function index()
    {
        helper(['form']);
        $data = [];
        echo view('signup', $data);
    }

    public function register()
    {
        helper(['form']);
        $rules = [
            'name'          => 'required|min_length[2]|max_length[50]',
            'email'         => 'required|min_length[4]|max_length[100]|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[4]|max_length[50]',
            'confirmpassword'  => 'matches[password]'
        ];
        $messages = [
            'email' => [
                'is_unique' => 'This email\'s already been registered.',
            ],
        ];
        $data = [
            'name'     => $this->request->getVar('name'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];
          
        if(! $this->validate($rules, $messages)){
            $data['validation'] = $this->validator;
            echo view('signup', $data);
            exit;
        }

        $data['error'] = '';
        $data['is_mail_sent'] = FALSE;
        $verifyModel = new EmailVerificationModel();
        $verify_code = $verifyModel->newVerification($data);
        if(FALSE === $verify_code) {
            $data['error'] = 'Service unavailable now';

        } else {
            $title = 'Verify your email';
            $body = "Welcome to Quiz. Before you get started, we need you to verify your email address. <a href='". site_url("verify-email?code={$verify_code}&email={$data['email']}") ."'>Verify Email Address</a>";
            $email = \Config\Services::email();
            $email->setTo($data['email']);
            $email->setSubject($title);
            $email->setMessage($body);
            $send = $email->send();
            if($send) {
                log_message('info', 'email sent to: '. $data['email']);
                $data['is_mail_sent'] = TRUE;
    
            } else {
                log_message('error', 'email error to: '. $data['email']);
                $data['error'] = 'Mail service unavailable. Please contact with the administrator.';
                
            }
        }
        
        echo view('signup', $data);
    }

    public function verify_email() {
        $error = '';
        try {
            $code = $this->request->getGet('code');
            $email = $this->request->getGet('email');
            if (!$code || !$email) {
                throw new UserException('Invalid request.');
            }
            $verifyModel = new EmailVerificationModel();
            $data = $verifyModel->verify($code, $email);

            $this->user->save($data);

        } catch (UserException $e) {
            $error = $e->getMessage();

        } catch (\Throwable $th) {
            exception_logger($th);
            $error = 'Service unavailable.';
        }
        
        echo view('verify_email', [
            'error' => $error
        ]);

    }
  
}