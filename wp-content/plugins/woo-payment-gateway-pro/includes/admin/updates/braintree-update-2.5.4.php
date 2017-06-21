<?php
/**
 * Update for version 2.5.4. This version consists of a complete code rewrite. As such, it is necessary to
 * convert some of the settings to the new format.
 */

/**
 * ****** WooCommerce Options *******
 */
if ( bt_manager()->get_option( 'checkout_form' ) === 'custom_form' ) {

} else {
	if ( bt_manager()->is_active( 'custom_form' ) ) {
		bt_manager()->set_option( 'checkout_form', 'custom_form' );
	} else {
		bt_manager()->set_option( 'checkout_form', 'dropin_form' );
	}
	unset( bt_manager()->settings [ 'custom_form' ] );
}

// Unset order status. This will force users to use the WC default status or
// re-configure the value.
unset( bt_manager()->settings [ 'order_status' ] );

$environments = array (
		'sandbox', 
		'production' 
);

// Convert merchant accounts.
foreach ( braintree_get_currencies() as $currency => $v ) {
	
	foreach ( $environments as $environment ) {
		
		$key = "woocommerce_braintree_{$environment}_merchant_account_id[{$currency}]";
		$merchant_account = isset( bt_manager()->settings [ $key ] ) ? bt_manager()->settings [ $key ] : '';
		
		if ( ! empty( $merchant_account ) ) {
			$new_value = bt_manager()->get_option( "woocommerce_braintree_{$environment}_merchant_account_id" );
			if ( ! is_array( $new_value ) ) {
				$new_value = array ();
			}
			$new_value [ $currency ] = $merchant_account;
			bt_manager()->set_option( "woocommerce_braintree_{$environment}_merchant_account_id", $new_value );
			unset( bt_manager()->settings [ $key ] );
		}
	}
}

$payment_methods = bt_manager()->get_option( 'payment_methods' );
if ( ! empty( $payment_methods ) ) {
	if ( is_array( $payment_methods ) ) {
		$new_methods = array ();
		foreach ( $payment_methods as $k => $v ) {
			if ( ! empty( $v ) && ! is_numeric( $k ) ) {
				$new_methods [] = $k;
			}
		}
		if ( ! empty( $new_methods ) ) {
			bt_manager()->set_option( 'payment_methods', $new_methods );
		}
	}
}

/* convert all braintree subscription values to new key */
global $wpdb;

$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta as postmeta INNER JOIN $wpdb->posts AS posts ON posts.ID = postmeta.post_id SET meta_key = %s WHERE posts.post_type IN ('product', 'product_variation') AND meta_key = %s", '_braintree_subscription', 'braintree_subscription' ) );

/**
 * ****** Donation Options *******
 */

$options = array (
		'donation_text_exempt' => 'donation_tax_exempt' 
);

/* convert old options to the new options */
foreach ( $options as $old_option => $new_option ) {
	bt_manager()->set_option( $new_option, bt_manager()->get_option( $old_option ) );
	
	unset( bt_manager()->settings [ $old_option ] );
}

$donation_fields = array ();
if ( bt_manager()->is_active( 'donation_address' ) ) {
	$donation_fields [] = 'billing_address_1';
}
if ( bt_manager()->is_active( 'donation_email' ) ) {
	$donation_fields [] = 'email_address';
}
if ( bt_manager()->is_active( 'donation_name' ) ) {
	$donation_fields [] = 'billing_first_name';
	$donation_fields [] = 'billing_last_name';
}
if ( $donation_fields ) {
	bt_manager()->settings [ 'donation_fields' ] = $donation_fields;
}
unset( bt_manager()->settings [ 'donation_address' ] );
unset( bt_manager()->settings [ 'donation_email' ] );
unset( bt_manager()->settings [ 'donation_name' ] );

$donation_payment_methods = bt_manager()->get_option( 'donation_payment_methods' );
if ( ! empty( $donation_payment_methods ) ) {
	if ( is_array( $donation_payment_methods ) ) {
		$new_methods = array ();
		foreach ( $donation_payment_methods as $k => $v ) {
			if ( ! empty( $v ) && ! is_numeric( $k ) ) {
				$new_methods [] = $k;
			}
		}
		if ( ! empty( $new_methods ) ) {
			bt_manager()->set_option( 'donation_payment_methods', $new_methods );
		}
	}
}

/**
 * *** Save Data ****
 */
bt_manager()->update_settings();

/**
 * *** Subscription Plan Data ****
 */
$products = get_posts( array (
		'post_status' => 'publish', 
		'post_type' => array (
				'product', 
				'product_variation' 
		), 
		'posts_per_page' => - 1, 
		'meta_key' => 'braintree_plans' 
) );

// Fetch the plans for both environments and compare to the database plans. That will show show which environment they are for.
$braintree_production_plans = array ();
$braintree_sandbox_plans = array ();
try {
	Braintree_Configuration::environment( 'production' );
	Braintree_Configuration::merchantId( bt_manager()->get_option( "production_merchant_id" ) );
	Braintree_Configuration::privateKey( bt_manager()->get_option( "production_private_key" ) );
	Braintree_Configuration::publicKey( bt_manager()->get_option( "production_public_key" ) );
	
	$braintree_production_plans = Braintree_Plan::all();
} catch( \Braintree\Exception $e ) {
}
try {
	Braintree_Configuration::environment( 'sandbox' );
	Braintree_Configuration::merchantId( bt_manager()->get_option( "sandbox_merchant_id" ) );
	Braintree_Configuration::privateKey( bt_manager()->get_option( "sandbox_private_key" ) );
	Braintree_Configuration::publicKey( bt_manager()->get_option( "sandbox_public_key" ) );
	
	$braintree_sandbox_plans = Braintree_Plan::all();
} catch( \Braintree\Exception $e ) {
}

$skip_products = array ();

foreach ( $braintree_production_plans as $prod_plan ) {
	
	foreach ( $products as $product ) {
		$plans = get_post_meta( $product->ID, 'braintree_plans', true );
		
		if ( in_array( $prod_plan->id, $plans ) ) {
			// These are production plans so save as production.
			update_post_meta( $product->ID, '_braintree_production_plans', $plans );
			$skip_products [] = $product->ID;
		}
	}
}
foreach ( $braintree_sandbox_plans as $sand_plan ) {
	
	foreach ( $products as $product ) {
		// product was already updated as production so skip.
		if ( in_array( $product->ID, $skip_products ) ) {
			continue;
		}
		$plans = get_post_meta( $product->ID, 'braintree_plans', true );
		
		if ( in_array( $sand_plan->id, $plans ) ) {
			// These are sandbox plans so save as production.
			update_post_meta( $product->ID, '_braintree_sandbox_plans', $plans );
			delete_post_meta( $product->ID, 'braintree_plans', true );
			$skip_products [] = $product->ID;
		}
	}
}

// delete the switch and solo payment method types

// add new endpoints.
if ( ! class_exists( 'Braintree_Gateway_WC_Query' ) ) {
	include_once bt_manager()->plugin_include_path() . 'class-wc-query.php';
}
$query = new Braintree_Gateway_WC_Query();
$query->add_endpoints();

// reqrite rules for webhooks
add_rewrite_rule( 'braintreegateway/webhooks/notifications', 'index.php?rest_route=/braintree-gateway/v1/webhooks' );

flush_rewrite_rules();