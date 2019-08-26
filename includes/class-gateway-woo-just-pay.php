<?php


class WC_Payment_Woo_Just_Pay_SMP extends WC_Payment_Gateway
{
    public function __construct()
    {
        $shop_currency = get_option('woocommerce_currency');

        $this->id = 'woo_just_pay_smp';
        $this->icon = woo_just_pay_smp()->plugin_url . "assets/images/online-$shop_currency.png";
        $this->method_title = __('Just Pay Online', 'woo-just-pay');
        $this->method_description = __('Just Pay Online payment method', 'woo-just-pay');
        $this->description  = $this->get_option( 'description' );
        $this->order_button_text = __('to pay', 'woo-just-pay');
        $this->has_fields = false;
        $this->supports = ['products'];
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');

        $this->isTest = $this->get_option( 'environment' );
        $this->debug = $this->get_option( 'debug' );

        if ($this->isTest){
            $this->public_key  = $this->get_option( 'sandbox_public_key' );
            $this->secure_key  = $this->get_option( 'sandbox_secure_key' );
            $this->end_point = $this->get_option( 'sandbox_end_point' );
        }else{
            $this->public_key  = $this->get_option( 'public_key' );
            $this->secure_key  = $this->get_option( 'secure_key' );
            $this->end_point = $this->get_option( 'end_point' );
        }

        $this->handle_payment_method = $this->get_option('handle_payment_method');

        if ((int)$this->get_option('expiration_time') < 30)
            $this->update_option('expiration_time', 30);

        $this->expiration_time = $this->get_option('expiration_time');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
        add_action('woocommerce_api_'.strtolower(get_class($this)), array($this, 'confirmation_ipn'));
    }

    public function is_available()
    {
        return parent::is_available() &&
            !empty($this->public_key) &&
            !empty($this->secure_key) &&
            !empty($this->end_point);
    }

    public function init_form_fields()
    {
        $this->form_fields = require( dirname( __FILE__ ) . '/admin/settings.php' );
    }

    public function admin_options()
    {
        ?>
        <h3><?php echo $this->title; ?></h3>
        <p><?php echo $this->method_description; ?></p>
        <table class="form-table">
            <?php $this->generate_settings_html(); ?>
        </table>
        <?php
    }

    public function process_payment($order_id)
    {
        $params = $_POST;
        $params['id_order'] = $order_id;

        $payment = new Woo_Just_Pay_SMP();
        $data = $payment->doPayment($params);
        

        if($data['status']){
            wc_reduce_stock_levels($order_id);
            WC()->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $data['url']
            );
        }else{
            wc_add_notice($data['message'], 'error' );
        }

        return parent::process_payment($order_id);
    }

    public function receipt_page($order_id)
    {
        ?>
        <a
                class="iframe-lightbox-link" id="iframe-just-pay"
                href="<?php echo $_GET['url_just_pay']; ?>"><?php __('Pay with Just Pay', 'woo-just-pay') ?>
        </a>
        <?php
    }

    public function confirmation_ipn()
    {
        if (!$_REQUEST['amount'] ||
            !$_REQUEST['channel'] ||
            !$_REQUEST['currency'] ||
            !$_REQUEST['signature'] ||
            !$_REQUEST['time'] ||
            !$_REQUEST['trans_ID'])
            return;

        $amount = $_REQUEST['amount'];
        $time = $_REQUEST['time'];
        $currency = $_REQUEST['currency'];
        $trans_id = $_REQUEST['trans_ID'];
        $channel = $_REQUEST['channel'];
        $confirm_transid = $trans_id;

        $data_sign = "$this->public_key$time$channel$amount$currency$trans_id$this->secure_key";
        $signature = hash('sha256', $data_sign);

        $response_confirm = "$this->public_key,$time,$channel,$amount,$currency,$trans_id,$confirm_transid,$signature";

        $trans_id = explode('_', $trans_id);
        $order_id = $trans_id[0];

        $order = new WC_Order($order_id);

        $order->payment_complete($trans_id);
        $order->add_order_note(sprintf(__('Successful payment (Transaction ID: %s)',
            'woo-just-pay'), $trans_id));

        die($response_confirm);

    }

}