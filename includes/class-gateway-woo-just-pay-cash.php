<?php


class WC_Payment_Woo_Just_Pay_Cash_SMP extends WC_Payment_Gateway
{
    public function __construct()
    {
        $shop_currency = get_option('woocommerce_currency');
        $this->id = 'woo_just_pay_cash_smp';
        $this->icon = woo_just_pay_smp()->plugin_url . "assets/images/cash-$shop_currency.png";
        $this->method_title = __('Just Pay Cash', 'woo-just-pay');
        $this->method_description = __('Just Pay cash payment method', 'woo-just-pay');
        $this->description  = $this->get_option( 'description' );
        $this->order_button_text = __('to pay', 'woo-just-pay');
        $this->has_fields = false;
        $this->supports = ['products'];
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->handle_payment_method = $this->get_option('handle_payment_method');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
    }

    public function is_available()
    {
        $just_pay = new WC_Payment_Woo_Just_Pay_SMP();

        return parent::is_available() && $just_pay->is_available();
    }

    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Enable/Disable', 'woo-just-pay'),
                'type' => 'checkbox',
                'label' => __('Just Pay Cash', 'woo-just-pay'),
                'default' => 'no'
            ],
            'title' => [
                'title' => __('Title', 'woo-just-pay'),
                'type' => 'text',
                'description' => __('It corresponds to the title that the user sees during the checkout', 'woo-just-pay'),
                'default' => __('Just Pay Cash', 'woo-just-pay'),
                'desc_tip' => true
            ],
            'description' => [
                'title' => __('Description', 'Just Pay Cash'),
                'type' => 'textarea',
                'description' => __('It corresponds to the description that the user will see during the checkout', 'Just Pay Cash'),
                'default' => __('Just Pay Cash', 'Just Pay Cash'),
                'desc_tip' => true
            ],
            'handle_payment_method' => [
                'title' => __('Handle payment method', 'woo-just-pay'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Redirection will send the user to the Just Pay checkout, Lightbox remains the user in the store', 'woo-just-pay'),
                'desc_tip' => true,
                'default' => 'redirection',
                'options'     => [
                    'redirection'    => __( 'Redirection', 'woo-just-pay' ),
                    'lightbox' => __( 'Lightbox', 'woo-just-pay' )
                ]
            ]
        ];
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

}