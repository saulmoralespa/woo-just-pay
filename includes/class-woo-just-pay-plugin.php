<?php


class Woo_Just_Pay_SMP_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var WC_Logger
     */
    public $logger;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version, $name)
    {
        $this->file = $file;
        $this->version = $version;
        $this->name = $name;

        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->lib_path = $this->plugin_path . trailingslashit( 'lib' );
        $this->logger = new WC_Logger();
    }

    public function just_pay_run()
    {
        try{
            if ($this->_bootstrapped)
                throw new Exception( __( 'Woo Just Pay can only be called once', 'woo-just-pay'));
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                add_action('admin_notices', function() use($e) {
                    woo_just_pay_smp_notices($e->getMessage());
                });
            }
        }
    }

    protected function _run()
    {
        require_once ($this->includes_path . 'class-gateway-woo-just-pay.php');
        require_once ($this->includes_path . 'class-gateway-woo-just-pay-cash.php');
        require_once ($this->includes_path . 'class-gateway-woo-just-pay-cards.php');
        require_once ($this->includes_path . 'class-woo-just-pay.php');
        add_filter( 'plugin_action_links_' . plugin_basename( $this->file), array( $this, 'plugin_action_links' ) );
        add_filter( 'woocommerce_payment_gateways', array($this, 'woocommerce_woo_just_pay_add_gateway'));
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp', array($this, 'return_params_just_pay'));
    }

    public function plugin_action_links($links)
    {
        $plugin_links = [];
        $plugin_links[] = '<a href="'.admin_url( 'admin.php?page=wc-settings&tab=checkout&section=woo_just_pay_smp').'">' .
            esc_html__( 'Settings', 'woo-just-pay' ) . '</a>';
        $plugin_links[] = '<a href="'.admin_url( 'admin.php?page=wc-settings&tab=checkout&section=woo_just_pay_cash_smp').'">' .
            esc_html__( 'Just Pay Cash Settings', 'woo-just-pay' ) . '</a>';
        $plugin_links[] = '<a href="'.admin_url( 'admin.php?page=wc-settings&tab=checkout&section=woo_just_pay_cards_smp').'">' .
            esc_html__( 'Just Pay Cards Settings', 'woo-just-pay' ) . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function woocommerce_woo_just_pay_add_gateway($methods)
    {
        $methods[] = 'WC_Payment_Woo_Just_Pay_SMP';
        $methods[] = 'WC_Payment_Woo_Just_Pay_Cash_SMP';
        $methods[] = 'WC_Payment_Woo_Just_Pay_Cards_SMP';
        return $methods;
    }

    public function log($message = '')
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);

        $this->logger->add('woo-just-pay', $message);
    }

    public function enqueue_scripts()
    {
        if(is_checkout() && is_wc_endpoint_url( 'order-pay' )){
            wp_enqueue_script( 'just-pay-lightbox', $this->plugin_url . 'assets/js/iframe-lightbox.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( 'just-pay-iframe-lightbox', $this->plugin_url . 'assets/js/lightbox.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_style('just-pay-lightbox', $this->plugin_url . 'assets/css/iframe-lightbox.min.css', array(), $this->version, null);
        }
    }

    public function return_params_just_pay()
    {
        if (!isset($_REQUEST['just_pay_order_id']))
            return;

        $order_id = $_REQUEST['just_pay_order_id'];

        $order = new WC_Order($order_id);

        if (isset($_REQUEST['status']))
            $order->update_status('failed');

        wp_redirect( $order->get_checkout_order_received_url() );
    }
}