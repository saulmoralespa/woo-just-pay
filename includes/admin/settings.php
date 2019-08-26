<?php

wc_enqueue_js( "
    jQuery( function( $ ) {
	
	let just_pay_live = '#woocommerce_woo_just_pay_smp_public_key, #woocommerce_woo_just_pay_smp_secure_key, #woocommerce_woo_just_pay_smp_end_point';
	
	let just_pay_sandbox = '#woocommerce_woo_just_pay_smp_sandbox_public_key, #woocommerce_woo_just_pay_smp_sandbox_secure_key, #woocommerce_woo_just_pay_smp_sandbox_end_point';
	
	
	$( '#woocommerce_woo_just_pay_smp_environment' ).change(function(){
		
		$( just_pay_sandbox + ',' + just_pay_live ).closest( 'tr' ).hide();	
	
		
		if ( '0' === $( this ).val() ) {
		    $( '#woocommerce_woo_just_pay_smp_api, #woocommerce_woo_just_pay_smp_api + p' ).show();
			$( '#woocommerce_woo_just_pay_smp_sandbox_api, #woocommerce_woo_just_pay_smp_sandbox_api + p' ).hide();
			$( just_pay_live ).closest( 'tr' ).show();
			
		}else{
		   $( '#woocommerce_woo_just_pay_smp_api, #woocommerce_woo_just_pay_smp_api + p' ).hide();
		   $( '#woocommerce_woo_just_pay_smp_sandbox_api, #woocommerce_woo_just_pay_smp_sandbox_api + p' ).show();
	   	   $( just_pay_sandbox ).closest( 'tr' ).show();
	
		}
	}).change();
});	
");




return array(
    'enabled' => array(
        'title' => __('Enable/Disable', 'woo-just-pay'),
        'type' => 'checkbox',
        'label' => __('Enable Just Pay', 'woo-just-pay'),
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'woo-just-pay'),
        'type' => 'text',
        'description' => __('It corresponds to the title that the user sees during the checkout', 'woo-just-pay'),
        'default' => __('Woo Just Pay Online', 'woo-just-pay'),
        'desc_tip' => true
    ),
    'description' => array(
        'title' => __('Description', 'woo-just-pay'),
        'type' => 'textarea',
        'description' => __('It corresponds to the description that the user will see during the checkout', 'woo-just-pay'),
        'default' => __('Just Pay Online payment method', 'woo-just-pay'),
        'desc_tip' => true
    ),
    'debug' => array(
        'title' => __('Debug', 'woo-just-pay'),
        'type' => 'checkbox',
        'label' => __('Debug records, it is saved in payment log', 'woo-just-pay'),
        'default' => 'no'
    ),
    'ipn' => array(
        'title' => __( 'Notify URL', 'woo-just-pay'),
        'type' => 'title',
        'description' => trailingslashit(get_bloginfo( 'url' )) . trailingslashit('wc-api') . strtolower(get_class($this))
    ),
    'handle_payment_method' => array(
        'title' => __('Handle payment method', 'woo-just-pay'),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __('Redirection will send the user to the Just Pay checkout, Lightbox remains the user in the store', 'woo-just-pay'),
        'desc_tip' => true,
        'default' => 'redirection',
        'options'     => array(
            'redirection'    => __( 'Redirection', 'woo-just-pay' ),
            'lightbox' => __( 'Lightbox', 'woo-just-pay' )
        )
    ),
    'expiration_time' => array(
        'title' => __('Expiration time', 'woo-just-pay'),
        'type' => 'number',
        'description' => __('The time allowed in minutes to generate transactions', 'woo-just-pay'),
        'default' => '120',
        'desc_tip' => true
    ),
    'environment' => array(
        'title' => __('Environment', 'woo-just-pay'),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __('Enable to run tests', 'woo-just-pay'),
        'desc_tip' => true,
        'default' => true,
        'options'     => array(
            false    => __( 'Production', 'woo-just-pay' ),
            true => __( 'Tests', 'woo-just-pay' )
        )
    ),
    'api'          => array(
        'title'       => __( 'Production credentials', 'woo-just-pay'),
        'type'        => 'title',
        'description' => __( 'Use the credentials of the Just Pay account   ', 'woo-just-pay' )
    ),
    'public_key' => array(
        'title' => __('public_key', 'woo-just-pay'),
        'type' => 'text',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'secure_key' => array(
        'title' => __('secure_key', 'woo-just-pay'),
        'type' => 'password',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'end_point' => array(
        'title' => __('Endpoint URL', 'woo-just-pay'),
        'type' => 'url',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'sandbox_api'          => array(
        'title'       => __( 'Sandbox credentials', 'woo-just-pay'),
        'type'        => 'title',
        'description' => __( 'Use the credentials of the Just Pay account for tests', 'woo-just-pay' )
    ),
    'sandbox_public_key' => array(
        'title' => __('public_key', 'woo-just-pay'),
        'type' => 'text',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'sandbox_secure_key' => array(
        'title' => __('secure_key', 'woo-just-pay'),
        'type' => 'password',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'sandbox_end_point' => array(
        'title' => __('Endpoint URL', 'woo-just-pay'),
        'type' => 'url',
        'description' => __('', 'woo-just-pay'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    )
);
