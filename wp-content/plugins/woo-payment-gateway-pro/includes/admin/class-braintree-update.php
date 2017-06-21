<?php
class Braintree_Gateway_Update
{

	private static function get_updates()
	{
		return array (
				'2.5.4' => bt_manager()->plugin_admin_path() . 'updates/braintree-update-2.5.4.php', 
				'2.6.0' => bt_manager()->plugin_admin_path() . 'updates/braintree-update-2.6.0.php' 
		);
	
	}

	public static function init()
	{
		
		/* Run updates when admin_init action is called. */
		add_action( 'braintree_wc_before_init', __CLASS__ . '::check_version' );
		
		add_filter( 'pre_set_site_transient_update_plugins', __CLASS__ . '::maybe_update_plugin' );
		
		add_action( 'in_plugin_update_message-' . BFWC_PLUGIN_NAME, __CLASS__ . '::get_admin_update_notice', 10, 2 );
		add_action( 'activate_' . BFWC_PLUGIN_NAME, __CLASS__ . '::maybe_install' );
	}

	/**
	 * On plugin activate, add the version if necessary.
	 */
	public static function maybe_install()
	{
		$version = get_option( 'braintree_for_woocommerce_version' );
		if ( ! $version ) {
			
			$previous_versions = array ();
			foreach ( self::get_updates() as $v => $file ) {
				$previous_versions [ $v ] = $v;
			}
			
			update_option( 'braintree_for_woocommerce_version', array (
					'currentVersion' => bt_manager()->version, 
					'previousVersions' => $previous_versions 
			) );
		}
	}

	/**
	 * Check the version of the current installation.
	 */
	public static function check_version()
	{
		$version = get_option( 'braintree_for_woocommerce_version' );
		if ( ! $version || version_compare( $version [ 'currentVersion' ], bt_manager()->version, '<' ) ) {
			self::update();
			add_action( 'admin_notices', __CLASS__ . '::update_notice' );
		}
	}

	public static function update()
	{
		if ( ! get_option( 'braintree_for_woocommerce_version' ) ) {
			$previousVersions = array ();
			foreach ( self::get_updates() as $version => $update ) {
				if ( file_exists( $update ) ) {
					include_once ( $update );
					$previousVersions [ $version ] = $version;
				}
			}
		} else {
			$versions = get_option( 'braintree_for_woocommerce_version' );
			if ( ! is_array( $versions ) ) {
				delete_option( 'braintree_for_woocommerce_version' );
				$versions = array (
						'previousVersions' => array () 
				);
			}
			$previousVersions = $versions [ 'previousVersions' ];
			foreach ( self::get_updates() as $version => $update ) {
				if ( ! array_key_exists( $version, $previousVersions ) ) {
					if ( file_exists( $update ) ) {
						include_once $update;
						$previousVersions [ $version ] = $version;
					}
				}
			}
		}
		update_option( 'braintree_for_woocommerce_version', array (
				'currentVersion' => bt_manager()->version, 
				'previousVersions' => $previousVersions 
		) );
		
		do_action( 'bfwc_admin_after_plugin_update' );
	}

	public static function update_notice()
	{
		$message = sprintf( __( 'Thank you for updating Braintree For WooCommerce to version %s.', 'braintree-payments' ), bt_manager()->version );
		echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
	}

	public static function get_admin_update_notice( $plugin_data, $response )
	{
		if ( $response && property_exists( $response, 'upgrade_notice' ) ) {
			echo '<div class="" style="background-color: #FFF2A7; padding: 12px 24px;">' . $response->upgrade_notice . '<div style="margin-top: 10px"><a href="' . $response->package . '">' . __( 'Direct Download', 'braintree-payments' ) . '</div></div>';
		}
	}

	/**
	 * Check to see if an update of the plugin is available.
	 *
	 * @param array $plugins        	
	 */
	public static function maybe_update_plugin( $plugins )
	{
		global $bfwc_pro_update_plugin;
		
		$plugin_name = bt_manager()->plugin_name();
		
		// exit since no update is needed.
		if ( $bfwc_pro_update_plugin === false ) {
			return $plugins;
		}
		if ( $bfwc_pro_update_plugin ) {
			$plugins->response [ $plugin_name ] = $bfwc_pro_update_plugin;
			return $plugins;
		}
		
		$args = array (
				'slm_action' => 'plugin_version_check', 
				'plugin_version' => bt_manager()->version 
		);
		$response = bt_manager()->execute_curl( $args );
		
		// result success and package is populated so set update info.
		if ( $response [ 'result' ] === 'success' ) {
			if ( version_compare( bt_manager()->version, $response [ 'version' ], '<' ) ) {
				$bfwc_pro_update_plugin = ( object ) array (
						'plugin' => bt_manager()->plugin_name(), 
						'new_version' => $response [ 'version' ], 
						'package' => $response [ 'package' ], 
						'url' => 'https://wordpress.org/plugins/woo-payment-gateway/', 
						'slug' => bt_manager()->slug_name(), 
						'upgrade_notice' => $response [ 'upgrade_notice' ] 
				);
				$plugins->response [ $plugin_name ] = $bfwc_pro_update_plugin;
				
				$message = sprintf( __( 'Version %s of Braintree For WooCommerce Pro is available.', 'braintree-payments' ), $response [ 'version' ] );
				
				$update_notices = get_option( 'bfwc_update_email', array () );
				
				// maybe send update email to admin.
				if ( ! in_array( $bfwc_pro_update_plugin->new_version, $update_notices ) ) {
					wp_mail( get_option( 'admin_email' ), __( 'Braintree For WooCommerce Upgrade Notice', 'braintree-payments' ), $message );
					$update_notices [] = $bfwc_pro_update_plugin->new_version;
					update_option( 'bfwc_update_email', $update_notices );
				}
			}else{
				$bfwc_pro_update_plugin = false;
			}
		} else {
			$bfwc_pro_update_plugin = false;
		}
		if ( ! empty( $response [ 'registered_domain' ] ) ) {
			update_option( 'bfwc_registered_domain', $response [ 'registered_domain' ] );
		}
		return $plugins;
	}
}
Braintree_Gateway_Update::init();