<?php
/**
 * Email Styles
 *
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load colours
$bg              = get_option( 'woocommerce_email_background_color' );
$body            = get_option( 'woocommerce_email_body_background_color' );
$base            = get_option( 'woocommerce_email_base_color' );
$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'woocommerce_email_text_color' );

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
?>
#wrapper {
    background-color: <?php echo esc_attr( $bg ); ?>;
    margin: 0;
    padding: 20px 0 10px 0;
    -webkit-text-size-adjust: none !important;
    width: 100%;
}

#template_container {
    background-color: <?php echo esc_attr( $body ); ?>;
    border-radius: 0px !important;
}

#template_header {
    
    border-radius: 0px 0px 0px 0px!important;
    color: <?php echo esc_attr( $base_text ); ?>;
    border-bottom: 1px solid #f13423;
    font-weight: 300;
    line-height: 100%;
    vertical-align: middle;
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
}

#template_header h1 {
    color: #232323;
}

#template_header_image p{
    margin-bottom:  0
}

#template_footer td {
    padding: 0;
    -webkit-border-radius: 6px;
}

#template_footer #credit {
    border:0;
    color: <?php echo esc_attr( $base_lighter_40 ); ?>;
    font-family: Arial;
    font-size:12px;
    line-height:125%;
    text-align:center;
    padding: 0 48px 48px 48px;
}

#body_content {
    background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content table td {
    padding: 32px 0 0 0;
}

#body_content table td td {
    padding: 12px;
}

#body_content table td th {
    padding: 12px 12px 0px 12px;
}

p {
    margin: 0 0 16px;
    text-align: center;
    font-weight: 300;
}

#body_content_inner {
    color: #505050;
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
    font-size: 16px;
    line-height: 150%;
    }

#body_content_inner ul {
    margin: 0;
    list-style-type: none;
    padding-left: 12px;
}

thead, th.td{
    text-transform: uppercase;
    font-weight: 300;
}

table.block td {
    padding-right: 8px;
}
table.td{
    margin-bottom: 10px;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    border-top: 0;
    font-weight: 300;
    color: rgba(0, 0, 0, 0.75);
}

table#addresses {
    padding-left: 12px;
}
th.tracking-provider {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
th.tracking-number {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
th.date-shipped {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
th.order-actions {
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
td.tracking-provider {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
td.tracking-number {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
td.date-shipped {
    font-weight: 300;
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}
td.order-actions {
    border-bottom: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}

th.td, td.td {
    border-right: 0;
    border-bottom: 1px solid #e5e5e5;
    border-top: 0;
    border-left: 0;
}

tfoot th {
    vertical-align: top;
}

table.td tbody tr td {
    border-left: 1px solid #e5e5e5;
}
table.td tbody tr td:nth-child(3) {
    border-right: 1px solid #e5e5e5;
}

table.td tfoot tr th {
    border-left: 1px solid #e5e5e5;
}
table.td tfoot tr td {
    border-right: 1px solid #e5e5e5;
    border-left: 1px solid #e5e5e5;
}

th {
    color: rgba(0, 0, 0, 0.75);
}
p.text {
    text-align: left;
}

table#template_body {
    margin-bottom: 10px;
    border-bottom: 1px solid #f13423;
}
.text {
    color: rgba(0, 0, 0, 0.75);
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
}

.link {
    color: <?php echo esc_attr( $base ); ?>;
}

.block {
  display:inline-block;
    min-width: 240px;
    width: 48%;
    vertical-align: top;
}

.block td {
  color: rgba(0, 0, 0, 0.75);
font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
margin: 0 0 16px;
}

#header_wrapper {
    padding: 10px 48px;
    display: block;
}

h1 {
    color: <?php echo esc_attr( $base ); ?>;
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
    font-size: 36px;
    font-weight: 300;
    line-height: 130%;
    margin: 0;
    text-align: center;
    text-transform: uppercase;
    -webkit-font-smoothing: antialiased;
}

h2 {
    color: rgba(0, 0, 0, 0.75);
    display: block;
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
    font-size: 28px;
    font-weight: 300;
    line-height: 130%;
    margin: 16px 0 8px;
    text-align: center;
    text-transform: uppercase;
}

h2.questions {
    font-size: 28px;
    color: rgba(0, 0, 0, 0.75);
}

h3.questions {
    margin-top: 0;

}

h3 {
    color: rgba(0, 0, 0, 0.75);
    display: block;
    font-family: "Open Sans", Helvetica, Roboto, Arial, sans-serif;
    font-size: 18px;
    font-weight: 500;
    line-height: 130%;
    margin: 16px 0 8px;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
    color: #f13423;
    font-weight: normal;
    text-decoration: none;
}

a.pay-link {
    color: #fff;
    padding: 5px 11px;
    font-weight: 300;
    font-size: 20px;
    border: 1px solid #f13423;
    background: #f13423;
}

p.questions{
    text-align: left;
}

img {
    border: none;
    display: inline;
    font-size: 14px;
    font-weight: bold;
    height: auto;
    line-height: 100%;
    outline: none;
    text-decoration: none;
    text-transform: capitalize;
}
strong {
    font-weight: 500;
    font-size: 16px;
    color: rgba(0, 0, 0, 0.75);
}
.update {
    font-weight: 500;
}
table.social {
    border-top:1px solid #f13423; 
}
table.contact {
    background: #fff;
    padding-bottom: 0;
}
td.contact {
    color: rgba(0, 0, 0, 0.75);
    font-weight: 400;
    border-top: 1px solid #f13423;
}
table.contact a {
    color: rgba(0, 0, 0, 0.75);
    font-weight: 400;
}
small {
    text-align: left;
}
td.td div {
    display: inline;
}
#body_content_inner img {
    float: right;
}
img.logo {
    max-width: 60px;
    padding-top: 16px;
}
<?php
