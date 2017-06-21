<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woothemes.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @package     WC-CSV-Import-Suite/Admin
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce CSV Import Suite Admin
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Admin {


	/**
	 * Admin class constructore
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// register importers
		add_action( 'admin_init', array( $this, 'register_importers' ) );

		// add the menu item
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// render any admin notices
		add_action( 'admin_notices', array( $this, 'add_admin_notices' ), 10 );

		// filter hidden order item meta in edit order screen
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );
	}


	/**
	 * Register the customer and order importers
	 *
	 * @since 3.0.0
	 */
	public function register_importers() {

		register_importer( 'woocommerce_customer_csv',
							'WooCommerce Customer (CSV)',
							__( 'Import <strong>customers</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		register_importer( 'woocommerce_order_csv',
							'WooCommerce Order (CSV)',
							__( 'Import <strong>orders</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		register_importer( 'woocommerce_coupon_csv',
							'WooCommerce Coupon (CSV)',
							__( 'Import <strong>coupons</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		// load importers early on action/POST requests, to support
		// `redirect after post` type pattern.
		// This allows processing action/POST requests before any output is sent to the
		// buffer and also using wp_redirect(). This, however, means that all the
		// importers _must_ use the `redirect after post` pattern.
		if ( isset( $_REQUEST['import'] ) && isset( $_REQUEST['action'] ) ) {
			$this->load_importer();
		}
	}


	/**
	 * Add a submenu item to the WooCommerce menu
	 *
	 * @since 3.0.0
	 */
	public function add_menu_link() {

		$menu_title = wc_csv_import_suite()->is_plugin_active( 'woocommerce-product-csv-import-suite.php' ) ? __( 'CSV Order Import Suite', 'woocommerce-csv-import-suite' ) : __( 'CSV Import Suite', 'woocommerce-csv-import-suite' );

		add_submenu_page(
			'woocommerce',
			__( 'CSV Import Suite', 'woocommerce-csv-import-suite' ),
			$menu_title,
			'manage_woocommerce',
			wc_csv_import_suite()->get_id(),
			array( $this, 'render_import_screen' )
		);

	}


	/**
	 * Include admin scripts
	 *
	 * @since 3.0.0
	 */
	public function load_styles_scripts() {

		// bail out if not on import page
		if ( ! isset( $_GET['import'] ) ) {
			return;
		}

		// Bail out on unsupported importer
		if ( ! wc_csv_import_suite()->get_importers_instance()->get_importer( $_GET['import'] ) ) {
			return;
		}

		// Load flot on progress page only
		if ( isset( $_GET['job_id'] ) && $_GET['job_id'] ) {
			wp_enqueue_script( 'flot' );
			wp_enqueue_script( 'flot-pie' );
		}

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
		wp_enqueue_style( 'wc-csv-import-suite-admin', wc_csv_import_suite()->get_plugin_url() . '/assets/css/admin/wc-csv-import-suite-admin.min.css', array( 'woocommerce_admin_styles' ), WC_CSV_Import_Suite::VERSION );

		wp_enqueue_script( 'wc-csv-import-suite-admin', wc_csv_import_suite()->get_plugin_url() . '/assets/js/admin/wc-csv-import-suite-admin.min.js', array( 'jquery', 'jquery-blockui' ), WC_CSV_Import_Suite::VERSION );

		wp_localize_script( 'wc-csv-import-suite-admin', 'wc_csv_import_suite', array(
			'preview_nonce'  => wp_create_nonce( 'get-csv-preview' ),
			'progress_nonce' => wp_create_nonce( 'get-import-progress' ),
			'type'           => isset( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : null,
			'i18n' => array(
				'show_details'             => esc_html__( 'View detailed results', 'woocommerce-csv-import-suite' ),
				'hide_details'             => esc_html__( 'Hide detailed results', 'woocommerce-csv-import-suite' ),
				'import_complete'          => esc_html__( 'Import complete.', 'woocommerce-csv-import-suite' ),
				'skipped_or_failed_lines'  => esc_html__( 'Some lines were skipped or failed to import. See below for details.', 'woocommerce-csv-import-suite' ),
				'unexpected_error_message' => esc_html__( 'Something unexpected happened while importing. Your import may or may have not completed. Please check your site error log for possible clues as to what may have happened.', 'woocommerce-csv-import-suite' ),
			),
		) );

	}


	/**
	 * Add import finished notices for the current user
	 *
	 * @since 3.1.0
	 */
	public function add_admin_notices() {

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$import_notices   = get_user_meta( $user_id, '_wc_csv_import_suite_notices', true );
		$is_import_screen = isset( $_GET['import'] ) && wc_csv_import_suite()->get_importers_instance()->get_importer( $_GET['import'] );

		if ( ! empty( $import_notices ) ) {

			foreach ( $import_notices as $import_id ) {

				$message_id                      = 'wc_csv_import_suite_finished_' . $import_id;
				$is_current_import_progress_page = isset( $_GET['job_id'] ) && $_GET['job_id'] === $import_id;

				if ( $is_current_import_progress_page || wc_csv_import_suite()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $user_id ) ) {

					// dismiss any completed/finished notices about the current import
					wc_csv_import_suite()->remove_import_finished_notice( $import_id, $user_id );

				} elseif ( ! $is_import_screen ) {

					// only display notices when not on any import screen, as to reduce confusion & clutter
					list( $message, $notice_class ) = $this->get_import_finished_message( $import_id );

					wc_csv_import_suite()->get_admin_notice_handler()->add_admin_notice( $message, $message_id, array( 'always_show_on_settings' => false, 'notice_class' => $notice_class ) );
				}
			}
		}
	}


	/**
	 * Get import finished message
	 *
	 * @since 3.1.0
	 * @param string $import_id
	 * @return array
	 */
	private function get_import_finished_message( $import_id ) {

		$import = wc_csv_import_suite()->get_background_import_instance()->get_job( $import_id );

		if ( ! $import ) {
			return '';
		}

		$filename            = basename( $import->file_path );
		$import_progress_url = admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) );

		if ( 'completed' === $import->status ) {

			if ( $import->options['dry_run'] ) {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Dry run of file %s is complete!', 'woocommerce-csv-import-suite' ), $filename );
			} else {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Importing file %s is complete!', 'woocommerce-csv-import-suite' ), $filename );
			}

			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			$message  .= ' ' . sprintf( __( 'You can see the import results in the %1$simport progress page%2$s', 'woocommerce-csv-import-suite' ), '<a href="' . $import_progress_url . '">', '</a>' );

			if ( $import->options['dry_run'] ) {
				$message .= __( ', then run your live import', 'woocommerce-csv-import-suite' );
			}

			// finally, full stop this bad boy
			$message .= '.';

			$notice_class = 'updated';

		} elseif ( 'failed' === $import->status ) {

			if ( $import->options['dry_run'] ) {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Dry run of file %s failed.', 'woocommerce-csv-import-suite' ), $filename );
			} else {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Importing file %s failed.', 'woocommerce-csv-import-suite' ), $filename );
			}

			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			$message .= ' ' . sprintf( __( 'Additional details may be found in the CSV Import %1$slogs%2$s.', 'woocommerce-csv-import-suite' ), '<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">', '</a>' );

			$notice_class = 'error';

		}

		if ( ! $message ) {
			return '';
		}

		return array( $message, $notice_class );
	}


	/**
	 * Render the admin page which includes links to the documentation,
	 * sample import files, and buttons to perform the imports
	 *
	 * @since 3.0.0
	 */
	public function render_import_screen() {

		$import_progress_url = $this->get_progress_url_for_current_import();

		include( 'views/html-import-screen.php' );
	}


	/**
	 * Load an importer and start processing the import queue
	 *
	 * @since 3.0.0
	 */
	public function load_importer() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			return;
		}

		$type     = isset( $_REQUEST['import'] ) ? esc_attr( $_REQUEST['import'] ) : null;
		$importer = wc_csv_import_suite()->get_importers_instance()->get_importer( $type );

		if ( $importer ) {
			$importer->dispatch();
		}
	}


	/**
	 * Hide _original_order_item_id meta on edit order screen
	 *
	 * @since 3.0.0
	 * @param array $hidden
	 * @return array
	 */
	public function hidden_order_itemmeta( $hidden ) {

		$hidden[] = '_original_order_item_id';

		return $hidden;
	}


	/**
	 * If there are jobs in process, return the URL for the progress screen.
	 *
	 * @since 3.1.1
	 * @return string progress screen URL if jobs are processing / queued
	 */
	public function get_progress_url_for_current_import() {

		// get any jobs that haven't completed or failed yet
		$args = array(
			'status' => array( 'queued', 'processing' ),
		);

		$imports      = wc_csv_import_suite()->get_background_import_instance()->get_jobs( $args );
		$progress_url = '';

		if ( empty( $imports ) ) {
			return $progress_url;
		}

		// we don't break the foreach here since we want to prefer processing jobs if they exist,
		// so we check all jobs, but set the progress URL for the first queued job in case none are processing
		foreach ( $imports as $import ) {

			// direct users to the processing jobs if available
			if ( 'processing' === $import->status ) {
				return admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) . '&block_new_import=yes' );
			}

			// otherwise we'll use the progress screen for the first queued import instead
			elseif ( ! $import_progress_url ) {
				$progress_url = admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) . '&block_new_import=yes' );
			}

		}

		return $progress_url;
	}

}
