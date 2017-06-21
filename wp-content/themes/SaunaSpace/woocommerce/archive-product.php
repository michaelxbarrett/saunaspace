<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
?>

<div class="container-fluid">
	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>
<div class="shop-top">
	<div class="row">
		<div class="col-md-7 shop-top-1">
			<span>THE<br>SAUNA<br>FOR<br>EVERY<br>BODY</span>
		</div>
		<div class="col-md-5 shop-top-grid">
			<div class="row">
				<div class="col-md-12 shop-top-2"></div>
			</div>
			<div class="row">
				<div class="col-md-6 shop-top-3"></div>
				<div class="col-md-6 shop-top-4"></div>
			</div>
		</div>
	</div>
</div>
<div class="container">


		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<div class="shop-header1">
				<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
			</div>
		<?php endif; ?>

		<div class="shop-main-products">
			<div class="row">
				<a href="<?php bloginfo('url'); ?>/shop/near-infrared-pocket-sauna/"><div class="col-md-6 shop-main-box shop-main-1">
					<div class="shop-main-box">
						<h4>NEAR INFRARED POCKET SAUNA</h4>
						<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><br>
					</div>
				</div></a>
				<a href="<?php bloginfo('url'); ?>/shop/near-infrared-sauna-shower-converter/"><div class="col-md-6 shop-main-box shop-main-3">
					<div class="shop-main-box">
						<h4>NEAR INFRARED SHOWER CONVERTER BUNDLE</h4>
						<i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><br>
					</div>
				</div></a>
			</div>
		</div>

		<div class="shop-items-title">
			<h2 class="header2">SAUNA COMPONENTS & ADD-ONS</h2>
		</div>
		<?php
			/**
			 * woocommerce_archive_description hook.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
		?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook.
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
</div>
</div>
