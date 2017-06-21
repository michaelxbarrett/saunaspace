<?php
global $woocommerce, $wpdb, $table_prefix;

add_action( 'wp_ajax_nopriv_myAjaxeasy', 'myAjaxeasy' );
add_action( 'wp_ajax_nopriv_myAjaxseasy', 'myAjaxseasy' );
add_action( 'wp_ajax_myAjaxseasy', 'myAjaxseasy' );
add_action( 'wp_ajax_myAjaxeasy', 'myAjaxeasy' );
add_action( 'wp_ajax_nopriv_getrates', 'getrates' );
add_action( 'wp_ajax_getrates', 'getrates' );
add_action( 'wp_ajax_nopriv_buylabel', 'buylabel' );
add_action( 'wp_ajax_buylabel', 'buylabel' );
add_action( 'wp_ajax_nopriv_labelinfo', 'labelinfo' );
add_action( 'wp_ajax_labelinfo', 'labelinfo' );
add_action( 'wp_ajax_nopriv_labelpackageinfo', 'labelpackageinfo' );
add_action( 'wp_ajax_labelpackageinfo', 'labelpackageinfo' );
add_action( 'wp_ajax_nopriv_insurepackage', 'insurepackage' );
add_action( 'wp_ajax_insurepackage', 'insurepackage' );
add_action( 'wp_ajax_nopriv_myAjaxs', 'myAjaxs' );
add_action( 'wp_ajax_myAjaxs', 'myAjaxs' );

require_once('lib/easypost.php');
$woocommerce_easypost_test = get_option( 'pvit_easypostwanderlust_shipper_test' );
$woocommerce_easypost_test_api_key = get_option( 'pvit_easypostwanderlust_testkey' );
$woocommerce_easypost_live_api_key = get_option( 'pvit_easypostwanderlust_livekey' );

	if ($woocommerce_easypost_test =='1') { 
		\EasyPost\EasyPost::setApiKey($woocommerce_easypost_test_api_key);
	} else {
		\EasyPost\EasyPost::setApiKey($woocommerce_easypost_live_api_key);
	} 

 
require_once('mod/get-rates-backend.php');
require_once('mod/generate-label.php');
require_once('mod/auto-generate.php');
require_once('mod/email_label.php');
require_once('mod/order-package-info.php');

//add_filter( 'woocommerce_thankyou', 'purchase_order', 10, 2 );
//add_action( 'woocommerce_order_status_pending', 'mysite_pending');
//add_action( 'woocommerce_order_status_failed', 'mysite_failed');
//add_action( 'woocommerce_order_status_on-hold', 'mysite_hold');
//add_action( 'woocommerce_order_status_processing', 'mysite_processing');
//add_action( 'woocommerce_order_status_completed', 'mysite_completed');
//add_action( 'woocommerce_order_status_refunded', 'mysite_refunded');
//add_action( 'woocommerce_order_status_cancelled', 'mysite_cancelled');
//add_action( 'woocommerce_payment_complete', 'mysite_woocommerce_payment_complete' );

$woocommerce_easypost_autogen = get_option( 'pvit_easypostwanderlust_autogen' ); //check if auto label is enabled
if($woocommerce_easypost_autogen == 1){
	add_action( 'woocommerce_order_status_processing', 'purchase_order');
}
 

function myAjaxeasy(){global $wpdb;$update = $_POST['updatess'];$bodytag = str_replace("\'", "'", $update);$update2  = $wpdb->query($bodytag);$results = "Saved";$results = '<META HTTP-EQUIV="refresh" CONTENT="1">';die($results);}
function myAjaxseasy(){global $wpdb;$update = $_POST['updatess'];$bodytag = str_replace("\'", "'", $update);$update2  = $wpdb->query($bodytag);$results = "Saved";die($results);}
function myAjaxs(){global $wpdb;$update = $_POST['updatess'];$bodytag = str_replace("\'", "'", $update);$update2  = $wpdb->query($bodytag);$results = "Saved";$results = '<META HTTP-EQUIV="refresh" CONTENT="1">';die($results);}



