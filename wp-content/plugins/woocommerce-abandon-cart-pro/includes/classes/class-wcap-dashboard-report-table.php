<?php
/**
 * WCAP Dashboard report
 *
 * @package     WooCommerce Abandon Cart Plugin
 * @subpackage  Dashboard
 * @copyright   Copyright (c) 2015, Tyche Softwares
 * @since       3.5
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Wcap_Dashboard_Report_Action {
    
    function wcap_get_all_reports( $selected_data_range, $start_date, $end_date ){
        
        include_once( 'class-wcap-dashboard-report.php' );
        
        $wcap_month_total_orders_amount   = $wcap_month_recovered_cart_amount = 0;
        $wcap_month_recovered_cart_count  = $wcap_month_abandoned_cart_count  = 0;
        $ratio_of_recovered_number        = 0;
        $wcap_month_wc_orders             = 0;
        $ratio_of_recovered               = 0;
        $ratio_of_total_vs_abandoned      = 0;
        
        $orders = new Wcap_Dashboard_Report();
        
        $wcap_month_total_orders_amount   = $orders->get_this_month_amount_reports( 'wc_total_sales' , $selected_data_range, $start_date, $end_date );
        $wcap_month_recovered_cart_amount = $orders->get_this_month_amount_reports( 'recover'        , $selected_data_range, $start_date, $end_date );
        
        // if total order amount goes less than zero, then set it to 0.
        if ( $wcap_month_total_orders_amount < 0 ){
            
            $wcap_month_total_orders_amount = 0 ;
        }
        if ( $wcap_month_recovered_cart_amount > 0 && $wcap_month_total_orders_amount > 0 ){
            $ratio_of_recovered            = ( $wcap_month_recovered_cart_amount / $wcap_month_total_orders_amount ) * 100;
            $ratio_of_recovered            = round( $ratio_of_recovered, wc_get_price_decimals() );
        }
        
        $wcap_month_abandoned_cart_count   = $orders->get_this_month_number_reports( 'abandoned', $selected_data_range, $start_date, $end_date  );
        $wcap_month_recovered_cart_count   = $orders->get_this_month_number_reports( 'recover'  , $selected_data_range, $start_date, $end_date );
        
        if ( $wcap_month_recovered_cart_count > 0 && $wcap_month_abandoned_cart_count > 0 ){
            $ratio_of_recovered_number     = ( $wcap_month_recovered_cart_count / $wcap_month_abandoned_cart_count ) * 100;
            $ratio_of_recovered_number     = round( $ratio_of_recovered_number, wc_get_price_decimals() );
        }
        
        $wcap_month_wc_orders              = $orders->get_this_month_total_vs_abandoned_order( 'wc_total_orders', $selected_data_range, $start_date, $end_date );
        
        if ( $wcap_month_abandoned_cart_count > 0 && $wcap_month_wc_orders > 0 ){
            $ratio_of_total_vs_abandoned   = ( $wcap_month_abandoned_cart_count / $wcap_month_wc_orders  ) * 100;
            $ratio_of_total_vs_abandoned   = round( $ratio_of_total_vs_abandoned, wc_get_price_decimals() );
        }
        
        $wcap_email_sent_count             = $orders->wcap_get_email_report( "total_sent", $selected_data_range, $start_date, $end_date );
        
        $wcap_email_opened_count           = $orders->wcap_get_email_report( "total_opened", $selected_data_range, $start_date, $end_date );
        
        $wcap_email_clicked_count          = $orders->wcap_get_email_report( "total_clicked", $selected_data_range, $start_date, $end_date );
        
        ?>
        <div class = "wrap woocommerce" id="main-div" >
            <div id = "poststuff" >
                <div class = "postbox" >
                    <div class = "inside">
                        <div class = "wcap_dashboard_report_filter">
                            <form id="wcap_report_search" method="get" >
                            <input type="hidden" name="page" value="woocommerce_ac_page" />
                                <?php 
                                $this->search_by_date();
                                ?>
                            </form>
                        </div>
                        <div>
                            <?php 
                            $this->wcap_get_total_vs_recovered_revenue   ( $wcap_month_recovered_cart_amount );
                            $this->wcap_get_abandoned_vs_recovered_number( $wcap_month_recovered_cart_count );
                            $this->wcap_get_total_vs_abandoned_number    ( $wcap_month_abandoned_cart_count );
                            ?>
                       </div>
                       
                        <div class="chart-container">
                            <div id = "abandoned_vs_recovered_amount" class="chart-placeholder abandoned_vs_recovered_amount pie-chart"> </div>
                            <div id = "abandoned_vs_recovered_cart_number" class="chart-placeholder abandoned_vs_recovered_cart_number pie-chart" > </div>
                            <div id = "total_orders_vs_abandoned_orders_number" class="chart-placeholder total_orders_vs_abandoned_orders_number pie-chart" > </div> 
                       </div>
                       
                       <div>
                            <?php 
                            $this->wcap_get_total_vs_recovered_revenue_ratio    ( $ratio_of_recovered );
                            $this->wcap_get_abandoned_vs_recovered_number_ratio ( $ratio_of_recovered_number );
                            $this->wcap_get_total_vs_abandoned_number_ratio     ( $ratio_of_total_vs_abandoned );
                            
                            wp_register_script( 'wcap-dashboard-create-report', plugins_url()  . '/woocommerce-abandon-cart-pro/assets/js/wcap_create_reports.js', array( 'jquery' ) );
                            wp_enqueue_script( 'wcap-dashboard-create-report' );
                            
                            wp_localize_script( 'wcap-dashboard-create-report', 'wcap_dashboard_create_report_params', array(
                                'this_month_total_orders_amount'   => $wcap_month_total_orders_amount,
                                'this_month_recovered_cart_amount' => $wcap_month_recovered_cart_amount,
                                'this_month_abandoned_cart_count'  => $wcap_month_abandoned_cart_count,
                                'this_month_recovered_cart_count'  => $wcap_month_recovered_cart_count,
                                'this_month_wc_orders'             => $wcap_month_wc_orders,
                                'wcap_email_sent_count'            => $wcap_email_sent_count,
                                'wcap_email_opened'                => $wcap_email_opened_count,
                                'wcap_email_clicked'               => $wcap_email_clicked_count,
                                'recovered'                        => __( 'recovered',         'woocommerce-ac' ),
                                'total_revenue'                    => __( 'Total Revenue',     'woocommerce-ac' ),
                                'recovered_revenue'                => __( 'Recovered Revenue', 'woocommerce-ac' ),
                                'abandoned_carts'                  => __( 'Abandoned Carts',   'woocommerce-ac' ),
                                'recovered_carts'                  => __( 'Recovered Carts',   'woocommerce-ac' ),
                                'total_orders'                     => __( 'Total Carts',       'woocommerce-ac' ),
                                'abandoned_orders'                 => __( 'Abandoned Carts',   'woocommerce-ac' ),
                                'email_sent'                       => __( 'Emails Sent',       'woocommerce-ac' ),
                                'email_opened'                     => __( 'Emails Opened',     'woocommerce-ac' ),
                                'email_not_opened'                 => __( 'Emails Not Opened', 'woocommerce-ac' ),
                                'click_rate'                       => __( 'Click Rate',        'woocommerce-ac' ),
                            ) );
                            ?>
                       </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class = "wrap woocommerce wcap_email_report_parent_div" id="main-div wcap_email_report_parent_div">
            <div id = "poststuff" >
                <div class = "postbox"  >
                    <div class = "inside" >
                        <div>
                            <?php 
                            $wcap_emails_clicked     = 0;
                            $wcap_email_opened_ratio = 0;
                            $this->wcap_get_total_email_sent ( $wcap_email_sent_count );
                            
                            if ( $wcap_email_opened_count > 0 && $wcap_email_sent_count > 0 ){
                                $wcap_email_opened_ratio =       ( $wcap_email_opened_count / $wcap_email_sent_count ) * 100 ;
                                $wcap_email_opened_ratio =  round( $wcap_email_opened_ratio, wc_get_price_decimals() );
                            }
                            $this->wcap_get_email_opened     ( $wcap_email_opened_ratio );
                            
                            if ( $wcap_email_clicked_count > 0 && $wcap_email_opened_count > 0 ){
                                $wcap_emails_clicked    =        ( $wcap_email_clicked_count / $wcap_email_opened_count ) * 100 ;
                                $wcap_emails_clicked    =   round( $wcap_emails_clicked, wc_get_price_decimals() );
                            }
                            $this->wcap_abandoned_email_clicked    ( $wcap_emails_clicked );
                            ?>
                       </div>
                       
                        <div class="chart-container">
                            <div id = "wcap_abandoned_email_sent" class="chart-placeholder-email wcap_abandoned_email_sent pie-chart"> </div>
                            <div id = "wcap_abandoned_email_opened" class="chart-placeholder-email wcap_abandoned_email_opened pie-chart"> </div>
                            <div id = "wcap_abandoned_email_clicked" class="chart-placeholder-email wcap_abandoned_email_clicked pie-chart"> </div>
                       </div>
                       
                       <div>
                            <?php 
                            $this->wcap_get_total_email_sent_ratio  ( $wcap_email_sent_count );
                            $this->wcap_abandoned_email_opened_ratio( $wcap_email_opened_count,  $wcap_email_sent_count );
                            $this->wcap_emails_clicked_ratio        ( $wcap_email_clicked_count, $wcap_email_opened_count );
                            ?>
                       </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <?php 
    }
    
    /*
     * All middle value of Pie
     */
    function wcap_get_total_vs_recovered_revenue ( $wcap_month_recovered_cart_amount ){
        ?>
        <div id ="abandoned_vs_recovered_amount_price" class ="abandoned_vs_recovered_amount_price" >
            <span id ="abandoned_vs_recovered_amount_span" class ="abandoned_vs_recovered_amount_span" > 
                <?php echo wc_price( $wcap_month_recovered_cart_amount ) ; ?>  
            </span>
        </div>
        <div id ="abandoned_vs_recovered_amount_price_text" class ="abandoned_vs_recovered_amount_price_text" >
            <span id ="abandoned_vs_recovered_amount_price_text_span" class ="abandoned_vs_recovered_amount_price_text_span" > 
                <?php echo "Recovered <br>Revenue"; ?>  
            </span>
        </div>
        <?php 
    }
    
    function wcap_get_abandoned_vs_recovered_number( $wcap_month_recovered_cart_count ){
        ?>
        <div id ="abandoned_vs_recovered_cart_number_div" class ="abandoned_vs_recovered_cart_number_div" >
            <span id ="abandoned_vs_recovered_cart_number_div_span" class ="abandoned_vs_recovered_cart_number_div_span" > 
                <?php echo $wcap_month_recovered_cart_count ; ?>  
            </span>
        </div>
        <div id ="abandoned_vs_recovered_cart_number_div_text" class ="abandoned_vs_recovered_cart_number_div_text" >
            <span id ="abandoned_vs_recovered_cart_number_div_text_span" class ="abandoned_vs_recovered_cart_number_div_text_span" > 
                <?php echo "Recovered <br>Carts"; ?>  
            </span>
        </div>
        <?php 
    }
    
    function wcap_get_total_vs_abandoned_number ( $wcap_month_abandoned_cart_count ) {
        ?>
        <div id ="total_orders_vs_abandoned_orders_number_div" class ="total_orders_vs_abandoned_orders_number_div" >
            <span id ="total_orders_vs_abandoned_orders_number_div_span" class ="total_orders_vs_abandoned_orders_number_div_span" > 
                <?php echo $wcap_month_abandoned_cart_count ; ?>  
            </span>
        </div>
        <div id ="total_orders_vs_abandoned_orders_number_div_text" class ="total_orders_vs_abandoned_orders_number_div_text" >
            <span id ="total_orders_vs_abandoned_orders_number_div_text_span" class ="total_orders_vs_abandoned_orders_number_div_text_span" > 
                <?php echo "Abandoned <br>Orders"; ?>
                
            </span>
        </div>
        <?php 
    }
    /*
     * All Ratio here
     */

    function wcap_get_total_vs_recovered_revenue_ratio ( $ratio_of_recovered ){
        ?>
        <div id ="abandoned_vs_recovered_amount_price_ratio" class ="abandoned_vs_recovered_amount_price_ratio">
            <span id ="abandoned_vs_recovered_amount_span_ratio" class ="abandoned_vs_recovered_amount_span_ratio"> 
                <?php echo $ratio_of_recovered. "%";  ?> 
            </span>
        </div>
        <div id ="abandoned_vs_recovered_amount_price_text_ratio" class ="abandoned_vs_recovered_amount_price_text_ratio" > 
            <span id ="abandoned_vs_recovered_amount_price_text_span_ratio" class ="abandoned_vs_recovered_amount_price_text_span_ratio"> 
                <?php echo "of Total Revenue";  ?> 
            </span>
        </div>
        <?php 
    }
    
    function wcap_get_abandoned_vs_recovered_number_ratio ( $ratio_of_recovered_number ) {
        ?>
        <div id ="abandoned_vs_recovered_cart_number_div_ratio" class ="abandoned_vs_recovered_cart_number_div_ratio">
            <span id ="abandoned_vs_recovered_cart_number_div_span_ratio" class ="abandoned_vs_recovered_cart_number_div_span_ratio"> 
                <?php echo $ratio_of_recovered_number. "%";  ?> 
            </span>
        </div>
        <div id ="abandoned_vs_recovered_cart_number_div_text_ratio" class ="abandoned_vs_recovered_cart_number_div_text_ratio" > 
            <span id ="abandoned_vs_recovered_cart_number_div_text_span_ratio" class ="abandoned_vs_recovered_cart_number_div_text_span_ratio"> 
                <?php echo "of Abandoned Carts";  ?> 
            </span>
        </div>
        <?php 
    }
    
    function wcap_get_total_vs_abandoned_number_ratio ( $ratio_of_total_vs_abandoned ){
        ?>
        <div id ="total_orders_vs_abandoned_orders_number_div_ratio" class ="total_orders_vs_abandoned_orders_number_div_ratio">
            <span id ="total_orders_vs_abandoned_orders_number_div_span_ratio" class ="total_orders_vs_abandoned_orders_number_div_span_ratio"> 
                <?php echo $ratio_of_total_vs_abandoned. "%";  ?> 
            </span>
        </div>
        <div id ="total_orders_vs_abandoned_orders_number_div_text_ratio" class ="total_orders_vs_abandoned_orders_number_div_text_ratio" > 
            <span id ="total_orders_vs_abandoned_orders_number_div_text_span_ratio" class ="total_orders_vs_abandoned_orders_number_div_text_span_ratio"> 
                <?php echo "of Total Carts";  ?> 
            </span>
        </div>
        <?php 
    }
    /*
     * Search data filter
     * 
     */
    public function search_by_date(  ) {
    
        $this->duration_range_select = array(
            
            'this_month'   => __( 'This Month'   , 'woocommerce-ac' ),
            'last_month'   => __( 'Last Month'   , 'woocommerce-ac' ),
            'this_quarter' => __( 'This Quarter' , 'woocommerce-ac' ),
            'last_quarter' => __( 'Last Quarter' , 'woocommerce-ac' ),
            'this_year'    => __( 'This Year'    , 'woocommerce-ac' ),
            'last_year'    => __( 'Last Year'    , 'woocommerce-ac' ),
            'other'        => __( 'Custom'       , 'woocommerce-ac' ),
        );
        if ( isset( $_GET['duration_select'] ) ) {
            $duration_range = $_GET['duration_select'];
        }else{
            $duration_range = "this_month";
        }
        ?>
        <div class = "main_start_end_date" id = "main_start_end_date" >
            <div class = "filter_date_drop_down" id = "filter_date_drop_down" >
                <label class="date_time_filter_label" for="date_time_filter_label" > 
                    <strong>
                        <?php _e( "Select date range:", "woocommerce-ac"); ?>
                    </strong>
                </label>
                    
                <select id=duration_select name="duration_select" >
                    <?php
                    foreach ( $this->duration_range_select as $key => $value ) {
                        $sel = "";
                        if ( $key == $duration_range ) {
                            $sel = __( "selected ", "woocommerce-ac" );
                        } 
                        echo"<option value='" . $key . "' $sel> " . __( $value,'woocommerce-ac' ) . " </option>";
                    }
                    ?>
                </select>
                <?php
                 
                $start_date_range = "";
                if ( isset( $_GET['wcap_start_date'] ) ) {
                    $start_date_range = $_GET['wcap_start_date'];
                }
                
                $end_date_range = "";
                if ( isset( $_GET['wcap_end_date'] ) ){
                    $end_date_range = $_GET['wcap_end_date'];
                }
                $start_end_date_div_show = 'block';
                if ( !isset($_GET['duration_select']) || $_GET['duration_select'] != 'other' ) {
                    $start_end_date_div_show = 'none';
                }
                ?>
                
                <div class = "wcap_start_end_date_div" id = "wcap_start_end_date_div" style="display: <?php echo $start_end_date_div_show; ?>;"  >
                    <input type="text" id="wcap_start_date" name="wcap_start_date" readonly="readonly" value="<?php echo $start_date_range; ?>" placeholder="yyyy-mm-dd"/>     
                    <input type="text" id="wcap_end_date" name="wcap_end_date" readonly="readonly" value="<?php echo $end_date_range; ?>" placeholder="yyyy-mm-dd"/>
                </div>
                <div id="wcap_submit_button" class="wcap_submit_button">
                    <?php submit_button( __( 'Go', 'woocommerce-ac' ), 'button', false, false, array('ID' => 'wcap-search-by-date-submit' ) ); ?>
                </div>
            </div>
        </div>
        
       <?php
    }
    
    function wcap_get_total_email_sent ( $wcap_sent_email_count ){
        ?>
        <div id ="wcap_sent_email_count_div" class ="wcap_sent_email_count_div" >
            <span id ="wcap_sent_email_count_div_span" class ="wcap_sent_email_count_div_span" > 
                <?php echo  $wcap_sent_email_count ; ?>  
            </span>
        </div>
        <div id ="wcap_sent_email_count_div_text" class ="wcap_sent_email_count_div_text" >
            <span id ="wcap_sent_email_count_div_text_span" class ="wcap_sent_email_count_div_text_span" > 
                <?php echo "Emails <br>Sent"; ?>  
            </span>
        </div>
        <?php 
    }
    
    function wcap_get_total_email_sent_ratio ( $wcap_sent_email_count ){
        ?>
        <div id ="wcap_sent_email_count_div_ratio" class ="wcap_sent_email_count_div_ratio" >
            <span id ="wcap_sent_email_count_div_ratio_span" class ="wcap_sent_email_count_div_ratio_span" > 
                <?php echo  $wcap_sent_email_count ; ?>  
            </span>
        </div>
        <div id ="wcap_sent_email_count_div_text_ratio" class ="wcap_sent_email_count_div_text_ratio" >
            <span id ="wcap_sent_email_count_div_text_ratio_span" class ="wcap_sent_email_count_div_text_ratio_span" > 
                <?php echo "Emails <br>Sent"; ?>  
            </span>
        </div>
        <?php 
    }
        
    function wcap_get_email_opened ( $wcap_email_opened ){
        ?>
        <div id ="wcap_email_opened_count_div" class ="wcap_email_opened_count_div" >
            <span id ="wcap_email_opened_count_div_span" class ="wcap_email_opened_count_div_span" > 
                <?php echo  $wcap_email_opened . '%' ; ?>  
            </span>
        </div>
        <div id ="wcap_email_opened_count_div_text" class ="wcap_email_opened_count_div_text" >
            <span id ="wcap_email_opened_count_div_text_span" class ="wcap_email_opened_count_div_text_span" > 
                <?php echo "Open <br>Rate"; ?>  
            </span>
        </div>
        <?php 
    }
    function wcap_abandoned_email_opened_ratio ( $wcap_email_opened_count,  $wcap_email_sent_count ) {
        ?>
        <div id ="wcap_abandoned_email_opened_div_ratio" class ="wcap_abandoned_email_opened_div_ratio">
            <span id ="wcap_abandoned_email_opened_div_span_ratio" class ="wcap_abandoned_email_opened_div_span_ratio"> 
                <?php echo $wcap_email_opened_count. " / ". $wcap_email_sent_count;  ?> 
            </span>
        </div>
        <div id ="wcap_abandoned_email_opened_div_text_ratio" class ="wcap_abandoned_email_opened_div_text_ratio" > 
            <span id ="wcap_abandoned_email_opened_div_text_span_ratio" class ="wcap_abandoned_email_opened_div_text_span_ratio"> 
                <?php echo "Emails <br>Opened";  ?> 
            </span>
        </div>
        <?php 
    }
    
    function wcap_abandoned_email_clicked ( $wcap_email_clicked ) {
        ?>
        <div id ="wcap_abandoned_email_clicked_div" class ="wcap_abandoned_email_clicked_div" >
            <span id ="wcap_abandoned_email_clicked_div_span" class ="wcap_abandoned_email_clicked_div_span" > 
                <?php echo $wcap_email_clicked . "%" ; ?>  
            </span>
        </div>
        <div id ="wcap_abandoned_email_clicked_div_text" class ="wcap_abandoned_email_clicked_div_text" >
            <span id ="wcap_abandoned_email_clicked_div_text_span" class ="wcap_abandoned_email_clicked_div_text_span" > 
                <?php echo "Click <br>Rate"; ?>  
            </span>
        </div>
        <?php 
    }
    
    function wcap_emails_clicked_ratio ( $wcap_emails_clicked, $wcap_email_opened_count ) {
        ?>
        <div id ="wcap_emails_clicked_ratio_div" class ="wcap_emails_clicked_ratio_div" >
            <span id ="wcap_emails_clicked_ratio_div_span" class ="wcap_emails_clicked_ratio_div_span" > 
                <?php echo $wcap_emails_clicked . " / " . $wcap_email_opened_count; ?>  
            </span>
        </div>
        <div id ="wcap_emails_clicked_ratio_div_text" class ="wcap_emails_clicked_ratio_div_text" >
            <span id ="wcap_emails_clicked_ratio_div_text_span" class ="wcap_emails_clicked_ratio_div_text_span" > 
                <?php echo "Emails <br>Clicked"; ?>  
            </span>
        </div>
        <?php 
    }
}