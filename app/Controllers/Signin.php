<?php

namespace App\Controllers;

use App\Libraries\UserException;

class Signin extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    public function index()
    {
        // check if already logged in
        if (session()->is_logged_in) {
            return redirect()->to('/');
        }

        helper(['form']);
        echo view('signin');
    }

    public function privacy()
    {
        echo view('privacy');
    }

    public function auth($source)
    {
        $config = config('App\\Auth');
        
        $origin = $this->session->get('sign_from');
        $success_redirect_to = !empty($origin) ? $origin : '/'; // redirect path after successfully logged in
        try {
            switch ($source) {
                case 'local':
                    $user = $this->user->signInWithPassword([
                        'email' => $this->request->getVar('email'),
                        'password' => $this->request->getVar('password')
                    ]);

                    $this->setSession($user);
                    return redirect()->to($success_redirect_to);
                    break;

                case 'google':
                    $post = $this->request->getPost(); // credential, g_csrf_token

                    // 验证csrf token
                    if (!isset($post['g_csrf_token']) || !$post['g_csrf_token']) {
                        $this->session->setFlashdata('msg', 'No CSRF token.');
                        return redirect()->to('/sign-in');
                    }

                    // 验证登录凭据
                    $id_token = $post['credential'];
                    $CLIENT_ID = $config->googleApp['client_id'];
                    $client = new \Google\Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
                    $payload = $client->verifyIdToken($id_token);
                    if ($payload) {
                        log_message('info', 'google auth user:' . print_r($payload, true));

                        $user = $this->user->signInWithThirdPartyAccount([
                            'id' => $payload['sub'],
                            'name' => $payload['name'],
                            'email' => $payload['email'],
                        ], 'google');

                        $this->setSession($user);
                        return redirect()->to($success_redirect_to);
                    } else {
                        // Invalid ID token
                        $this->session->setFlashdata('msg', 'Invalid token.');
                        return redirect()->to('/sign-in');
                    }
                    break;

                case 'facebook': // use forwardFacebook instead currenctly 
                    $code = $this->request->getGet('code');
                    $state = $this->request->getGet('state');

                    $facebook = new \Facebook\Facebook([
                        'app_id' => $config->facebookApp['app_id'],
                        'app_secret' => $config->facebookApp['app_secret'],
                        'default_graph_version' => 'v2.3',
                    ]);

                    if (!$code || !$state) {
                        throw new UserException("Invalid request");
                    }

                    $fb_helper = $facebook->getRedirectLoginHelper();
                    $fb_helper->getPersistentDataHandler()->set('state', $state);


                    if ($this->session->get('fb_access_token')) {
                        $fb_access_token = $this->session->get('fb_access_token');
                    } else {
                        $fb_access_token = $fb_helper->getAccessToken();
                        $this->session->set('fb_access_token', $fb_access_token);
                        $facebook->setDefaultAccessToken($this->session->get('fb_access_token'));
                    }

                    $graph_response = $facebook->get('/me?fields=name,email', $fb_access_token);
                    $fb_user_info = $graph_response->getGraphUser();
                    log_message('info', 'facebook auth user:' . print_r($fb_user_info, true));

                    if (!empty($fb_user_info['id'])) {
                        $user = $this->user->signInWithThirdPartyAccount([
                            'id' => $fb_user_info['id'],
                            'name' => $fb_user_info['name'],
                            'email' => $fb_user_info['email'],
                        ], 'facebook');

                        $this->setSession($user);
                        return redirect()->to($success_redirect_to);
                    } else {
                        $this->session->setFlashdata('msg', 'Auth failed');
                        return redirect()->to('/sign-in');
                    }

                    break;

                default:
                    throw new UserException("Invalid source");
                    break;
            }
        } catch (UserException $e) {
            $this->session->setFlashdata('msg', $e->getMessage());
            return redirect()->to('/sign-in');
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . "\r\n" . $th->getTraceAsString());
            $this->session->setFlashdata('msg', 'Service unavailable.');
            return redirect()->to('/sign-in');
        }
    }

    // facebook 结合js sdk请求
    public function forwardFacebook()
    {
        $access_token = $this->request->getPost('accessToken');
        $uid =  $this->request->getPost('userID');

        $origin = $this->session->get('sign_from');
        $success_redirect_to = !empty($origin) ? $origin : '/'; // redirect path after successfully logged in

        try {
            if (!$access_token || !$uid) {
                throw new UserException('Invalid request.');
            }
            $client = \Config\Services::curlrequest();
            $response = $client->get("https://graph.facebook.com/{$uid}?fields=id,name,email&access_token={$access_token}");
            $body = $response->getBody();
            log_message('info', 'facebook get user >>> :' . print_r($body, true));
            $user_data = json_decode($body, true);
            if (!empty($user_data) && !empty($user_data['email'])) {
                $user = $this->user->signInWithThirdPartyAccount([
                    'id' => $user_data['id'],
                    'name' => $user_data['name'],
                    'email' => $user_data['email'],
                ], 'facebook');

                $this->setSession($user);
                return $this->respondCreated([
                    'errCode' => 0,
                    'to' => site_url($success_redirect_to),
                ]);

            } else {
                throw new UserException('Verification failed. Authorization is missing or incorrect information is provided.');
            }


        } catch (UserException $e) {
            return $this->respondCreated([
                'errCode' => 2000,
                'errMsg' => $e->getMessage()
            ]);

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . $th->getTraceAsString());
            return $this->respondCreated([
                'errCode' => 5000,
                'errMsg' => 'server error'
            ]);
        }
    }

    public function signout()
    {
        // 如果通过facebook登录，则跳转到facebook登出
        if ($this->session->get('fb_access_token')) {
            log_message('info', 'should logout from facebook');
            $config = config('App\\Auth');
            $fb = new \Facebook\Facebook([
                'app_id' => $config->facebookApp['app_id'],
                'app_secret' => $config->facebookApp['app_secret'],
                'default_graph_version' => 'v2.3',
            ]);
            $helper = $fb->getRedirectLoginHelper();
            $logout_from_facebook = $helper->getLogoutUrl($this->session->get('fb_access_token'), site_url('sign-in'));
            if ($logout_from_facebook) {
                header('Location: '. $logout_from_facebook);
                // return redirect()->to($logout_from_facebook);
            }
        }

        $this->session->destroy();
        return redirect()->to('/sign-in');
        
    }

    // facebook 账号移除说明
    public function accountCloseDesc()
    {
        echo view('discription_account_close');
    }
}
