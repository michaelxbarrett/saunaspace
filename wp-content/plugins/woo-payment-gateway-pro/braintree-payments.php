<?php

/**
 * Plugin Name: Braintree For WooCommerce Pro
 * Plugin URI: https://wordpress.paymentplugins.com
 * Description: Official partner of Braintree & PayPal. Sell your WooCommerce products and subscriptions or accept donations using your Braintree Account. SAQ A compliant.
 * Version: 2.6.12
 * Author: Payment Plugins, support@paymentplugins.com
 * Author URI: https://support.paymentplugins.com
 * Text Domain: braintree-payments
 * Domain Path: /i18n/languages/
 * Tested up to: 4.8
 */

function bfwc_pro_version_check_error()
{
	$message = sprintf( __( 'Your PHP version is %s but Braintree For WooCommerce requires version 5.4+.', 'braintree-payments' ), PHP_VERSION );
	echo '<div class="notice notice-error"><p style="font-size: 16px">' . $message . '</p></div>';
}

if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
	add_action( 'admin_notices', 'bfwc_pro_version_check_error' );
	return;
}

if ( in_array( 'woo-payment-gateway/braintree-payments.php', get_option( 'active_plugins', array () ) ) ) {
	add_action( 'admin_notices', function ()
	{
		$message = sprintf( __( 'Please deactivate Braintree For WooCommerce Free before activating Pro to prevent conflicts.', 'braintree-payments' ) );
		echo '<div class="notice notice-error"><p style="font-size: 16px">' . $message . '</p></div>';
	} );
	return;
}

define( 'BRAINTREE_GATEWAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'BRAINTREE_GATEWAY_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'BRAINTREE_LICENSE_ACTIVATION_URL', 'https://wordpress.paymentplugins.com/' );
define( 'BRAINTREE_LICENSE_VERIFICATION_KEY', 'gTys$hsjeScg63dDs35JlWqbx7h' );
define( 'BFWC_PLUGIN_NAME', plugin_basename( __FILE__ ) );
require_once ( BRAINTREE_GATEWAY_PATH . 'Braintree.php' );
require_once BRAINTREE_GATEWAY_PATH . 'includes/class-braintree-manager.php';