<?php
/**
 * @since 2.6.9
 * @author Payment Plugins
 *
 */
class BFWC_Admin_Notices
{
	private static $notices = array (
			'template_version_notice' 
	);

	public static function init()
	{
		add_action( 'admin_init', __CLASS__ . '::add_notices' );
	}

	public static function add_notices()
	{
		foreach ( self::$notices as $notice ) {
			add_action( 'admin_notices', __CLASS__ . '::' . $notice );
		}
	}

	public static function template_version_notice()
	{
		$theme_templates = self::get_template_files( bt_manager()->template_path() );
		$core_templates = self::get_core_templates( bt_manager()->plugin_path() . 'templates/' );
		$show_notice = false;
		if ( $theme_templates && $core_templates ) {
			foreach ( $theme_templates as $name => $file ) {
				if ( array_key_exists( $name, $core_templates ) ) {
					$theme_version = self::get_template_file_version( $file );
					$plugin_version = self::get_template_file_version( $core_templates [ $name ] );
					
					if ( ! $theme_version || ( $plugin_version && version_compare( $theme_version, $plugin_version, '<' ) ) ) {
						$show_notice = true;
						break;
					}
				}
			}
		}
		if ( $show_notice ) {
			$theme = wp_get_theme();
			$text = sprintf( __( 'Your theme (%1$s) contains outdated copies of some Braintree For WooCommerce templates.', 'braintree-payments' ), $theme [ 'Name' ] );
			printf( '<div class="notice notice-warning is-dismissible"><p><strong>%s</strong></p></div>', $text );
		}
	}

	public static function get_template_file_version( $file )
	{
		$version = 0;
		if ( file_exists( $file ) ) {
			$data = self::get_file_data( $file, array (
					'version' => 'version' 
			) );
			if ( $data ) {
				$version = isset( $data [ 'version' ] ) ? $data [ 'version' ] : $version;
			}
		}
		return $version;
	}

	public static function get_file_data( $file, $headers = array() )
	{
		$fp = fopen( $file, 'r' );
		
		$file_data = fread( $fp, 8192 );
		
		fclose( $fp );
		
		foreach ( $headers as $key => $regex ) {
			if ( preg_match( '/[\t\/*@]*' . $regex . '\s*([\w.]+)/', $file_data, $matches ) ) {
				$headers [ $key ] = $matches [ 1 ];
			} else {
				$headers [ $key ] = '';
			}
		}
		return $headers;
	}

	public static function get_template_files( $path )
	{
		$theme_path = false;
		$results = array ();
		
		if ( file_exists( get_stylesheet_directory() . '/' . $path ) ) {
			$theme_path = get_stylesheet_directory() . '/' . $path;
		} elseif ( file_exists( get_template_directory() . '/' . $path ) ) {
			$theme_path = get_template_directory() . '/' . $path;
		}
		if ( $theme_path ) {
			self::scan_directory( $theme_path, $results );
		}
		return $results;
	}

	public static function get_core_templates( $path )
	{
		$results = array ();
		self::scan_directory( $path, $results );
		return $results;
	}

	/**
	 *
	 * @param string $path        	
	 * @param array $results        	
	 */
	public static function scan_directory( $path, &$results, $dir = '' )
	{
		$files = @scandir( $path );
		
		if ( $files ) {
			foreach ( $files as $file ) {
				if ( ! in_array( $file, array (
						".", 
						".." 
				) ) ) {
					if ( is_dir( $path . DIRECTORY_SEPARATOR . $file ) ) {
						self::scan_directory( $path . DIRECTORY_SEPARATOR . $file, $results, $file );
					} else {
						$name = preg_match( '/[\w-_]*\.php$/', $file, $matches ) ? $matches [ 0 ] : $file;
						$results [ $dir . '/' . $name ] = $path . DIRECTORY_SEPARATOR . $file;
					}
				}
			}
		}
	}
}
BFWC_Admin_Notices::init();