<?php


class Woo_Just_Pay_SMP extends WC_Payment_Woo_Just_Pay_SMP
{
    public function __construct()
    {
        parent::__construct();
    }

    public function doPayment(array $params)
    {
        $time = date('Y-m-d\TH:i:s');
        $payment_method = $params['payment_method'];
        $channel = $this->getChannel($payment_method);

        $order_id = $params['id_order'];
        $order = new WC_Order($order_id);
        $amount = $order->get_total();
        $currency = $order->get_currency();
        $trans_id = $order_id . "_" . time();
        $url_ok = get_bloginfo( 'url' ) . "?just_pay_order_id=$order_id";
        $url_error = get_bloginfo( 'url' ) . "?status=error&just_pay_order_id=$order_id";
        $url_finalizar = get_bloginfo( 'url' ) . "?just_pay_order_id=$order_id";

        $data_sign = "$this->public_key$time$amount$currency$trans_id$this->expiration_time$url_ok$url_error$channel$this->secure_key";

        $signature = hash('sha256', $data_sign);

        $data = [
            'public_key' => $this->public_key,
            'time' => $time,
            'channel' => $channel,
            'amount' => $order->get_total(),
            'currency' => $order->get_currency(),
            'trans_id' => $trans_id,
            'time_expired' => $this->expiration_time,
            'url_ok' => $url_ok,
            'url_error' => $url_error,
            'url_finalizar' => $url_finalizar,
            'signature' => $signature,
            'name_shopper' => $order->get_shipping_first_name() ? $order->get_shipping_first_name()  : $order->get_billing_first_name(),
            'las_name_Shopper' => $order->get_billing_last_name() ? $order->get_billing_last_name() : $order->get_shipping_last_name(),
            'email' => $order->get_billing_email(),
            'country_code' => $order->get_billing_country() ? $order->get_billing_country() : $order->get_shipping_country(),
            'phone' => $order->get_billing_phone(),
            'mobile' => $order->get_billing_phone()
        ];

        $url_payment = wp_safe_remote_post($this->end_point, ['body' => $data]);

        if (is_wp_error($url_payment))
            return ['status' => false, 'message' => $url_payment->get_error_message()];

        if ( $url_payment['response']['code'] != 200 )
            return ['status' => false, 'message' => __('An error has occurred when requesting the session for payment','woo-just-pay')];

        $url = wp_remote_retrieve_body( $url_payment );

        if ($this->getHandlePayment($payment_method) === 'lightbox')
            $url = $order->get_checkout_payment_url(true) . "&url_just_pay=" .  urlencode($url);

        return ['status' => true, 'url' => $url];

    }

    public function getChannel($method)
    {
        $channel = '1';
        if ($method === 'woo_just_pay_cash_smp')
            $channel = '2';
        if ($method === 'woo_just_pay_cards_smp')
            $channel = '3';
        return $channel;
    }

    public function getHandlePayment($method)
    {
        $payment = new WC_Payment_Woo_Just_Pay_SMP();
        if ($method === 'woo_just_pay_cash_smp')
            $payment = new WC_Payment_Woo_Just_Pay_Cash_SMP();
        if ($method === 'woo_just_pay_cards_smp')
            $payment = new WC_Payment_Woo_Just_Pay_Cards_SMP();

        return $payment->handle_payment_method;
    }

}