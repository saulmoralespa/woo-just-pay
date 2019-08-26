<?php
/*
Plugin Name: Woo Just Pay
Description: Just pay pasarela de pago de Chile
Version: 1.0.0
Author: Saul Morales Pacheco
Author URI: https://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: woo-just-pay
Domain Path: /languages/
WC tested up to: 3.5
WC requires at least: 2.6
*/

if (!defined( 'ABSPATH' )) exit;

if(!defined('WOO_JUST_PAY_WJP_SMP_VERSION')){
    define('WOO_JUST_PAY_WJP_SMP_VERSION', '1.0.0');
}

if(!defined('WOO_JUST_PAY_WJP_SMP_NAME')){
    define('WOO_JUST_PAY_WJP_SMP_NAME', 'woo payu latam sdk');
}

add_action('plugins_loaded','woo_just_pay_smp_init', 0);

function woo_just_pay_smp_init(){

    load_plugin_textdomain('woo-just-pay', FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    if (!requeriments_woo_just_pay_smp())
        return;

    woo_just_pay_smp()->just_pay_run();
}

function woo_just_pay_smp_notices( $notice ) {
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function requeriments_woo_just_pay_smp(){

    if ( version_compare( '7.1.0', PHP_VERSION, '>' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $php = __('Woo Just Pay: Requires php version 7.1.0 or higher', 'woo-just-pay');
            add_action('admin_notices', function() use($php) {
                woo_just_pay_smp_notices($php);
            });
        }
        return false;
    }

    if ( !in_array(
        'woocommerce/woocommerce.php',
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
        true
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $woo = __( 'Woo Just Pay: Woocommerce must be installed and active.', 'woo-just-pay' );
            add_action('admin_notices', function() use($woo) {
                woo_just_pay_smp_notices($woo);
            });
        }
        return false;
    }

    $shop_currency = get_option('woocommerce_currency');

    if (!in_array($shop_currency, ['CLP', 'PEN', 'USD'])){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $currency = __('Woo Just Pay: Requires the currencies CLP, PEN or USD ',
                    'woo-just-pay' )  .
                sprintf(__('%s', 'woo-just-pay' ), '<a href="' . admin_url() .
                    'admin.php?page=wc-settings&tab=general#s2id_woocommerce_currency">' .
                    __('Click here to configure', 'woo-just-pay') . '</a>' );
            add_action('admin_notices', function() use($currency) {
                woo_just_pay_smp_notices($currency);
            });
        }
        return false;
    }

    return true;
}

function woo_just_pay_smp(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-woo-just-pay-plugin.php');
        $plugin = new Woo_Just_Pay_SMP_Plugin(__FILE__, WOO_JUST_PAY_WJP_SMP_VERSION, WOO_JUST_PAY_WJP_SMP_NAME);
    }
    return $plugin;
}