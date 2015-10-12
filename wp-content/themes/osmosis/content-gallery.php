<?php
/**
 * The Gallery Post Type Template
 */
?>

<?php
if ( is_singular() ) {
	$grve_disable_media = grve_post_meta( 'grve_disable_media' );
	$slider_items = grve_post_meta( 'grve_post_slider_items' );
	$gallery_mode = grve_post_meta( 'grve_post_type_gallery_mode', 'gallery' );
	$gallery_image_mode = grve_post_meta( 'grve_post_type_gallery_image_mode' );
	$image_size_slider = 'grve-image-large-rect-horizontal';
	if ( 'resize' == $gallery_image_mode ) {
		$image_size_slider = 'grve-image-fullscreen';
	}
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('grve-single-post'); ?> itemscope itemType="http://schema.org/BlogPosting">
<?php
		if ( !empty( $slider_items ) && 'yes' != $grve_disable_media ) {
?>
			<div id="grve-single-media">
				<?php grve_print_gallery_slider( $gallery_mode, $slider_items, $image_size_slider  ); ?>
			</div>
<?php
		}
?>
		<div id="grve-post-content">
			<?php grve_print_post_header_title( 'content' ); ?>
			<?php grve_print_post_single_meta(); ?>
			<div itemprop="articleBody">	
				<?php the_content(); ?>
			</div>
		</div>

	</article>

<?php
} else {
	$grve_post_class = grve_get_post_class();
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( $grve_post_class ); ?> itemscope itemType="http://schema.org/BlogPosting">
		<?php

			$slider_items = grve_post_meta( 'grve_post_slider_items' );
			$gallery_mode = grve_post_meta( 'grve_post_type_gallery_mode', 'gallery' );
			$gallery_image_mode = grve_post_meta( 'grve_post_type_gallery_image_mode' );
			$image_size_slider = 'grve-image-large-rect-horizontal';
			if ( 'resize' == $gallery_image_mode ) {
				$image_size_slider = 'grve-image-fullscreen';
			}
			if ( !empty( $slider_items ) ) {
				grve_print_gallery_slider( $gallery_mode, $slider_items, $image_size_slider  );
			}

		?>

		<div class="grve-post-content">
			<?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><h4 class="grve-post-title" itemprop="name headline">', '</h4></a>' ); ?>
			<div class="grve-post-meta">
				<?php grve_print_post_author_by(); ?>
				<?php grve_print_post_date(); ?>
				<?php grve_print_like_counter(); ?>
			</div>
			<div itemprop="articleBody">
				<?php grve_print_post_excerpt(); ?>
			</div>
		</div>

	</article>

<?php
}
?>



