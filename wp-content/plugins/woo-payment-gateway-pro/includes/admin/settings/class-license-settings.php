<?php

class Braintree_Gateway_License_Settings extends Braintree_Gateway_Settings_API
{

	public function __construct()
	{
		$this->page = 'braintree-gateway-settings';
		$this->tab = 'license-settings';
		$this->id = 'license_settings';
		$this->label = __( 'License', 'braintree-payments' );
		$this->title = array (
				'title' => __( 'License Settings' ), 
				'description' => __( 'On this page you can activate and manage your license with Payment Plugins. Should you need to transfer your license to another domain, you can refresh the license. A license can only be refreshed from the same domain that it was activated on.', 'braintree-payments' ) 
		);
		add_filter( 'braintree_gateway_validate_license', array (
				$this, 
				'validate_license' 
		) );
		add_action( 'bfwc_admin_after_save_button', array (
				$this, 
				'output_buttons' 
		) );
		add_filter( 'bfwc_admin_settings_button_text', array (
				$this, 
				'settings_button_text' 
		), 10, 2 );
		parent::__construct();
	}

	/**
	 * Check if the user is trying to check an expired license first.
	 *
	 * {@inheritDoc}
	 *
	 * @see Stripe_Gateway_Settings_API::save()
	 */
	public function save()
	{
		if ( isset( $_POST [ 'bfwc_refresh_license' ] ) ) {
			$this->refresh_license( $this->get_field_value( 'license' ) );
		} elseif ( isset( $_POST [ 'bfwc_check_license' ] ) ) {
			$this->check_license();
		} else {
			parent::save();
		}
	}

	public function settings()
	{
		$options = array (
				'license' => array (
						'title' => __( 'License Key', 'braintree-payments' ), 
						'type' => 'text', 
						'default' => '', 
						'value' => '', 
						'description' => __( 'In this field you enter your license key. To activate your license, enter the license key from your Payment Plugins order and save your settings.', 'braintree-payments' ), 
						'tool_tip' => true 
				), 
				'license_status_notice' => array (
						'title' => __( 'License Status', 'braintree-payments' ), 
						'type' => 'custom', 
						'function' => array (
								$this, 
								'output_license_notice' 
						), 
						'default' => '', 
						'class' => '', 
						'tool_tip' => true, 
						'description' => __( 'In order for your license status to show as active, you must purchase a license and activate it.', 'braintree-payments' ) 
				), 
				'license_registered_domain' => array (
						'title' => __( 'Registered Domain', 'braintree-payments' ), 
						'type' => 'custom', 
						'function' => array (
								$this, 
								'output_registered_domain' 
						), 
						'default' => '', 
						'class' => '', 
						'tool_tip' => true, 
						'description' => __( 'This is the domain that your license is registered under.', 'braintree-payments' ) 
				) 
		);
		if ( ! get_option( 'bfwc_registered_domain', false ) ) {
			unset( $options [ 'license_registered_domain' ] );
		}
		return $options;
	}

	public function output_license_notice( $key, $data )
	{
		$status = bt_manager()->get_license_status();
		$class = '';
		switch( $status ) {
			case 'inactive' :
				$class = 'red-text text-lighten-2';
				break;
			case 'expired' :
				$class = 'orange-text';
				break;
			case 'pending' :
				$class = 'light-blue-text darken-3';
				break;
			case 'active' :
				$class = 'green-text text-lighten-2';
				break;
		}
		echo '<div class="row"><div class="input-field col s12"><h5 class="' . $class . '">' . bfwc_admin_status_name( $status ) . '</h5></div></div>';
	}

	public function output_registered_domain( $key, $data )
	{
		$reg_domain = get_option( 'bfwc_registered_domain', false );
		if ( $reg_domain ) {
			echo '<div class="row"><div class="input-field col s12"><p> ' . $reg_domain . '</p></div>';
		}
	}

