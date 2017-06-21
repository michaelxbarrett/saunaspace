<?php
use Braintree\Exception;

class Braintree_Gateway_API_Settings extends Braintree_Gateway_Settings_API
{

	public function __construct()
	{
		$this->page = 'braintree-gateway-settings';
		$this->id = 'api_settings';
		$this->tab = 'general';
		$this->label = __( 'API Settings', 'braintree-payments' );
		$this->title = array (
				'title' => __( 'API Settings', 'braintree-payments' ), 
				'description' => __( 'On this page you can configure your Braintree API Keys and set the environment that you want to use.', 'braintree-payments' ), 
				'helper' => array (
						'title' => __( 'Setup API Keys:', 'braintree-payments' ), 
						'enabled' => true, 
						'type' => 'video', 
						'url' => 'https://www.youtube.com/embed/yRl43QPukVE?vq=hd480', 
						'description' => __( 'Watch this instructional video on how to find and configure your API keys.', 'braintree-payments' ) 
				) 
		);
		add_action( 'bfwc_settings_title_after_description', array (
				$this, 
				'display_help_button' 
		), 9, 2 );
		add_action( 'bfwc_settings_title_after_description', array (
				$this, 
				'dispay_license_button' 
		), 10, 2 );
		add_action( 'braintree_gateway_after_save_settings', array (
				$this, 
				'maybe_test_connection' 
		) );
		add_action( "braintree_gateway_after_save_settings", array (
				$this, 
				'maybe_retrieve_merchant_accounts' 
		), 20 );
		add_action( 'bfwc_admin_settings_end', array (
				$this, 
				'output_input_field' 
		) );
		parent::__construct();
	}

