<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
<?php if ( $background = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ) ) : ?>

    <div class="post-top-info" style="background: url('<?php echo $background[0]; ?>'); height: 600px; background-size: cover; background-repeat: none;">


<?php endif; ?>
</div>
      <div class="post-avatar">
        <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
      </div>
      <div class="post-titles">
        <div class="container">
      <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php get_template_part('templates/entry-meta'); ?>
      </div>
    </div>
    <div class="entry-content">
        <div class="container">
          <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <div class="container">
    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </div>
  </article>
<?php endwhile; ?>
