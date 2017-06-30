<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */

$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);





// ***** SaunaSpace Custom Functions ***************************************************************************************************************************** 


// Remove showing results in Shop
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );


// Remove default sorting dropdown in Shop
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Remove sale! sticker in Shop and on Product page
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );


// Move Price down next to add to cart in Product short description on Product Detail Pages
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );


// Display 18 products on Shop Product Loop
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 18;' ), 20 );


// Hide Related Products from Product Detail Pages
function wc_remove_related_products( $args ) {
  return array();
}
add_filter('woocommerce_related_products_args','wc_remove_related_products', 10);
 

// Remove prices from Shop Page Product Loop
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );


// Add the Edit Cart Link to checkout above Payment Box
function woocommerce_button_proceed_to_checkout() {
 $checkout_url = WC()->cart->get_checkout_url();
 ?>
 <a href="<?php echo $checkout_url; ?>" class="checkout-button button alt wc-forward"><?php _e( 'Checkout Now <i class="fa fa-lock cart-to-checkout"></i>', 'woocommerce' ); ?></a>
 <?php
} 


// Make Fedex default shipping provider in Shipment Tracking widget in Adminr Order Edit page
add_filter( 'woocommerce_shipment_tracking_default_provider', 'custom_woocommerce_shipment_tracking_default_provider' );
function custom_woocommerce_shipment_tracking_default_provider( $provider ) {
  $provider = 'fedex'; // Replace this with the name of the provider. See line 42 in the plugin for the full list.
  return $provider;
}


// Add used coupons to the ADMIN order edit page
function custom_checkout_field_display_admin_order_meta($order){
  if( $order->get_used_coupons() ) {
    $coupons_count = count( $order->get_used_coupons() );
      echo '<h4>' . __('Coupons used') . ' (' . $coupons_count . ')</h4>';
      echo '<p><strong>' . __('Coupons used') . ':</strong> ';
      $i = 1;
      foreach( $order->get_used_coupons() as $coupon) {
        echo $coupon;
        if( $i < $coupons_count )
          echo ', ';
        $i++;
      }
      echo '</p>';
  }
}


// Add 50x50px product image to all WC emails
function sww_add_wc_order_email_images( $table, $order ) {
  ob_start();
  $template = $plain_text ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php';
  wc_get_template( $template, array(
    'order'                 => $order,
    'items'                 => $order->get_items(),
    'show_download_links'   => $show_download_links,
    'show_sku'              => $show_sku,
    'show_purchase_note'    => $show_purchase_note,
    'show_image'            => true,
    'image_size'            => array( 50, 50 )
  ) );
  return ob_get_clean();
}
add_filter( 'woocommerce_email_order_items_table', 'sww_add_wc_order_email_images', 10, 2 );


// Add 40x40px product image to print invoice
function example_product_image( $product ) {
  if( isset( $product->id ) && has_post_thumbnail( $product->id ) ) {
    echo get_the_post_thumbnail( $product->id, array( 40, 40 ) );
  }
}
add_action( 'wcdn_order_item_before', 'example_product_image' );


// Change Customer Details Phone label in all WC emails
add_filter( 'woocommerce_email_customer_details_fields', 'my_function', 40, 3);
function my_function($fields, $sent_to_admin, $order ) {
  if (!$sent_to_admin) {
    $fields["customer_note"]["label"] = 'Order Notes';
    $fields["billing_phone"]["label"] = 'Phone';
  }
  return $fields;
}


// Display field shipping phone value on the Admin order edit page
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'shipping_phone_field_display_admin_order_meta', 10, 1 );
function shipping_phone_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Phone').':</strong> ' . get_post_meta( $order->id, 'shipping_phone', true ) . '</p>';
}


// Add optional Shipping Phone input field + selective change of Labels on Checkout
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
  $fields['billing']['billing_email']['placeholder'] = 'email address';
  $fields['billing']['billing_phone']['placeholder'] = 'billing phone';
  $fields['order']['order_comments']['placeholder'] = 'Request signature-required delivery or other special requests.';
  /* $fields['order']['order_comments']['custom_attributes'] = array(
    'cols' => 5, 
    'rows' => 4); */
  $fields['shipping']['shipping_phone'] = array(
        'label'       => __('Phone', 'woocommerce'),
      'placeholder'   => _x('shipping phone (optional)', 'placeholder', 'woocommerce'),
      'required'    => false,
      'class'       => array('form-row-wide'),
      'clear'       => true);
  return $fields;
}