	public function settings()
	{
		return array (
				'license_status_notice' => array (
						'title' => __( 'License Status', 'braintree-payments' ), 
						'type' => 'custom', 
						'function' => array (
								$this, 
								'output_license_notice' 
						), 
						'default' => '', 
						'class' => '', 
						'description' => __( 'In order for your license status to show as active, you must purchase a license and activate it.', 'braintree-payments' ), 
						'tool_tip' => true 
				), 
				'production_environment' => array (
						'type' => 'checkbox', 
						'title' => __( 'Enable Production', 'braintree-payments' ), 
						'default' => '', 
						'value' > 'yes', 
						'class' => 'filled-in production-option', 
						'attributes' => array (
								'uncheck' => "#{$this->get_field_key_name('sandbox_environment')}" 
						), 
						'tool_tip' => true, 
						'description' => __( 'When enabled, your Wordpress site will be connected to your Braintree production environment. You must have a license in order to activate production.', 'braintree-payments' ) 
				), 
				'production_merchant_id' => array (
						'type' => 'text', 
						'title' => __( 'Production Merchant ID', 'braintree-payments' ), 
						'default' => '', 
						'value' => '', 
						'class' => 'production-option', 
						'tool_tip' => true, 
						'description' => __( 'Your production merchant ID is used to identify your account when connection to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'enabled' => true, 
								'title' => __( 'Production Merchant ID', 'braintree-payments' ), 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/merchant_id.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://www.braintreegateway.com/login">Braintree Production</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'production_private_key' => array (
						'type' => 'password', 
						'title' => __( 'Production Private Key', 'braintree-payments' ), 
						'default' => '', 
						'value' => '', 
						'class' => 'production-option', 
						'tool_tip' => true, 
						'description' => __( 'Your private key is used like a password when connecting to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'title' => __( 'Production Private Key', 'braintree-payments' ), 
								'enabled' => true, 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/private_key.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://www.braintreegateway.com/login">Braintree Production</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'production_public_key' => array (
						'type' => 'text', 
						'title' => __( 'Production Public Key', 'braintree-payments' ), 
						'default' => '', 
						'value' => '', 
						'class' => 'production-option', 
						'tool_tip' => true, 
						'description' => __( 'Your public key is used like a username when connecting to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'title' => __( 'Production Public Key', 'braintree-payments' ), 
								'enabled' => true, 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/public_key.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://www.braintreegateway.com/login">Braintree Production</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'production_connection_test' => array (
						'type' => 'button', 
						'title' => __( 'Production Connection Test', 'braintree-payments' ), 
						'class' => 'btn blue darken-1 production-option', 
						'label' => __( 'Test Connection', 'braintree-payments' ), 
						'pre_loader' => true, 
						'tool_tip' => true, 
						'description' => __( 'This connection test will alrt you if you have entered your API keys incorrectly. Always test the connection after configuring your API keys.', 'braintree-payments' ) 
				), 
				'sandbox_environment' => array (
						'type' => 'checkbox', 
						'value' => 'yes', 
						'default' => '', 
						'title' => __( 'Enable Sandbox Mode', 'braintree-payments' ), 
						'class' => 'filled-in', 
						'attributes' => array (
								'uncheck' => "#{$this->get_field_key_name('production_environment')}" 
						), 
						'tool_tip' => true, 
						'description' => __( 'When enabled, your Wordpress site will be connected to your Braintree sandbox environment.', 'braintree-payments' ) 
				), 
				'sandbox_merchant_id' => array (
						'type' => 'text', 
						'title' => __( 'Sandbox Merchant ID', 'braintree-payments' ), 
						'value' => '', 
						'default' => '', 
						'tool_tip' => true, 
						'description' => __( 'Your sandbox merchant ID is used to identify your account when connection to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'title' => __( 'Sandbox Merchant ID', 'braintree-payments' ), 
								'enabled' => true, 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/merchant_id.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Sandbox</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'sandbox_private_key' => array (
						'type' => 'password', 
						'title' => __( 'Sandbox Private Key', 'braintree-payments' ), 
						'value' => '', 
						'default' => '', 
						'class' => '', 
						'tool_tip' => true, 
						'description' => __( 'Your private key is used like a password when connecting to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'title' => __( 'Sandbox Private Key', 'braintree-payments' ), 
								'enabled' => true, 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/private_key.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Sandbox</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'sandbox_public_key' => array (
						'type' => 'text', 
						'title' => __( 'Sandbox Public Key', 'braintree-payments' ), 
						'value' => '', 
						'default' => '', 
						'class' => '', 
						'tool_tip' => true, 
						'description' => __( 'Your public key is used like a username when connecting to Braintree.', 'braintree-payments' ), 
						'helper' => array (
								'title' => __( 'Sandbox Public Key', 'braintree-payments' ), 
								'enabled' => true, 
								'type' => 'img', 
								'url' => 'https://wordpress.paymentplugins.com/woo-payment-gateway/assets/public_key.png', 
								'description' => __( 'Login to your <a target="_blank" href="https://sandbox.braintreegateway.com/login">Braintree Sandbox</a> account and navigate to <strong>Account</strong> > <strong>My User</strong> > <strong>View Authorizations</strong> then click the <strong>View</strong> link.', 'braintree-payments' ) 
						) 
				), 
				'sandbox_connection_test' => array (
						'type' => 'button', 
						'title' => __( 'Sandbox Connection Test', 'braintree-payments' ), 
						'value' => '', 
						'label' => __( 'Test Connection', 'braintree-payments' ), 
						'class' => 'btn blue darken-1', 
						'pre_loader' => true, 
						'tool_tip' => true, 
						'description' => __( 'Once you have entered and saved your API keys, you can perform a connection test to ensure you have entered them correctly.', 'braintree-payments' ) 
				), 
				/* 'enable_auto_update' => array (
						'title' => __( 'Auto Update', 'braintree-payments' ), 
						'type' => 'checkbox', 
						'default' => 'no', 
						'value' => 'yes', 
						'tool_tip' => true, 
						'description' => __( 'If enabled, the plugin will update automatically.', 'braintree-payments' ) 
				), */
				'enable_debug' => array (
						'type' => 'checkbox', 
						'value' => 'yes', 
						'default' => 'yes', 
						'title' => __( 'Enable Debug Mode', 'braintree-payments' ), 
						'class' => 'filled-in', 
						'tool_tip' => true, 
						'description' => __( 'If you need to troubleshoot payment transactions, enable debug mode. You can view the debug messages on this page log entries page.', 'braintree-payments' ) 
				), 
				'enable_script_debug' => array (
						'type' => 'checkbox', 
						'title' => __( 'Enable Script Debug', 'braintree-payments' ), 
						'value' => 'yes', 
						'default' => 'no', 
						'tool_tip' => true, 
						'description' => __( 'This option should only be set when you are debugging the plugin\'s javascript files. The Payment Plugins support team may ask you to activate this setting if they are troubleshooting an issue on your site.', 'braintree-payments' ) 
				) 
		);
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
		echo '<div class="row"><div class="input-field col s12"><h5 class="' . $class . '">' . bfwc_admin_status_name( $status ) . '</h5><input type="hidden" id="braintree_gateway_license_status" value="' . bt_manager()->get_license_status() . '"/></div></div>';
	}

	public function maybe_test_connection()
	{
		$environment = null;
		if ( isset( $_POST [ $this->get_field_key_name( 'sandbox_connection_test' ) ] ) ) {
			$environment = 'sandbox';
		} elseif ( isset( $_POST [ $this->get_field_key_name( 'production_connection_test' ) ] ) ) {
			$environment = 'production';
			if ( bt_manager()->get_license_status() !== 'active' ) {
				bt_manager()->add_admin_notice( 'error', __( 'Hey hold up a sec! You gotta purchase a license before you start doing production stuff.', 'braintree-payments' ) );
				return;
			}
		} else {
			return;
		}
		Braintree_Configuration::environment( $environment );
		Braintree_Configuration::merchantId( bt_manager()->get_option( "{$environment}_merchant_id" ) );
		Braintree_Configuration::privateKey( bt_manager()->get_option( "{$environment}_private_key" ) );
		Braintree_Configuration::publicKey( bt_manager()->get_option( "{$environment}_public_key" ) );
		try {
			Braintree_ClientToken::generate();
			bt_manager()->add_admin_notice( 'success', sprintf( __( 'Your %s connection test was successful.', 'braintree-payments' ), $environment ) );
		} catch( Exception $e ) {
			bt_manager()->add_admin_notice( 'error', sprintf( __( 'Your %s connection test was unsuccessful. Please verify your API keys and try again.', 'braintree-payments' ), $environment ) );
		}
	}

	public function maybe_retrieve_merchant_accounts()
	{
		global $current_tab;
		if ( $this->tab === $current_tab ) {
			if ( ! empty( $_POST [ 'bfwc_settings_input_changed' ] ) ) {
				$env = bt_manager()->get_environment();
				bt_manager()->initialize_braintree();
				try {
					$gateway = \Braintree\Configuration::gateway()->merchantAccount();
					$accounts = $gateway->all();
					$merchant_accounts = array ();
					$saved_merchants = bt_manager()->get_option( "woocommerce_braintree_{$env}_merchant_account_id" );
					$saved_merchants = ! $saved_merchants ? array () : $saved_merchants;
					foreach ( $accounts as $account ) {
						if ( ! isset( $saved_merchants [ $account->currencyIsoCode ] ) ) {
							$saved_merchants [ $account->currencyIsoCode ] = $account->id;
						}
					}
					bt_manager()->set_option( "woocommerce_braintree_{$env}_merchant_account_id", $saved_merchants );
					bt_manager()->update_settings();
				} catch( \Braintree\Exception $e ) {
					bt_manager()->error( sprintf( __( 'Error retrieving merchant accounts for your %s environment.', 'braintree-payments' ), $env ) );
				}
			}
		}
	}

	public function output_input_field()
	{
		global $current_tab;
		if ( $this->tab === $current_tab ) {
			echo '<input type="hidden" name="bfwc_settings_input_changed" id="bfwc_settings_input_changed" value=""/>';
		}
	}

}
new Braintree_Gateway_API_Settings();