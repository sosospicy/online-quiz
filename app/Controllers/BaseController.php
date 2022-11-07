<?php

namespace App\Controllers;

use App\Libraries\StripeApi;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['logger'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
        $this->user = new UserModel();

    }

    public function setSession($user) 
    {
        // check subscription status
        $is_paid = FALSE;
        if ($user['subscription_id']) {
            try {
                $api = new StripeApi();
                $info = $api->getSubscriptionInfo($user['subscription_id']);
                if ($info['is_active']) {
                    $is_paid = TRUE;
                }
                // subscription status updated?
                if ($info['status'] != $user['subscription_status']) {
                    $this->user->update($user['id'], ['subscription_status' => $info['status']]);
                }
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage() . "\r\n". $th->getTraceAsString());
            }
        }
        $ses_data = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'is_logged_in' => TRUE, 
            'is_paid' => $is_paid,
            'customer_id' => $user['customer_id'],
            'subscription_id' => $user['subscription_id'],
        ];
        $this->session->set($ses_data);
    }
}
