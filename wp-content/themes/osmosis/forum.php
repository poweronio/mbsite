<?php get_header(); ?>
<?php the_post(); ?>
<div id="grve-main-content">

	<?php grve_print_header_title( 'forum' ); ?>
	<div class="grve-container <?php echo grve_sidebar_class( 'forum' ); ?>">
		<!-- Content -->
		<div id="grve-content-area">
		
			<!-- Content -->
			<div id="grve-forum-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php the_content(); ?>

			</div>
			<!-- End Content -->

		</div>
		<!-- End Content -->
		<?php grve_set_current_view( 'forum' ); ?>
		<?php get_sidebar(); ?>

	</div>
</div>
<?php get_footer(); ?>