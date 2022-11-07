<?php

namespace App\Controllers;

use App\Models\UserModel;

class Webhook extends BaseController
{
    public function index()
    {
        $config = config('App\\Stripe');
        $endpoint_secret = $config->endpoint_secret;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // log_message('error', 'payment intent:'. print_r($paymentIntent, true));
                break;

            case 'checkout.session.completed':
                $checkout = $event->data->object;
                log_message('info', "[webhook] receive checkout:  \r\nid = {$checkout->id}, status = {$checkout->status} \r\ncustomer = {$checkout->customer} \r\nsubscription = {$checkout->subscription}" );
                break;

            case 'customer.subscription.updated':
                $subscription = $event->data->object;
                log_message('info', "[webhook] receive subscription:  \r\nid = {$subscription->id}, status = {$subscription->status} \r\ncustomer = {$subscription->customer}" );
                $userModel = new UserModel();
                $userModel->updateSubscriptionStatus($subscription->id, $subscription->customer, $subscription->status);
                break;

            default:
                echo 'Received unknown event type ' . $event->type;
                break;
        }

        http_response_code(200);
    }
}
