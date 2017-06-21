<?php
/**
 * Shipment Tracking
 *
 * Shows tracking information in the HTML order email
 *
 * @author  WooThemes
 * @package WooCommerce Shipment Tracking/templates/email
 * @version 1.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $tracking_items ) : ?>
	<h2><?php echo apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Tracking Information', 'wc_shipment_tracking' ) ); ?></h2>

	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">

		<thead>
			<tr>
				<th class="tracking-provider" scope="col" class="td" style="text-align: left; color: rgba(0, 0, 0, 0.75); font-family: 'Open Sans', Helvetica, Roboto, Arial, sans-serif; border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; border-top: 0; border-left: 0; padding: 12px;"><?php _e( 'Provider', 'wc_shipment_tracking' ); ?></th>
				<th class="tracking-number" scope="col" class="td" style="text-align: left; color: rgba(0, 0, 0, 0.75); font-family: 'Open Sans', Helvetica, Roboto, Arial, sans-serif; border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; border-top: 0; border-left: 0; padding: 12px;"><?php _e( 'Tracking Number', 'wc_shipment_tracking' ); ?></th>
				<th class="date-shipped" scope="col" class="td" style="text-align: left; color: rgba(0, 0, 0, 0.75); font-family: 'Open Sans', Helvetica, Roboto, Arial, sans-serif; border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; border-top: 0; border-left: 0; padding: 12px;"><?php _e( 'Date', 'wc_shipment_tracking' ); ?></th>
				<th class="order-actions" scope="col" class="td" style="text-align: left; color: rgba(0, 0, 0, 0.75); font-family: 'Open Sans', Helvetica, Roboto, Arial, sans-serif; border-right: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; border-top: 0; border-left: 0; padding: 12px;">&nbsp;</th>
			</tr>
		</thead>

		<tbody><?php
			foreach ( $tracking_items as $tracking_item ) {
				
				?><tr class="tracking">
					<td class="tracking-provider" data-title="<?php _e( 'Provider', 'wc_shipment_tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item[ 'formatted_tracking_provider' ] ); ?>
					</td>
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'wc_shipment_tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item[ 'tracking_number' ] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Status', 'wc_shipment_tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item[ 'date_shipped' ] ); ?></time>
					</td>
					<td class="order-actions" style="text-align: center; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
							<a href="<?php echo esc_url( $tracking_item[ 'formatted_tracking_link' ] ); ?>" target="_blank"><?php _e( 'Track', 'wc_shipment_tracking' ); ?></a>
					</td>
				</tr><?php
			}
		?></tbody>
	</table>

<?php
endif;
