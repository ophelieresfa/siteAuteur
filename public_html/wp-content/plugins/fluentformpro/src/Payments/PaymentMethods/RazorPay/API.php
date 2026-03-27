<?php

namespace FluentFormPro\Payments\PaymentMethods\RazorPay;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\Framework\Helpers\ArrayHelper;

class API
{
    public function verifyIPN()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $postData = file_get_contents('php://input');
        if (!$postData) {
            return;
        }

        // Verify RazorPay webhook signature if present
        $signature = isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) ? $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] : '';
        if ($signature) {
            $keys = RazorPaySettings::getApiKeys();
            $expectedSignature = hash_hmac('sha256', $postData, $keys['api_secret']);
            if (!hash_equals($expectedSignature, $signature)) {
                status_header(401);
                exit;
            }
        }

        $data = json_decode($postData, true);
        if (!$data || !is_array($data)) {
            return;
        }

        $data = wp_parse_args($data, $_REQUEST);
        $this->handleIpn($data);
        exit(200);
    }

    protected function handleIpn($data)
    {
        $submissionId = intval(ArrayHelper::get($data, 'submission_id'));
        if (!$submissionId || empty($data['id'])) {
            return;
        }
        $submission = wpFluent()->table('fluentform_submissions')->where('id', $submissionId)->first();
        if (!$submission) {
            return;
        }
        $vendorId = sanitize_text_field($data['id']);
        $vendorTransaction = $this->makeApiCall('payments/'.$vendorId, [], $submission->form_id, 'GET');

        if(is_wp_error($vendorTransaction)) {
            $logData = [
                'parent_source_id' => $submission->form_id,
                'source_type'      => 'submission_item',
                'source_id'        => $submission->id,
                'component'        => 'Payment',
                'status'           => 'error',
                'title'            => __('RazorPay Payment Webhook Error', 'fluentformpro'),
                'description'      => $vendorTransaction->get_error_message()
            ];
            do_action('fluentform/log_data', $logData);
        }

        $status = $vendorTransaction['status'];

        if($status == 'captured') {
            $status = 'paid';
        }

        do_action_deprecated(
            'fluentform_ipn_razorpay_action_' . $status,
            [
                $submission,
                $vendorTransaction,
                $data
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/ipn_razorpay_action_' . $status,
            'Use fluentform/ipn_razorpay_action_' . $status . ' instead of fluentform_ipn_razorpay_action_' . $status
        );

        do_action('fluentform/ipn_razorpay_action_' . $status, $submission, $vendorTransaction, $data);

        if($refundAmount = ArrayHelper::get($vendorTransaction, 'amountRefunded.value')) {
            $refundAmount = intval($refundAmount * 100); // in cents
            do_action_deprecated(
                'fluentform_ipn_razorpay_action_refunded',
                [
                    $refundAmount,
                    $submission,
                    $vendorTransaction,
                    $data
                ],
                FLUENTFORM_FRAMEWORK_UPGRADE,
                'fluentform/ipn_razorpay_action_refunded',
                'Use fluentform/ipn_razorpay_action_refunded instead of fluentform_ipn_razorpay_action_refunded.'
            );
            do_action('fluentform/ipn_razorpay_action_refunded', $refundAmount, $submission, $vendorTransaction, $data);
        }
    }

    public function makeApiCall($path, $args, $formId, $method = 'GET')
    {
        $keys = RazorPaySettings::getApiKeys($formId);
        $headers = [
            'Authorization' => 'Basic '.base64_encode($keys['api_key'].':'.$keys['api_secret']),
            'Content-type' => 'application/json'
        ];
        if($method == 'POST') {
            $response = wp_remote_post('https://api.razorpay.com/v1/'.$path, [
                'headers' => $headers,
                'body' => json_encode($args)
            ]);
        } else {
            $response = wp_remote_get('https://api.razorpay.com/v1/'.$path, [
                'headers' => $headers,
                'body' => $args
            ]);
        }

        if(is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $responseData = json_decode($body, true);

        if(!empty($responseData['error'])) {
            $message = ArrayHelper::get($responseData, 'error.description');
            if(!$message) {
                $message = __('Unknown RazorPay API request error', 'fluentformpro');
            }
            return new \WP_Error(423, $message, $responseData);
        }

        return $responseData;
    }
}
