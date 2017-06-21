<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <div class="page-header-2">
      <div class="container">
        <h1>Articles</h1>
      </div>
    </div>
    <div class="page-blog">
      <div class="container" role="document">
        <div class="content row">
          <div>
            <?php include Wrapper\template_path(); ?>
          </div><!-- /.main -->
        </div><!-- /.content -->
      </div><!-- /.wrap -->
    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
      wp_footer();
    ?>
  </div>
  </body>
</html>
