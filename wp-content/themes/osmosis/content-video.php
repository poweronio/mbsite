<?php
/**
 * The Video Post Type Template
 */
?>

<?php
if ( is_singular() ) {
	$grve_disable_media = grve_post_meta( 'grve_disable_media' );
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'grve-single-post' ); ?> itemscope itemType="http://schema.org/BlogPosting">
		<?php
			if ( 'yes' != $grve_disable_media ) {
		?>
		<div id="grve-single-media">
			<?php grve_print_post_video(); ?>
		</div>
		<?php
			}
		?>
		<div id="grve-post-content">
			<?php grve_print_post_header_title( 'content' ); ?>
			<?php grve_print_post_single_meta(); ?>
			<?php the_content(); ?>
		</div>

	</article>

<?php
} else {
	$grve_post_class = grve_get_post_class();
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( $grve_post_class ); ?> itemscope itemType="http://schema.org/BlogPosting">
		<?php grve_print_post_video(); ?>
		<div class="grve-post-content">

			<?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><h4 class="grve-post-title" itemprop="name headline">', '</h4></a>' ); ?>

			<div class="grve-post-meta">
				<?php grve_print_post_author_by(); ?>
				<?php grve_print_post_date(); ?>
				<?php grve_print_like_counter(); ?>
			</div>
			<?php grve_print_post_excerpt(); ?>
		</div>
	</article>

<?php
}
?>