// Change Both Billing and Shipping Address Labels on Checkout
add_filter( 'woocommerce_default_address_fields', 'get_default_address_fields' );
function get_default_address_fields( $fields ) {
  $fields['city']['label'] = 'City';
  $fields['city']['placeholder'] = 'city';
  $fields['company']['placeholder'] = 'company name (optional)';
    $fields['address_1']['placeholder'] = 'street address';
    $fields['address_2']['placeholder'] = 'apt, ste, unit etc. (optional)';
  $fields['postcode']['placeholder'] = 'postal code';
  $fields['first_name']['placeholder'] = 'first name';
  $fields['last_name']['placeholder'] = 'last name';
  $fields['postcode']['label'] = 'ZIP Code';
  return $fields;
}


// Change Shipping Fields Order
add_filter("woocommerce_checkout_fields", "order_fields");
function order_fields($fields) {
    $order = array(
        "shipping_first_name",
        "shipping_last_name",
        "shipping_company",
        "shipping_phone",
        "shipping_country",
        "shipping_address_1",
        "shipping_address_2",
        "shipping_city",
        "shipping_state",
        "shipping_postcode"
    );
    foreach($order as $field)
    {
        $ordered_fields[$field] = $fields["shipping"][$field];
    }
    $fields["shipping"] = $ordered_fields;
    return $fields;
}


// Add coupon code used to New Order Email 
add_action( 'woocommerce_email_after_order_table', 'add_payment_method_to_admin_new_order', 15, 2 );

/*
//Add the Edit Cart Link to checkout above Payment Box
add_action( 'woocommerce_review_order_before_payment', 'checkout_return_to_cart_link' );
function checkout_return_to_cart_link( $checkout ) {
  echo '<div id="return_to_cart"><p>' . __('<a title="Edit Cart" href="/cart">edit cart</a>') . '</p>';
  echo '</div>';
} 
*/

// Switch Google Product Feed Plugin's Product Description to pull from Short Description
function lw_woocommerce_gpf_description( $description, $ID ) {    
  global $post;
  $save_post = $post;
  $post = get_post( $ID );
  if ( ! empty( $post->post_parent ) ) {
      $post = get_post( $post->post_parent );
  }
  setup_postdata( $post );
  $excerpt = get_the_excerpt();
  $post = $save_post;
  return $excerpt;
}
add_filter( 'woocommerce_gpf_description', 'lw_woocommerce_gpf_description', 10, 2 );


// Display Regular/Sale Price in the Cart Table
add_filter( 'woocommerce_cart_item_price', 'bbloomer_change_cart_table_price_display', 30, 3 );
 
function bbloomer_change_cart_table_price_display( $price, $values, $cart_item_key ) {
$slashed_price = $values['data']->get_price_html();
$is_on_sale = $values['data']->is_on_sale();
if ( $is_on_sale ) {
 $price = $slashed_price;
}
return $price;
}


// Display Total Discount Amount / Total Savings @ Cart & Checkout
function bbloomer_wc_discount_total() {
 
    global $woocommerce;
      
    $discount_total = 0;
      
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values) {
          
    $_product = $values['data'];
  
        if ( $_product->is_on_sale() ) {
        $discount = ($_product->regular_price - $_product->sale_price) * $values['quantity'];
        $discount_total += $discount;
        }
  
    }
             
    if ( $discount_total > 0 ) {
    echo '<tr class="cart-discount">
    <th>'. __( 'You Saved', 'woocommerce' ) .'</th>
    <td data-title=" '. __( 'You Saved', 'woocommerce' ) .' ">'
    . wc_price( $discount_total + $woocommerce->cart->discount_cart ) .'</td>
    </tr>';
    }
 
}
 
// Hook our values to the Basket and Checkout pages
 
add_action( 'woocommerce_cart_totals_after_order_total', 'bbloomer_wc_discount_total', 99);
add_action( 'woocommerce_review_order_after_order_total', 'bbloomer_wc_discount_total', 99);

remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_action('woocommerce_after_checkout_form', 'woocommerce_checkout_coupon_form');




//Move Star Rating down below Add to Cart Button on Product pages
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 40 );


// Change Shipping Fields Order - Still Broken
/*
add_filter("woocommerce_checkout_fields", "order_fields");
function order_fields($fields) {
    $order = array(
        "shipping_first_name",
        "shipping_last_name",
        "shipping_company",
        "shipping_phone",
        "shipping_country",
        "shipping_address_1",
        "shipping_address_2",
        "shipping_city",
        "shipping_state",
        "shipping_postcode"
    );
    foreach($order as $field)
    {
        $ordered_fields[$field] = $fields["shipping"][$field];
    }
    $fields["shipping"] = $ordered_fields;
    return $fields;
}
*/



// Change Customer Details Phone label in all WC emails - Still Broken
/*add_filter( 'woocommerce_email_customer_details_fields', 'my_function', 40, 3);
function my_function($fields, $sent_to_admin, $order ) {
  if (!$sent_to_admin) {
    $fields["customer_note"]["label"] = 'Order Notes';
    $fields["billing_phone"]["label"] = 'Phone';
  }
  return $fields;
}*/
