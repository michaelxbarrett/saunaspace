<?php
function getrates(){
	
	$woocommerce_easypost_enabled = get_option ( 'pvit_easypostwanderlust_shipper_enable' );
	if ($woocommerce_easypost_enabled =='1') { 
	$default = esc_attr( get_option('woocommerce_default_country') );
	$woocommerce_currency = get_woocommerce_currency();

	$country = ( ( $pos = strrpos( $default, ':' ) ) === false ) ? $default : substr( $default, 0, $pos );  

	$woocommerce_easypost_customs_info_description = get_option( 'pvit_easypostwanderlust_customsdescription' );
	$woocommerce_easypost_customs_info_hs_tariff_number = get_option( 'pvit_easypostwanderlust_customshs' );
	$woocommerce_easypost_customs_info_contents_type = get_option( 'pvit_easypostwanderlust_customstype' );
	$woocommerce_easypost_company = get_option( 'pvit_easypostwanderlust_sender_company' ); 
 	$woocommerce_easypost_sender_name = get_option( 'pvit_easypostwanderlust_sender_name' ); 
	$woocommerce_easypost_street1 = get_option( 'pvit_easypostwanderlust_sender_address1' );
	$woocommerce_easypost_city = get_option( 'pvit_easypostwanderlust_shipper_city' );
	$woocommerce_easypost_state = get_option( 'pvit_easypostwanderlust_sender_state' );
	$woocommerce_easypost_zip = get_option( 'pvit_easypostwanderlust_shipper_zipcode' );
	$woocommerce_easypost_phone = get_option( 'pvit_easypostwanderlust_shipper_phone' );
	$woocommerce_easypost_country = get_option( 'pvit_easypostwanderlust_shipper_country' );


	$order_id =  $_POST ['orderid'];
	$order = new WC_Order ( $order_id  );

    $client_country = $order->shipping_country;  
    $client_company = $order->shipping_company;
    $client_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
    $client_fname =  $order->shipping_first_name;
    $client_lname = $order->shipping_last_name;
    $client_address1 = $order->shipping_address_1. ' ' . $order->shipping_address_2;
    $client_address1a = $order->shipping_address_1;
    $client_address2 = $order->shipping_address_2;
    $client_city = $order->shipping_city;
    $client_state = $order->shipping_state;
    $client_zip = $order->shipping_postcode;
    $client_phone = $order->billing_phone;
    $client_email = $order->billing_email;  
		
 	$name = str_replace("\'","'", $client_name);
 	$company = str_replace("\'","'", $client_company);
 	$street1 = str_replace("\'","'", $client_address1a);
 	$street2 = str_replace("\'","'", $client_address2);
 	$city = str_replace("\'","'", $client_city);



 	$largocaja = $_POST ['lengthnuevo'];
 	$anchocaja =$_POST ['widthnuevo'];
 	$altocaja =$_POST ['heightnuevo'];
 	$valorpaquete =$_POST ['valuenuevo']; 
 	$shipping_info ['items'] = $shipping_info_items;
 	$shipping_info ['total_weight'] = $total_weight;
 	$pesototal =$_POST ['weightnuevo'];
 	$shipment_signature =$_POST ['shipment_signature'];
 	$residential_to_address =$_POST ['residential_to_address'];
 	$flatbox = $_POST ['service']; 

 
 	session_start();
 	$_SESSION['service'] = $_POST ['service']; 
 	$_SESSION['pesototal'] = $_POST ['weightnuevo']; 
 	$_SESSION['residential_to_address'] = $_POST ['residential_to_address'];

 	$items = $order->get_items();
 	$productinorder =  count($items);
 	$weight_unit = esc_attr( get_option('woocommerce_weight_unit' ));     
 	$dimension_unit = esc_attr( get_option('woocommerce_dimension_unit' ));
	

	if ( sizeof( $items ) > 0 ) { 
					$i = 0;
					$sum = 0;
					$prod = 0;
					$totales =  array();
								foreach( $items as $item ) {    				 
									if ( $item['product_id'] > 0 ) {
										$productid = $item['product_id']; 
										$productidweight  = get_post_meta($productid, '_weight' ); 
										$prodcutdimentionslength = get_post_meta($productid, '_length');
										$prodcutdimentionswidth = get_post_meta($productid, '_width');
										$prodcutdimentionsheight = get_post_meta($productid, '_height');
										$pweight = $productidweight[0] * $item['item_meta']['_qty']['0'];
										$i++; 
										$prod += $item['item_meta']['_qty']['0']; 
										$sum += $pweight; 
										$totales[] = $item['item_meta']['_qty']['0'];
									$variation = $item['variation_id'];
									if ( ! empty( $variation) ) { 	
										$productidweight2  = get_post_meta($variation, '_weight' ); 
										$prodcutdimentionslength2 = get_post_meta($variation, '_length');
										$prodcutdimentionswidth2 = get_post_meta($variation, '_width');
										$prodcutdimentionsheight2 = get_post_meta($variation, '_height'); 
										$pweightb = $productidweight2[0] * $item['item_meta']['_qty']['0'];
										//var_dump($pweight);
										$sum += $pweight + $pweightb;
									} 

									} 
								}  $itemtotales = array_sum($totales); 

	 }

 

	try {

		$customs_item = \EasyPost\CustomsItem::create(array(
		  "description" => $woocommerce_easypost_customs_info_description,
		  "quantity" => $itemtotales,
		  "weight" => $pesototal,
		  "value" => $valorpaquete,
		  "hs_tariff_number" => $woocommerce_easypost_customs_info_hs_tariff_number,
		  "origin_country" => $country 
		));            


		$customs_info = \EasyPost\CustomsInfo::create(array(
		  "eel_pfc" => 'NOEEI 30.37(a)',
		  "customs_certify" => false,
		  "contents_type" => $woocommerce_easypost_customs_info_contents_type,
		  "contents_explanation" => '',
		  "restriction_type" => 'none',
		  "non_delivery_option" => 'return',
		  "customs_items" => array($customs_item)
		));
	

	    if($client_country == 'US'){
			$to_address =  \EasyPost\Address::create(array(
				"name"    => $name,
				"company" => $company,
				"street1" => $street1,
				"street2" => $street2,
				"city"    => $city,
				"state"   => $client_state,
				"zip"     => $client_zip,
				"phone"   => $client_phone,
				"country" => $client_country,
				"email" => $client_email,
				"residential" => $residential_to_address,
 			)
 			);
 		echo $to_address['message'];
 	    } else if($client_country != 'US') {
			$to_address =  \EasyPost\Address::create(array(
				"name"    => $name,
 				"company" => $company,
				"street1" => $street1,
				"street2" => $street2,
				"city"    => $city,
				"state"   => $client_state,
				"zip"     => $client_zip,
				"phone"   => $client_phone,
				"country" => $client_country,
				"email" => $client_email,
				"residential" => $residential_to_address,
			)
			);		
 		}	
		  


		$from_address = \EasyPost\Address::create(
			array(
				"company" => $woocommerce_easypost_company,
				"street1" => $woocommerce_easypost_street1,
				"city"    => $woocommerce_easypost_city,
				"state"   => $woocommerce_easypost_state,
				"zip"     => $woocommerce_easypost_zip,
				"phone"   => $woocommerce_easypost_phone,
				"country" => $woocommerce_easypost_country
			)
		);
 
		// PARCELS //
		if (empty($flatbox)){

				$boxes = array();
				$boxesdim = array();		
				$boxes_dimensions = $_POST ['boxes'];

				$box_single = explode( ',', $boxes_dimensions ) ;
				$result = count($box_single);

				if($result <= 6){			
						$output = array_slice($box_single, 0, 6);
						$boxesdim[] = $output;
				} else if($result <= 12){
						$outputb = array_slice($box_single, 6, 12);
						$output = array_slice($box_single, 0, 6);
						$boxesdim[] = $output;
						$boxesdim[] = $outputb;
				} else if($result <= 18){
						$outputc = array_slice($box_single, 12, 18);	
						$outputb = array_slice($box_single, 6, 12);
						$output = array_slice($box_single, 0, 6);
						$boxesdim[] = $output;
						$boxesdim[] = $outputb;
						$boxesdim[] = $outputc;
				} else if($result <= 24){
						$outputd = array_slice($box_single, 18, 24);	
						$outputc = array_slice($box_single, 12, 18);	
						$outputb = array_slice($box_single, 6, 12);
						$output = array_slice($box_single, 0, 6);
						$boxesdim[] = $output;
						$boxesdim[] = $outputb;
						$boxesdim[] = $outputc;	
						$boxesdim[] = $outputd;		
				}

				foreach($boxesdim as $box){
						$boxes[] = 
								 array(
									 "parcel" => array("length" => $box[3], "width" => $box[4], "height" => $box[2], "weight" => $box[1] * 16),
									 "options" => array(
										  'delivery_confirmation' => $shipment_signature, 
										  'print_custom_1' => 'Order #' . $order_id,
										  'currency' => $woocommerce_currency
									 ), "customs_info" => $customs_info,
								 );	
				}

				$order = \EasyPost\Order::create(array(
					"from_address" => $from_address,
					"to_address" => $to_address,
					"shipments" => $boxes,
 					"customs_info" => $customs_info,
				));
 
				$_SESSION['multilabel'] = $order->id; //save easypost orderid

		} else {
 				$_SESSION['multilabel'] = null;
				$params = array("length" => null,
								"width"  => null,
								"height" => null,
								"predefined_package" => $flatbox,
								"weight" => $pesototal 
				);

				$parcel = \EasyPost\Parcel::create($params);
 
			$shipment = \EasyPost\Shipment::create(
				array(
				  "to_address"   => $to_address,
				  "from_address" => $from_address,
				  "parcel"       => $parcel,
				  "customs_info" => $customs_info,
				  "options" => array(
									  'delivery_confirmation' => $shipment_signature, 
									  'residential_to_address' => $residential_to_address,
									  'print_custom_1' => 'Order #' . $order_id,
					  				  'currency' => $woocommerce_currency
									)
				)
			  );

			$created_rates = \EasyPost\Rate::create($shipment);
				  foreach($created_rates as $r)  {
					$rate = array(
					  'id' => sprintf("%s-%s|%s", $r->carrier, $r->service, $shipment->id),
					  'label' => sprintf("%s %s", $r->carrier , $r->service),
					  'cost' => $r->rate,
					  'calc_tax' => 'per_item'
					);

					$filter_out =   array('LibraryMail', 'MediaMail');
					error_log(var_export($filter_out,1));  {
					  if (!in_array($r->service, $filter_out))  {
						// Register the rate
						 //var_dump($rate);
					  }
					} 
				  } 


			echo '<div>';
			echo '<h3>Shipping Rates </h3>';
			echo '<select id="service_option" name="service_selection">';
			   foreach ($shipment['rates'] as $key) { 
			   //var_dump($key);
				   if($key["carrier"] == 'FedEx'){
							$str23 = substr($key["delivery_date"], 5); 
							$str2 = substr($str23, 0, -10);  // returns "abcde"
							echo '<option value="'. $key["service"] . '" data-carriers="'. $key["carrier"] .'" data-id="'. $key["shipment_id"] .'">' . '$' . $key["rate"] . ' - ' . $key["carrier"] . ', '. $key["service"] . ', - Delivery Date '. $str2 . '</option>';

				   } else {	   
							echo '<option value="'. $key["service"] . '" data-carriers="'. $key["carrier"] .'" data-id="'. $key["shipment_id"] .'">' . '$' . $key["rate"] . ' - ' . $key["carrier"] . ', '. $key["service"] . '</option>';
					}
				}
			echo '</select>'; 
			echo '</div>';
		}	
	
		if (empty($flatbox)){
				foreach($order['rates'] as $r)  {
					$rate = array(
					  'id' => sprintf("%s-%s|%s", $r->carrier, $r->service, $shipment->id),
					  'label' => sprintf("%s %s", $r->carrier , $r->service),
					  'cost' => $r->rate,
					  'calc_tax' => 'per_item'
					);

					$filter_out =   array('LibraryMail', 'MediaMail');
					error_log(var_export($filter_out,1));  {
					  if (!in_array($r->service, $filter_out))  {
						// Register the rate
						 //var_dump($rate);
					  }
					} 
				} 

				echo '<div>';
				echo '<h3>Shipping Rates </h3>';
				echo '<select id="service_option" name="service_selection">';

			$newrates = array();
			foreach($order['rates'] as $r)  {
				
				$newrates[] = array(
					 'rate' => $r->rate, 
					 'carrier' => $r->carrier, 
					 'service' => $r->service,
					);
					
  			}
		
		array_multisort($newrates, SORT_ASC, $order['rates']); 	
 				
	      foreach($newrates as $key) {				


 				   //var_dump($key);
					   if($key["carrier"] == 'FedEx'){
								$str23 = substr($key["delivery_date"], 5); 
								$str2 = substr($str23, 0, -10);  // returns "abcde"
								echo '<option value="'. $key["service"] . '" data-carriers="'. $key["carrier"] .'" data-id="'. $key["shipment_id"] .'">' . '$' . $key["rate"] . ' - ' . $key["carrier"] . ', '. $key["service"] . ', - Delivery Date '. $str2 . '</option>';

					   } else {	   
								echo '<option value="'. $key["service"] . '" data-carriers="'. $key["carrier"] .'" data-id="'. $key["shipment_id"] .'">' . '$' . $key["rate"] . ' - ' . $key["carrier"] . ', '. $key["service"] . '</option>';
						}
					}
				echo '</select>'; 
				echo '</div>';
		}	
	
	} catch (Exception $e) {
    	echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
die($results);}}