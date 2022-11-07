<?php

namespace App\Libraries;

require FCPATH . '../vendor/autoload.php';

class StripeApi
{
    private $_config;
    private $_stripe;
    public function __construct()
    {
        $this->_config = config('App\\Stripe');
        \Stripe\Stripe::setApiKey($this->_config->APIKEY);
        $this->_stripe = new \Stripe\StripeClient($this->_config->APIKEY);
    }
    /**
     * 创建用户账户
     * @param string $email
     * @return string $customer_id  return FALSE when failed
     */
    public function createCustomer($email)
    {
        $customer = $this->_stripe->customers->create([
            'email' => $email,
        ]);
        if (!$customer) {
            return FALSE;
        }
        return $customer['id'];
    }

    /** 
     * 读取订阅信息
     * @param string $subscription_id
     * @return array $info
     **/

    public function getSubscriptionInfo($subscription_id)
    {
        $subscription = $this->_stripe->subscriptions->retrieve(
            $subscription_id,
            []
        );
        if (!$subscription) {
            return [];
        }
        log_message('info', "check subscription: {$subscription['id']} status: {$subscription['status']}");

        return [
            'id' => $subscription['id'],
            'status' => $subscription['status'],
            'is_active' => $subscription['status'] === 'active' || $subscription['status'] === 'trialing',
            'latest_invoice' => $subscription['latest_invoice'],
        ];
    }

    /**
     * 读取发票信息
     * @param string $invoice_id
     */
    public function getInvoiceInfo($invoice_id)
    {
        $invoice = $this->_stripe->invoices->retrieve($invoice_id, []);
        if (!$invoice) {
            return [];
        }

        return [
            'id' => $invoice['id'],
            'status' => $invoice['status'],
            'checkout_url' => $invoice['hosted_invoice_url'],
        ];
    }

    public function getCheckoutSession($checkout_session_id)
    {
        $checkout_session = $this->_stripe->checkout->sessions->retrieve($checkout_session_id, []);
        return [
            'status' => $checkout_session->status,
            'subscription_id' => $checkout_session->subscription,
        ];
    }

    /**
     * 创建新订阅付款会话
     * @param array[] [
     *  string $customer_id,
     *  string $success_callback,
     *  string $cancel_callback
     * ]
     * 
     * @return array[] [ 
     *  string $checkout_id,
     *  string $checkout_url,
     * ]
     */
    public function createCheckoutSession($params)
    {
        $data = [
            'customer' => $params['customer_id'],
            'line_items' => [[
                'price' => $this->_config->price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' =>  $params['success_callback'] . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $params['cancel_callback'],
            'subscription_data' => [
                'trial_period_days' => $this->_config->trial_days,
            ],
        ];

        $checkout_session = \Stripe\Checkout\Session::create($data);

        return [
            'checkout_id' => $checkout_session->id,
            'checkout_url' => $checkout_session->url,
        ];
    }


}
