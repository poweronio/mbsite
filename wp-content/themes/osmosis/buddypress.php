<?php get_header(); ?>

<?php the_post(); ?>


<?php
add_action('bcp_coverphoto_style', 'klein_cover_photo');

function klein_cover_photo()
{
	$cover_photo_url = klein_get_cover_photo_src();

	if (!empty($cover_photo_url)) 
	{
		echo $cover_photo_url = sprintf('style="background-image: url(%s);"', $cover_photo_url); 
	} 

	return;
}

/**
 * Returns the cover photo url
 * @return string the cover photo url
 */
function klein_get_cover_photo_src()
{
	if (!function_exists('bcp_get_cover_photo')) { return; }

	$item_id = bp_displayed_user_id();
	$item_type = 'user';

	if (bp_is_group()) {
		$item_id = bp_get_group_id();
		$item_type = 'group';
	}

	$args = array(
		'type' => $item_type,
		'object_id'=> $item_id,
	); 

	$cover_photo_url = esc_url(bcp_get_cover_photo($args));

	return $cover_photo_url;
}
?>

<?php $cover_photo = klein_get_cover_photo_src(); ?>



<?php
	if ( 'yes' == grve_post_meta( 'grve_disable_content' ) ) {
		get_footer();
	} else {
?>

		<div id="grve-main-content" class="custombb">

			

			<?php
				$page_nav_menu = grve_post_meta( 'grve_page_navigation_menu' );

			?>

			<div class="grve-container grve-right-sidebar">

				<!-- Content Area -->
				<div id="grve-content-area" class="custombuddy">

                    <?php if (!empty($cover_photo)) { ?>
			<div id="buddycover" class="cover-photo-parallax-container hidden-xs" <?php echo $cover_photo_url = sprintf('style="background-image: url(%s)"', $cover_photo);  ?>")">
<!--				<img id="cover-photo" src="<?php echo $cover_photo; ?>" alt="<?php echo __('Cover Photo', 'klein'); ?>" />-->
			</div>
		<?php } ?>
					<!-- Content -->
					<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

						<?php the_content(); ?>
                        
                        

					</div>
					<!-- End Content -->

					<?php if ( grve_visibility( 'page_comments_visibility' ) ) { ?>
						<?php comments_template(); ?>
					<?php } ?>

				</div>

			</div>
            		<!-- Sidebar -->
		
		<!-- End Sidebar -->

		</div>

	<?php get_footer(); ?>

<?php
	}
?>