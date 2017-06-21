<?php
	function insurepackage($shipsid, $packagevalue) { 
			$woocommerce_easypost_test = get_option( 'pvit_easypostwanderlust_shipper_test' );
			$woocommerce_easypost_test_api_key = get_option( 'pvit_easypostwanderlust_testkey' );
			$woocommerce_easypost_live_api_key = get_option( 'pvit_easypostwanderlust_livekey' );
			if ($woocommerce_easypost_test == 1) { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_test_api_key); } else { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_live_api_key); } 
			if (empty($shipsid)){
				$shipsid = $_POST ['shippingid']; 
			}

			$shipment = \EasyPost\Shipment::retrieve($shipsid);
			$shipment->insure(array('amount' => $packagevalue));	

			echo '</br>Insured for: $' . $shipment->insurance;

			die($results);
	}	
	
	function labelpackageinfo() { 	
		$woocommerce_easypost_test = get_option( 'pvit_easypostwanderlust_shipper_test' );
		$woocommerce_easypost_test_api_key = get_option( 'pvit_easypostwanderlust_testkey' );
		$woocommerce_easypost_live_api_key = get_option( 'pvit_easypostwanderlust_livekey' );
		if ($woocommerce_easypost_test == 1) { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_test_api_key); } else { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_live_api_key); } 

		$_SESSION['shippingid'] = $_POST ['shippingid']; 
		$order = $_POST ['shiporder']; 
		$shipment = \EasyPost\Shipment::retrieve($_SESSION['shippingid']);
		$selected_rate = $shipment->selected_rate->rate;
		$carrier = $shipment->selected_rate->carrier;
		$service = $shipment->selected_rate->service;
		$delivery_days = $shipment->selected_rate->delivery_days;
		$created_at = $shipment->parcel->created_at;
		$delivery_date = $shipment->selected_rate->delivery_date;
		$height = $shipment->parcel->height;
		$length = $shipment->parcel->length;
		$width = $shipment->parcel->width;
		$weight	= $shipment->parcel->weight;
		$predefined_package = $shipment->parcel->predefined_package;
		$insurancecost = $shipment->insurance * 0.01; 

		

		$_SESSION['datashiporderid'] = $_POST ['datashiporderid']; 
		if(!empty($_SESSION['datashiporderid'])){
			$datashiporderid = \EasyPost\Order::retrieve($_SESSION['datashiporderid']);
			echo 'Insurance: $' . $datashiporderid['shipments'][0]->insurance;
			echo '</br>Insurance Cost: $' . $datashiporderid['shipments'][0]->insurance * 0.01; 
			echo '</br>';
		} else {
			echo 'Insurance: $' . $shipment->insurance;
			echo '</br>Insurance Cost: $' . $insurancecost;
			echo '</br>'; //check this for each shipment
		}

		if($carrier == 'FedEx'){echo 'Cost: $'.$selected_rate. ' </br>Service: '.$service. '</br>Delivery Days: '.$delivery_days. ' </br>Created on: ' .$created_at .'</br>Delivery Date: '.$delivery_date. ' </br>Height: ' .$height.' in. </br>Lenght: '.$length.' in. </br> Width: '.$width.' in. </br> Weight: '.$weight.' oz. </br> Pred. Package: '.$predefined_package; 
		} else {
		echo 'Cost: $'.$selected_rate. ' </br>Service: '.$service. ' </br>Created on: ' .$created_at .' </br>Height: ' .$height.' in. </br>Lenght: '.$length.' in. </br> Width: '.$width.' in. </br> Weight: '.$weight.' oz. </br> Pred. Package: '.$predefined_package; 
		}

		//print($shipment.insurance);
		die($results);
	}

	function labelinfo() { 	
		$woocommerce_easypost_test = get_option( 'pvit_easypostwanderlust_shipper_test' );
		$woocommerce_easypost_test_api_key = get_option( 'pvit_easypostwanderlust_testkey' );
		$woocommerce_easypost_live_api_key = get_option( 'pvit_easypostwanderlust_livekey' );
		if ($woocommerce_easypost_test == 1) { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_test_api_key); } else { \EasyPost\EasyPost::setApiKey($woocommerce_easypost_live_api_key); } 

		$_SESSION['shippingid'] = $_POST ['shippingid']; 
		$order = $_POST ['shiporder']; 
		$shipment = \EasyPost\Shipment::retrieve($_SESSION['shippingid']);
 		$tracking = $shipment->tracker->tracking_details;

		 foreach ($tracking as $key) {
 			 echo '<h4>Message</h4>';
		 	 echo $key['message'];
		 	 echo ' - at <span style="font-size:11px;font-style: italic;">';
		 	 echo $key['datetime'];
		 	 echo '</span></br>';
			 //echo '</br><h4>Status</h4>';
			 //echo $key['status'];
		 }


	 
	//$tracking_code = "1Z204E38YW95204424";
  	//$carrier = "UPS";
  	//$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));

		//$order = new WC_Order( $order );

		//$order->add_order_note( sprintf( "Tracking Status: '%s'",  $message ) ); 
		//die($results);
	}