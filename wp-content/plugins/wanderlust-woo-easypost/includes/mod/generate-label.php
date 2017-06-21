<?php
	function buylabel(){
		$woocommerce_easypost_enabled = get_option ( 'pvit_easypostwanderlust_shipper_enable' );
		if ($woocommerce_easypost_enabled =='1') { 

			$default = esc_attr( get_option('woocommerce_default_country') );
			$country = ( ( $pos = strrpos( $default, ':' ) ) === false ) ? $default : substr( $default, 0, $pos );  
			$woocommerce_easypost_test = get_option( 'pvit_easypostwanderlust_shipper_test' );
			$woocommerce_easypost_test_api_key = get_option( 'pvit_easypostwanderlust_testkey' );
			$woocommerce_easypost_live_api_key = get_option( 'pvit_easypostwanderlust_livekey' );
			$woocommerce_easypost_customs_info_description = get_option( 'pvit_easypostwanderlust_customsdescription' );
			$woocommerce_easypost_customs_info_hs_tariff_number = get_option( 'pvit_easypostwanderlust_customshs' );
			$woocommerce_easypost_customs_info_contents_type = get_option( 'pvit_easypostwanderlust_customstype' );
			$woocommerce_easypost_company = get_option( 'pvit_easypostwanderlust_sender_company' ); 
			$woocommerce_easypost_street1 = get_option( 'pvit_easypostwanderlust_sender_address1' );
			$woocommerce_easypost_city = get_option( 'pvit_easypostwanderlust_shipper_city' );
			$woocommerce_easypost_state = get_option( 'pvit_easypostwanderlust_sender_state' );
			$woocommerce_easypost_zip = get_option( 'pvit_easypostwanderlust_shipper_zipcode' );
			$woocommerce_easypost_phone = get_option( 'pvit_easypostwanderlust_shipper_phone' );
			$woocommerce_easypost_country = get_option( 'pvit_easypostwanderlust_shipper_country' );

			if ($woocommerce_easypost_test =='1') { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_test_api_key);} else {\EasyPost\EasyPost::setApiKey($woocommerce_easypost_live_api_key);} 

			$order_id =  $_POST ['orderid'];
			$orders = new WC_Order ( $order_id  );

			$sendtext = $_POST ['sendtext'];
			session_start();
			$_SESSION['carrier'] = $_POST ['carrier'];
			$_SESSION['shipservice'] = $_POST ['shipservice'];
			$_SESSION['send_email'] = $_POST ['send_email']; 
			$_SESSION['shippingid'] = $_POST ['shippingid']; 
			$residential_to_address = $_SESSION['residential_to_address'];
			$_SESSION['valorpaquete'] = $_POST ['valuenuevo']; 		

			try {
				
				if(!empty($_SESSION['shippingid']) && empty($_SESSION['multilabel'])){ //IF IS FLAT BOX
					$shipment = \EasyPost\Shipment::retrieve($_SESSION['shippingid']);
					$shipment->buy($shipment->lowest_rate(array($_SESSION['carrier']), array($_SESSION['shipservice'])));
					
					  $date = strtotime( date('Y-m-d') );
					  $tracking_provider = strtolower($shipment->selected_rate->carrier);
					  update_post_meta($order_id, 'easypost_shipping_label_1', $shipment->postage_label->label_url); 
					  update_post_meta($order_id, '_tracking_number',  $shipment->tracking_code);
					  update_post_meta($order_id, '_tracking_provider', $tracking_provider );   
					  update_post_meta($order_id, '_date_shipped', $date);
					  update_post_meta($order_id, '_wanderlustshipid', $shipment->id);
					  $orders->update_status('completed', 'order_note');

						$orders->add_order_note(
						  sprintf(
							  "Shipping label available at: '%s'",
							  $shipment->postage_label->label_url
						  )
						);

						$orders->add_order_note(
						  sprintf(
							  "Tracking Code: '%s'",
							  $shipment->tracking_code
						  )
						);

						if ($_SESSION['send_email'] == 1) {
									$sendto = get_option( 'pvit_easypostwanderlust_email_label_to' );  
									if (!empty($sendto)){ 
										$sendfrom = get_option( 'pvit_easypostwanderlust_email_label_from' );
										$mesage = $sendtext;
										$mailer = new AttachMailer($sendfrom, $sendto, "Shipping Label", $mesage);
										$mailer->attachFile($shipment->postage_label->label_url);
										$mailer->send() ? "envoye": "probleme envoi";
									}  
						}         

	
						$save_path = plugin_dir_path ( __FILE__ ) . 'generated_labels/';
						$save_url = plugin_dir_url(dirname(__FILE__)) . 'mod/generated_labels/';
	
						$fp = fopen($save_path . $shipment->tracking_code . '.png', 'wb');
						$content = file_get_contents($shipment->postage_label->label_url);
						fwrite($fp, $content); //Create PNG or PDF file
						fclose($fp);
	
						echo '<h3>Shipping Label</h3>';   
						echo  '<div style="cursor: pointer;" class="print"  data-imgid="'. $save_url . $shipment->tracking_code .'.png"><a href="#"><img src="'. $save_url . $shipment->tracking_code .'.png" width="150" height="auto" alt="'. $shipment->selected_rate->service .'" title="'. $shipment->selected_rate->service .'"></a></div>';
	
				} else {  //IF IS REGULAR ORDER
 					$order = \EasyPost\Order::retrieve($_SESSION['multilabel']);	
					$order->buy(array("carrier" => $_SESSION['carrier'], "service" => $_SESSION['shipservice']));

					echo '<h3>Shipping Label</h3>';  

					$countlabels = 0;
					// CHECK ALL SHIMPMENTS -- STARTS
					foreach($order['shipments'] as $shipment)  {
						$countlabels++; 
 
						// UPDATE ORDER INFO -- STARTS
						$today = date("m/d/Y"); 
						$date = strtotime( date('Y-m-d') );
						$tracking_provider = $shipment->selected_rate->carrier;

if($tracking_provider == 'FedEx'){$tracking_provider = 'fedex';}

		$tracking_item = array();
		$tracking_item[ 'tracking_provider' ]        = wc_clean( $tracking_provider );
		$tracking_item[ 'custom_tracking_provider' ] = wc_clean( $args[ 'custom_tracking_provider' ] );
		$tracking_item[ 'custom_tracking_link' ]     = wc_clean( $args[ 'custom_tracking_link' ] );
		$tracking_item[ 'tracking_number' ]          = wc_clean( $shipment->tracking_code );
		$tracking_item[ 'date_shipped' ]             = wc_clean(  $date  );
		$tracking_items[] = $tracking_item;

		update_post_meta( $order_id, '_wc_shipment_tracking_items', $tracking_items );
		


						update_post_meta($order_id, 'easypost_shipping_label_' . $countlabels, $shipment->postage_label->label_url); 
 
						update_post_meta($order_id, '_wanderlustshipid', $shipment->id);
						update_post_meta($order_id, '_wanderlustshiporderid', $_SESSION['multilabel']);				
						//$order->update_status('completed', 'order_note'); //check this			

						$orders->add_order_note(
							sprintf(
							  "Shipping label available at: '%s'",
							  $shipment->postage_label->label_url
							)
						);

						$orders->add_order_note(
							sprintf(
							  "Tracking Code: '%s'",
							  $shipment->tracking_code
							)
						);	
						// UPDATE ORDER INFO -- ENDS

						// SEND VIA EMAIL -- STARTS
						if ($_SESSION['send_email'] == 1) {
							$sendto = get_option( 'pvit_easypostwanderlust_email_label_to' );  
								if (!empty($sendto)){ 
									$sendfrom = get_option( 'pvit_easypostwanderlust_email_label_from' );
									$mesage = $sendtext;
									$mailer = new AttachMailer($sendfrom, $sendto, "Shipping Label", $mesage);
									$mailer->attachFile($shipment->postage_label->label_url);
									$mailer->send() ? "envoye": "probleme envoi";
								}  
						}   
						// SEND VIA EMAIL -- ENDS

						// SAVE LABEL ON FTP -- STARTS
						$save_path = plugin_dir_path ( __FILE__ ) . 'generated_labels/';
						$save_url = plugin_dir_url(dirname(__FILE__)) . 'mod/generated_labels/';
						$fp = fopen($save_path . $shipment->tracking_code . '.png', 'wb'); //Create PNG or PDF file
						$content = file_get_contents($shipment->postage_label->label_url);
						fwrite($fp, $content); 
						fclose($fp);
						// SAVE LABEL ON FTP -- ENDS

						// SHOW LABEL IMAGES -- STARTS
						echo  '<div style="cursor: pointer;" class="print"  data-imgid="'. $save_url . $shipment->tracking_code .'.png"><a href="#"><img src="'. $save_url . $shipment->tracking_code .'.png" width="150" height="auto" alt="'. $shipment->selected_rate->service .'" title="'. $shipment->selected_rate->service .'"></a></div>';
						// SHOW LABEL IMAGES -- ENDS
					}
					// CHECK ALL SHIMPMENTS -- ENDS
 				} 		

				// INSURE PACKAGE -- STARTS
 				if($_POST ['insurance']  == '1'){
 					$shipsid = $_SESSION['shippingid'];
 					$packagevalue = $_SESSION['valorpaquete'];
 					insurepackage($shipsid, $packagevalue);
 				}
				// INSURE PACKAGE -- ENDS

			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n"; // ERRORS -- STARTS/ENDS
			}	
		die($results);
		}
	} // END FUNCTION //	