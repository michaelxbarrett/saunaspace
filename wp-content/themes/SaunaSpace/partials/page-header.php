<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>
<div id="post" class="page-bg-style jumbotron" style="background-image: url('<?php echo $thumb['0'];?>'); background-size: cover;">
  <div class="container">
    <h1><?php the_title(); ?></h1>
  </div>
</div>