	public function validate_license( $license )
	{
		if ( empty( $license ) ) {
			bt_manager()->add_admin_notice( 'error', __( 'Please add a valid license key in order to begin accepting live payments.', 'braintree-payments' ) );
			return false;
		}
		
		$attempt = 0;
		$url_args = array (
				'slm_action' => 'slm_activate', 
				'license_key' => $license, 
				'item_reference' => 'woo-payment-gateway' 
		);
		
		$response = bt_manager()->execute_curl( $url_args );
		if ( $response [ 'result' ] === 'error' ) {
			bt_manager()->add_admin_notice( 'error', sprintf( __( 'Your license could not be activated at this time. Reason: %s', 'braintree-payments' ), $response [ 'message' ] ) );
			bt_manager()->error( sprintf( __( 'Your license could not be activated at this time. Reason: %s', 'braintree-payments' ), $response [ 'message' ] ) );
			$license = false;
		} else if ( $response [ 'result' ] === 'success' ) {
			bt_manager()->add_admin_notice( 'success', sprintf( __( $response [ 'message' ] . 'You can now configure your Live API keys and begin accepting real payments.', 'braintree-payments' ) ) );
			bt_manager()->b702ac1335a1508782a8d789085feefe( $response [ 'license_status' ] );
			update_option( 'bfwc_registered_domain', $response [ 'registered_domain' ] );
		}
		return $license;
	}

	public function refresh_license( $license )
	{
		$url_args = array (
				'slm_action' => 'slm_deactivate', 
				'license_key' => $license 
		);
		
		$response = bt_manager()->execute_curl( $url_args );
		
		if ( $response [ 'result' ] === 'success' ) {
			bt_manager()->add_admin_notice( 'success', sprintf( __( 'Your license has been refreshed successfully. You can now activate your license on another domain.', 'braintree-payments' ) ) );
			bt_manager()->b702ac1335a1508782a8d789085feefe( 'pending' );
			bt_manager()->settings [ 'sandbox_environment' ] = 'yes';
			bt_manager()->settings [ 'production_environment' ] = 'no';
			bt_manager()->update_settings();
			delete_option( 'bfwc_registered_domain' );
		} elseif ( $response [ 'result' ] === 'error' ) {
			bt_manager()->add_admin_notice( 'error', sprintf( __( 'Your license could not be refreshed at this time. Reason: %s', 'braintree-payments' ), $response [ 'message' ] ) );
		}
	}

	public function check_license()
	{
		$url_args = array (
				'slm_action' => 'slm_check', 
				'item_reference' => 'woo-payment-gateway' 
		);
		$response = bt_manager()->execute_curl( $url_args );
		
		if ( $response [ 'result' ] === 'success' ) {
			bt_manager()->b702ac1335a1508782a8d789085feefe( $response [ 'status' ] );
			bt_manager()->add_admin_notice( 'success', sprintf( __( 'License check completed. License status is: %s', 'braintree-payments' ), bfwc_admin_status_name( $response [ 'status' ] ) ) );
			if ( isset( $response [ 'registered_domain' ] ) ) {
				update_option( 'bfwc_registered_domain', $response [ 'registered_domain' ] );
			}
		} elseif ( $response [ 'result' ] === 'error' ) {
			bt_manager()->add_admin_notice( 'error', sprintf( __( 'Error checking license. Reason: %s', 'braintree-payments' ), $response [ 'message' ] ) );
		}
	}

	public function output_buttons( $current_tab )
	{
		if ( $this->tab === $current_tab ) {
			ob_start();
			echo '<div class="input-field col s12 m6 l4"><button class="waves-effect waves-light btn teal darken-1" name="bfwc_refresh_license">' . __( 'Refresh License', 'braintree-payments' );
			bfwc_admin_get_template( 'html-helpers/pre-loader.php' );
			echo '</button></div>';
			
			$license = bt_manager()->get_option( 'license' );
			if ( ! empty( $license ) ) {
				echo '<div class="input-field col s12 m6 l4"><button class="waves-effect waves-light btn light-blue darken-1" name="bfwc_check_license">' . __( 'Check License Status', 'braintree-payments' );
				bfwc_admin_get_template( 'html-helpers/pre-loader.php' );
				echo '</button></div>';
			}
			
			echo ob_get_clean();
		}
	}

	public function settings_button_text( $text, $tab )
	{
		if ( $this->tab === $tab ) {
			$text = __( 'Activate License', 'braintree-payments' );
		}
		return $text;
	}
}
new Braintree_Gateway_License_Settings();