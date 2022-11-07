<?php

namespace App\Controllers;

use App\Libraries\StripeApi;
use App\Models\UserModel;
use Exception;

// 收费
class Charge extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    // callbacks, use them with site_url() helper function
    private $_success_callback = 'payment_success';
    private $_cancel_callback = 'payment_cancel';

    // 开启新的订阅
    public function start()
    {
        $session = session();
        if ($session->is_logged_in) {
            // 将已有订阅重定向到续费
            $userModel = new UserModel();
            $subscription_status = $userModel->getSubscriptionStatus();
            if ($subscription_status && $subscription_status !== 'active' && $subscription_status !== 'trialing') {
                return redirect()->to('/restore');
            }
        } else {
            // return redirect()->to('/sign-in');
        }

        echo view('payment/start', ['is_paid' => $session->is_paid]);
    }

    // 恢复订阅
    public function restore()
    {
        $session = session();
        $subscription_id = $session->subscription_id;

        // 查询订阅关联的最新发票并取得付费链接
        $payment_link = '';
        $message = '';
        try {
            $api = new StripeApi();
            $subscription = $api->getSubscriptionInfo($subscription_id);
            // 如果状态已经为active就跳转
            if ($subscription['status'] === 'active') {
                $user = $this->user->where('id', $this->session->id)->first();
                $this->session->set('is_paid', TRUE);
                // subscription status updated?
                if ($subscription['status'] != $user['subscription_status']) {
                    $this->user->update($user['id'], ['subscription_status' => 'active']);
                }

                return redirect()->to('/exam');
            }
            $latest_invoice = $subscription['latest_invoice'];
            // 取得订阅的末次发票， 如果已支付， 则创建一个新发票
            $invoice = $api->getInvoiceInfo($latest_invoice);
            log_message('info', print_r($invoice, true));
            if ($invoice && $invoice['status'] === 'open') {
                $payment_link = $invoice['checkout_url'];
            } else {
                $message = 'Pending invoice not found. <br>Please wait a few minutes to refresh the page if you have already paid.';
            }
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . "\r\n" . $th->getTraceAsString());

            $message = 'Service not available now. <br>If you have any question, please contact with the administrator.';
        }



        echo view('payment/restore', [
            'message' => $message,
            'payment_link' => $payment_link,
        ]);
    }

    // 提交新订阅
    public function purchase()
    {
        $session = session();
        $userModel = new UserModel();
        $api = new StripeApi();
        try {
            $customer_id = $session->customer_id;
            if (!$customer_id) {
                // 创建用户stripe账户
                $customer_id = $api->createCustomer($session->email);
                // 更新用户customer_id信息
                if (!$userModel->update($session->id, ['customer_id' => $customer_id])) {
                    throw new Exception("user {$session->id} update customer_id => {$customer_id} failed.");
                }
                $session->set('customer_id', $customer_id);
            }


            $data = [
                'customer_id' => $customer_id,
                'success_callback' => site_url($this->_success_callback),
                'cancel_callback' => site_url($this->_cancel_callback),
            ];
            $checkout_session = $api->createCheckoutSession($data);
            return $this->respondCreated([
                'errCode' => 0,
                'url' => $checkout_session['checkout_url']
            ]);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . "\r\n" . $th->getTraceAsString());
            return $this->respondCreated([
                'errCode' => 5000,
                'errMsg' => 'server error'
            ]);
        }
    }

    // 订阅付款成功, 更新用户订阅ID和付款状态
    public function purchase_success()
    {
        $api = new StripeApi();
        $session = session();
        try {
            $checkout_session_id = $this->request->getGet('session_id'); // 付款标识
            if (!$checkout_session_id) {
                throw new Exception("invalid session_id", 1);
            }
            $checkout_session = $api->getCheckoutSession($checkout_session_id);

            if ($checkout_session) {
                $userModel = new UserModel();
                // 记录 subscription_id
                if (!$userModel->update($session->id, [
                    'subscription_id' => $checkout_session['subscription_id'],
                    'subscription_status' => 'active',
                ])) {
                    throw new Exception("user {$session->id} update subscription_id => {$checkout_session['subscription_id']} failed.");
                }
                $session->set('subscription_id', $checkout_session['subscription_id']);
                $session->set('is_paid', TRUE);
            }


            echo view('payment/success');
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage() . "\r\n" . $th->getTraceAsString());

            echo 'error occurred.';
        }
    }

    // 订阅付款取消
    public function purchase_cancel()
    {
        echo view('payment/cancel');
    }
}
