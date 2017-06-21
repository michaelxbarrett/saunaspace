<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="product-top-wrapper">
<div class="row">
<div id="column-wrap">
	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="col-md-5 main-right-col">
		<div class="summary entry-summary">

			<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_price - 25
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>

		</div>
	</div>
	</div><!-- .summary -->
</div>
</div>
	<div class="product-blurb">
		<p>HANDMADE IN THE <span>USA</span>  •  LIFETIME WARRANTY</p>
	</div>


<!--<div class="product-sec-3">
 <div class="container">
	 <h3 class="header2">REVIEWS</h3>
	 <div class="yotpo yotpo-reviews-carousel" 
		data-mode="most_recent" 
		data-type="product" 
		data-count="9" 
		data-show-bottomline="1" 
		data-autoplay-enabled="1" 
		data-autoplay-speed="3000" 
		data-show-navigation="1" 
		data-header-customisation-alignment="left" 
		data-background-color="transparent">&nbsp;</div>
	 </div> -->

<div class="product-reviews" style="background-color: #fff;">
<div class="container">
<br>
	<?php wc_yotpo_show_widget(); ?> 
</div>
</div>
<hr>

	<!-- <div class="in-the-box-wrapper">
		<div class="in-the-box">
			<div class="container">
				<div class="row">
					<div class="col-md-offset-1">

					</div>
				</div>
			</div>
		</div>
	</div> -->

	<div class="product-sec-2">
    <div class="product-sec-2-wrap container">
			<h2 class="header2">IN THE BOX</h2>
      <div class="product-tabs">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active col-md-2 col-xs-6">
            <a href="#frame" aria-controls="gallery" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_1'); ?>" alt="gallery" /></a>
          </li>
          <li role="presentation" class="col-md-2 col-xs-6">
            <a href="#mat" aria-controls="description" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_2'); ?>" alt="gallery" /></a>
          </li>
          <li role="presentation" class="col-md-2 col-xs-6">
            <a href="#stool" aria-controls="stackup" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_3'); ?>" alt="gallery" /></a>
          </li>
					<li role="presentation" class="col-md-2 col-xs-6">
						<a href="#lights" aria-controls="stackup" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_4'); ?>" alt="gallery" /></a>
					</li>
					<li role="presentation" class="col-md-2 col-xs-6">
						<a href="#cloth" aria-controls="stackup" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_5'); ?>" alt="gallery" /></a>
					</li>
					<li role="presentation" class="col-md-2 col-xs-6">
						<a href="#cloth2" aria-controls="stackup" role="tab" data-toggle="tab"><img class="img-responsive" src="<?php the_field('intheboxpic_-_6'); ?>" alt="gallery" /></a>
					</li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="frame">
						<div class="container">
								<?php the_field('inthebox_-_1'); ?>
						</div>
          </div>
          <div role="tabpanel" class="tab-pane" id="mat">
            <div class="container">
                <?php the_field('inthebox_-_2'); ?>
          	</div>
          </div>
          <div role="tabpanel" class="tab-pane" id="stool">
            <div class="container">
              <?php the_field('inthebox_-_3'); ?>
            </div>
          </div>
					<div role="tabpanel" class="tab-pane" id="lights">
            <div class="container">
              <?php the_field('inthebox_-_4'); ?>
            </div>
          </div>
					<div role="tabpanel" class="tab-pane" id="cloth">
            <div class="container">
              <?php the_field('inthebox_-_5'); ?>
            </div>
          </div>
					<div role="tabpanel" class="tab-pane" id="cloth2">
            <div class="container">
              <?php the_field('inthebox_-_6'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="product-sec-4-bg"></div>

<div class="product-sec-3">
		<div class="container">
			<h3 class="header2">PRODUCT DETAILS</h3>
      <div class="product-tabs">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active col-sm-4 col-xs-12">
            <a href="#features" aria-controls="gallery" role="tab" data-toggle="tab">FEATURES</a>
          </li>
          <li role="presentation" class="col-sm-4 col-xs-12">
            <a href="#materials" aria-controls="description" role="tab" data-toggle="tab">MATERIALS</a>
          </li>
          <li role="presentation" class="col-sm-4 col-xs-12">
            <a href="#specs" aria-controls="stackup" role="tab" data-toggle="tab">SPECIFICATIONS</a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="features">
            <div>
            <div class="row">
            <div class="col-md-offset-1 col-md-10">
              <div class="gallery-img">
								<?php the_field('features'); ?>
              </div>
            </div>
          </div>
          </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="materials">
            <div class="container">
            <div class="row">
              <div class="col-md-offset-1 col-md-10">
                <?php the_field('materials'); ?>
              </div>
            </div>
          </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="specs">
						<div class="row">
              <div class="col-md-offset-1 col-md-10">
                <?php the_field('specifications'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
		</div>
	</div>

	


	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>



<div class="product-sec-5-bg"></div>

<div class="product-sec-6">
	<div class="container">
		<h3>OPERATE YOUR SAUNASPACE <span>ANYWHERE</span> IN THE WORLD</h3>
		<div class="row">
			<div class="col-sm-6 product-sec-6-block">
				<img class="img-responsive" src="<?php bloginfo('url'); ?>/wp-content/themes/SaunaSpace/assets/images/footer-top-1.png" alt=""/>
				<h4>CHOOSE THE CORRECT LIGHT PANEL POWER CORD</h4>
			</div>
			<div class="col-sm-6 product-sec-6-block">
				<img class="img-responsive" src="<?php bloginfo('url'); ?>/wp-content/themes/SaunaSpace/assets/images/footer-top-2.png" alt=""/>
				<h4>PLUG IT IN AT YOUR DESIRED DESTINATION</h4>
			</div>
		</div>
	</div>
</div>

<div class="product-sec-7">
	<div class="container">
		<h3 class="header2">NEED MORE INFORMATION?</h3>
		<a href="/faqs/" class="btn btn-red">FREQUENTLY ASKED QUESTIONS</a>
	</div>
</div>
<meta itemprop="url" content="<?php the_permalink(); ?>" />
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
