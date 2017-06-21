<?php
// File Security Check
if ( ! empty( $_SERVER [ 'SCRIPT_FILENAME' ] ) && basename( __FILE__ ) == basename( $_SERVER [ 'SCRIPT_FILENAME' ] ) ) {
	die( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php

/* ----------------------------------------------------------------------------------- */
/* Start WooThemes Functions - Please refrain from editing this section */
/* ----------------------------------------------------------------------------------- */

// Define the theme-specific key to be sent to PressTrends.
define( 'WOO_PRESSTRENDS_THEMEKEY', 'zdmv5lp26tfbp7jcwiw51ix9sj389e712' );

// WooFramework init
require_once ( get_template_directory() . '/functions/admin-init.php' );

/* ----------------------------------------------------------------------------------- */
/*
 * Load the theme-specific files, with support for overriding via a child theme.
 * /*-----------------------------------------------------------------------------------
 */

$includes = array (
		'includes/theme-options.php',  // Options panel settings and custom settings
		'includes/theme-functions.php',  // Custom theme functions
		'includes/theme-actions.php',  // Theme actions & user defined hooks
		'includes/theme-comments.php',  // Custom comments/pingback loop
		'includes/theme-js.php',  // Load JavaScript via wp_enqueue_script
		'includes/sidebar-init.php',  // Initialize widgetized areas
		'includes/theme-widgets.php',  // Theme widgets
		'includes/theme-install.php',  // Theme installation
		'includes/theme-woocommerce.php',  // WooCommerce options
		'includes/theme-plugin-integrations.php' 
); // Plugin integrations
   
// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'woo_includes', $includes );

foreach ( $includes as $i ) {
	locate_template( $i, true );
}

/* ----------------------------------------------------------------------------------- */
/* You can add custom functions below */
/* ----------------------------------------------------------------------------------- */

/**
 *
 * @param WC_Order $order        	
 */
function paymnt_plugins_admin_display_registered_domain( $order )
{
	global $wpdb;
	
	if ( ! defined( 'SLM_TBL_LICENSE_KEYS' ) ) {
		return;
	}
	
	$licenses_table = SLM_TBL_LICENSE_KEYS;
	$domains_table = SLM_TBL_LIC_DOMAIN;
	
	$reg_domain = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $domains_table AS domains 
			INNER JOIN $licenses_table AS licenses ON domains.lic_key = licenses.license_key WHERE licenses.txn_id = %s", $order->id ) );
	
	?>
<p class="form-field form-field-wide">
	<label><?php _e('Registered Domain')?></label>
		<?php if($reg_domain):?>
		<span><?php echo $reg_domain->registered_domain ?></span>
		<?php endif;?>
	</p>
<?php
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'paymnt_plugins_admin_display_registered_domain', 10, 1 );

function paymnt_plugins_enqueue_zendesk_widget()
{
	wp_enqueue_script( 'payment-plugins-zendesk-widget', get_template_directory_uri() . '/js/zendesk-widget.js', array (
			'jquery' 
	) );
}

add_action( 'wp_enqueue_scripts', 'paymnt_plugins_enqueue_zendesk_widget' );
/* ----------------------------------------------------------------------------------- */
/* Don't add any code below here or the sky will fall down */
/* ----------------------------------------------------------------------------------- */
?>