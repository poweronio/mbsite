<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package klein
 */

if ( ! function_exists( 'klein_content_nav' ) ) :

/**
 * Display navigation to next/previous pages when applicable
 */
function klein_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>
	<?php 
		wp_link_pages(array(  
			'before' => '<div class="row page-link">' . 'Pages:',  
			'after' => '</div>'  
		)) 
	?>
	
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
	<?php if (is_single()) : // navigation links for single posts ?>
		<?php $post = get_next_post(); ?>
		<?php // next reading ?>
		<?php if (!empty($post)) { ?>
			<?php setup_postdata($post); ?>
			<div class="text-alignrights next-posts">
				<h3 class="h5">
					<em><?php _e('Next Reading', 'klein'); ?></em>
				</h3>
				<h3>
					<a href="<?php echo esc_url(get_permalink( $post->ID )); ?>"><?php the_title(); ?></a>
				</h3>
				<?php the_excerpt(); ?>
			</div>
		<?php } // end !empty($post) #next reading?>
		<?php wp_reset_postdata(); ?>

		<?php // previous post ?>
		<?php $post = get_previous_post(); ?>
		<?php if (!empty($post)) { ?>
			<?php setup_postdata($post); ?>
			<div class="text-alignrights prev-posts">
				<h3 class="h5">
					<em><?php _e('Previous Reading', 'klein'); ?></em>
				</h3>
				<h3>
					<a href="<?php echo esc_url(get_permalink( $post->ID )); ?>"><?php the_title(); ?></a>
				</h3>
				<?php the_excerpt(); ?>
			</div>
		<?php } ?>
		<?php wp_reset_postdata(); ?>
	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next-link">
				<?php previous_posts_link( __( '<div class="pull-right nav-next">'.__('Newer Posts','klein').' <span class="meta-nav"><i class="fa fa-angle-right"></i></span></div>', 'klein' ) ); ?>
			</div>
		<?php endif; ?>

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous-link">
				<?php next_posts_link( __( '<div class="pull-left nav-previous"><span class="meta-nav"><i class="fa fa-angle-left"></i></span> '.__('Older Posts','klein').' </div>', 'klein' ) ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // klein_content_nav

if ( ! function_exists( 'klein_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function klein_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'klein' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'klein' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			
			<div class="row">

				<div class="col-sm-2 col-xs-2 comment-author">
				<!--avatar -->
						<?php if (0!= $args['avatar_size']) { ?>
							<?php echo get_avatar( $comment, 100 ); ?>
						<?php } ?>
				</div>

				<div class="col-sm-10 col-xs-10">
					<div class="comment-meta">
						<div class="comment-author vcard">
							<!--author name-->
							<?php printf( __( '%s ', 'klein' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
							<!-- time -->
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">

								<time datetime="<?php comment_time( 'c' ); ?>">
									<span class="glyphicon glyphicon-time"></span>
									<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'klein' ), get_comment_date(), get_comment_time() ); ?>
								</time>
							</a>
							<!-- edit link -->
							<?php edit_comment_link( __( '<span class="glyphicon glyphicon-pencil"></span>', 'klein' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .comment-author -->

						<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'klein' ); ?></p>
						<?php endif; ?>
					</div><!-- .comment-meta -->

					<div class="comment-content">
						<?php comment_text(); ?>
					</div><!-- .comment-content -->

					<div class="reply">
						
						<em>
							<span class="glyphicon glyphicon-share-alt"></span>
						<?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</em>
					</div><!-- .reply -->

				</div>

			</div><!--row-->
			
		</article><!-- .comment-body -->

	<?php
	endif;
}
endif; // ends check for klein_comment()

if ( ! function_exists( 'klein_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function klein_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'klein_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

if ( ! function_exists( 'klein_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function klein_posted_on( $echo = true ) {
	
	global $post;
	
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= ' and was updated on <time class="updated" datetime="%3$s">%4$s</time>';
	} else {
		$time_string .= ' <span class="sr-only">and was updated on <time class="updated" datetime="%3$s">%4$s</span></time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);
	
	$entry_meta = sprintf( __( '<span class="posted-on">%1$s</span><span class="byline"> / %2$s</span>', 'klein' ),
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn" href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'klein' ), get_the_author() ) ),
			esc_html( get_the_author_meta( 'display_name', $post->post_author ) )
		)
	);
	
	if( $echo ){
		echo $entry_meta;
	}else{		
		return $entry_meta;
	}
	
}
endif;

/**
 * Returns true if a blog has more than 1 category
 */
function klein_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so klein_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so klein_categorized_blog should return false
		return false;
	}
}



/**
 * Shows entry meta of a post inside the 'loop'
 */
if (!function_exists('klein_entry_meta')) {
function klein_entry_meta(){
?>
	<div class="entry-meta">
		
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'klein' ) );
	
			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'klein' ) );
	
			// always show the date and the author
			_e( sprintf( 'This entry was published on %s. ', klein_posted_on( false ) ), 'klein' );
			
			if ( ! klein_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					$meta_text = __( 'Tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'klein' );
				} else {
					$meta_text = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'klein' );
				}
	
			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( 'Posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'klein' );
				} else {
					$meta_text = __( 'Posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'klein' );
				}
	
			} // end check for categories on this blog
			
			printf(				
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink(),
				the_title_attribute( 'echo=0' )
			);
		?>
	
		<?php edit_post_link( __( 'Edit', 'klein' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-meta -->
<?php
}
}

if (!function_exists('klein_the_audio')) {
function klein_the_audio(){
	
	$audio_url = get_post_meta(get_the_ID(), '_format_audio_embed', true);
	
	if (!empty($audio_url)) {
		$audio_shortcode = sprintf('[embed width="800" height="120"]%s[/embed]', esc_url($audio_url));
		echo apply_filters('the_content', $audio_shortcode);
	}

	return;
}
}
/*
 * Video Post Format
 */
if( !function_exists( 'klein_post_formats_video' ) ){
function klein_post_formats_video( $height = '275', $width ='100%' )
{
	global $post;

	$video_url = get_post_meta($post->ID, '_format_video_embed', true);
		
	if (!empty($video_url)) {
		$video_shortcode = sprintf('[embed width="942"]%s[/embed]', esc_url($video_url));
			echo apply_filters('the_content', $video_shortcode);
	}
} // end klein_post_formats_video
}// !function_exists

/**
 * Check wheter the current page is blog
 * @return bool
 */
 
if( !function_exists( 'klein_is_blog' ) ){
	function klein_is_blog() {
	 
		global $post;
		
		// get the post type of the current page
		$type = get_post_type( $post );
		// return bool
		return (
			( is_home() || is_archive() || is_single() )
			&& ($type == 'post')
		) ? true : false ;
		
	}
} 

/**
 * Displays user info in loop
 */
if (!function_exists( 'klein_author' )) {
	
function klein_author() {

	$author_profile = ot_get_option('author_profile', 'on');

	// enable or disable sharing
	if ('on' !== $author_profile) {
		return;
	}
	
	global $post;
	?>
	<h3><?php _e('The Author', 'klein'); ?></h3>
	<div class="row author-about">
		<div class="col-sm-2 col-xs-3">
			<?php if (function_exists( 'bp_core_fetch_avatar' )) { ?>
				<?php echo bp_core_fetch_avatar( array( 'type' => 'full', 'item_id' => $post->post_author ) ); ?>
			<?php } else { ?>
				<div class="blog-author-avatar no-bp"><?php echo get_avatar( $post->post_author, 75 ); ?></div>
			<?php } ?>
		</div>
		<div class="col-sm-10 col-xs-9">
			<h5>
				<span class="author vcard">
					<a class="url fn" href="<?php echo esc_url( get_author_posts_url(get_the_author_meta('ID'))); ?>">
						<?php the_author_meta( 'display_name' ); ?>
					</a>
				</span>	
			</h5>
			<?php the_author_meta('description', $post->author_id); ?> 
		</div>
	</div>
	<?php
	}
}
/**
 *
 * klein_is_blog()
 *
 * Check if current page is blog
 * returns true otherwhise false
 *
 * @package klein
 * @return bool
 */
 
function klein_is_blog() {
	global  $post;
	$posttype = get_post_type($post );
	return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
}

/**
 * 
 * klein_sidebar_left()
 *
 * Renders the sidebar. Check for page settings and fallbacks
 * Specific section default sidebar
 *
 * @package klein
 * 
 */
if( !function_exists( 'klein_sidebar_left' ) ){ 

	function klein_sidebar_left( $fallback = 'sidebar-2' ){

		global $post;
		 
		$sidebar_meta = get_post_meta( $post->ID, 'klein_sidebar_left', true ); 
		do_action( 'before_sidebar_left' ); 
		
		if( !empty( $sidebar_meta ) ){
				dynamic_sidebar( $sidebar_meta ); 
			}else{ 
				dynamic_sidebar( $fallback ); 
			}
			
		return;
	} 
}

/**
 * 
 * klein_sidebar_right()
 *
 * Renders the sidebar. Check for page settings and fallbacks
 * Specific section default sidebar
 *
 * @package klein
 * 
 */
if( !function_exists( 'klein_sidebar_right' ) ){ 

	function klein_sidebar_right( $fallback = 'sidebar-1' ){

		global $post;
		
		$sidebar_meta = $fallback;

		if (!empty($post) && is_object($post)) { 
			$sidebar_meta = get_post_meta($post->ID, 'klein_sidebar', true); 
		}

		do_action( 'before_sidebar' ); 
		
		if (!empty( $sidebar_meta )) {
			dynamic_sidebar( $sidebar_meta ); 
		} else { 
			dynamic_sidebar($fallback); 
		} 
		
		return;
	} 
}

/**
 * Displays user updates in a dropdown
 */
if( !function_exists( 'klein_user_nav') ){
	function klein_user_nav(){
		global $current_user;
		
		// replaced deprecated tempalte tag 'bp_core_get_notifications_for_user'
		// @since 1/16/2014
		
		if( function_exists( 'bp_notifications_get_notifications_for_user' ) ){
		?>
		<?php $notifications = bp_notifications_get_notifications_for_user( get_current_user_id() ); ?>
	
			<section id="klein-top-updates">
				<a id="klein-top-updates-btn" class="btn btn-primary" href="#" title="<?php _e('Updates','klein'); ?>">
					<i class="glyphicon glyphicon-chevron-down"></i> 
					<?php if( !empty( $notifications ) ){ ?>
						<span id="klein-top-updates-badge"><?php echo count( $notifications ); ?></span>
					<?php } ?>	
				</a>
				<nav id="klein-top-updates-nav">
					<ul class="pull-right bp-klein-dropdown-menu" role="menu" aria-labelledby="global-updates">
						<?php do_action( 'klein_before_user_nav' ); ?>
						<li id="klein-user-nav-user-profile">
							<a href="<?php echo bp_core_get_user_domain( $current_user->ID );?>">
								<?php echo get_avatar( $current_user->ID, 32 ); ?>
								<span>
									<?php echo $current_user->display_name; ?>
								</span>
							</a>
						</li>
						<?php if( !empty( $notifications ) ){ ?>
							<?php foreach( $notifications as $notification ){ ?> 
								<li role="presentation">
									<?php
										// check if the format of notification is array
										// assume array has [text] and [link] element
										//
										// @patch 1/16/2014
										// @fix bp update
									?>
									<?php if( is_array( $notification ) ){ ?>
									
										<?php $notification_text = !empty( $notification['text'] ) ? $notification['text'] : ''; ?>
										<?php $notification_link = !empty( $notification['link'] ) ? $notification['link'] : ''; ?>
										
										<a title="<?php echo esc_attr( $notification_text ); ?>" href="<?php echo esc_url( $notification_link ); ?>">
											<?php echo esc_attr( $notification_text ); ?>
										</a>
										
									<?php }else{ ?>
											<?php echo $notification; ?>
									<?php } ?>
								</li>
							<?php } ?>
						<?php } // !empty( $notifications )?>
						<?php if( function_exists('bp_loggedin_user_domain') ){ ?>
							<?php if( bp_is_active( 'settings' ) ){ ?>
								<li role="presentation">
									<a href="<?php echo bp_loggedin_user_domain(); ?>profile/edit" title="<?php _e( 'Edit Profile','klein' ); ?>">
										<span class="fa fa-edit"></span>
										<?php _e( 'Edit Profile', 'klein' ); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="<?php echo bp_loggedin_user_domain(); ?>profile/change-avatar/" title="<?php _e( 'Change Avatar','klein' ); ?>">
										<span class="fa fa-user-secret"></span>
										<?php _e( 'Change Photo', 'klein' ); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="<?php echo bp_loggedin_user_domain(); ?>settings" title="<?php _e( 'Settings','klein' ); ?>">
										<span class="glyphicon glyphicon-cog"></span>
										<?php _e( 'Settings', 'klein' ); ?>
									</a>
								</li>
							<?php } ?>
						<?php } ?>	
							<li role="presentation">
								<a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e( 'Logout','klein' ); ?>">
									<i class="glyphicon glyphicon-log-out"></i> <?php _e( 'Logout','klein' ); ?>
								</a>
							</li>
						<?php do_action( 'klein_after_user_nav' ); ?>
					</ul>	
				</nav>
			</section>
			
		
		<?php 
		} // end function_exists('bp_core_get_notifications_for_user')
	} // end function klein_user_updates()
} // end !function_exists( 'klein_user_updates')

/**
 * klein_has_rev_slider
 * 
 * Checks whether the slider revolution is available for display
 * returns true if current template is front-page-rev-slider.php
 * and there is a rev-slider available on the settings
 * 
 * @package klein
 *
 */

if( !function_exists( 'klein_has_rev_slider' ) ){
	function klein_has_rev_slider(){
		if( !function_exists( 'putRevSlider' ) ){
			return false;
		}
		$current_template =	basename( get_page_template() ); 
		$rev_slider_page_template_names = array( 'front-page-rev-slider.php', 'front-page-rev-slider-content.php' );
		$rev_slider_id = ot_get_option( 'front_page_slider_id', '' );
		
		if( empty( $rev_slider_id ) ){
			return false;
		};
		
		if( in_array( $current_template, $rev_slider_page_template_names ) ){
			return true;
		}else{
			return false;
		}
	}
}

/**
 * Flush out the transients used in klein_categorized_blog
 */
 
function klein_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}

add_action( 'edit_category', 'klein_category_transient_flusher' );
add_action( 'save_post',     'klein_category_transient_flusher' );

if (!function_exists('the_content_sharer')) {

function the_content_sharer() {

	$sharer = ot_get_option('sharer', 'on');

	// enable or disable sharing
	if ('on' !== $sharer) {
		return;
	}
?>
<?php $permalink = get_permalink(get_the_ID()); ?>
<?php $fb_url = sprintf('http://facebook.com/sharer/sharer.php?u=%s', $permalink); ?>
<?php $twitter_request_url = sprintf('http://twitter.com/share?text=%s&amp;url=%s', get_the_title(), $permalink); ?>
<?php $google_url = sprintf('https://plus.google.com/share?url=%s', $permalink); ?>

<div class="content-sharer">
	<div class="row">
		<div class="col-sm-3">
			<div class="caption"><em><?php _e('Share On', 'klein'); ?>: </em></div>
		</div>
		<div class="col-sm-3">
			<a href="<?php echo $fb_url; ?>" class="article-sharer fb"><?php _e('Facebook', 'klein'); ?></a>
		</div>
		<div class="col-sm-3">
			<a href="<?php echo $google_url; ?>" class="article-sharer google"><?php _e('Google +', 'klein'); ?></a>
		</div>
		<div class="col-sm-3">
			<a href="<?php echo $twitter_request_url; ?>" class="article-sharer twitter"><?php _e('Twitter', 'klein'); ?></a>
		</div>
	</div>
</div>
<?php
return;
}
} //if (!function_exists('the_content_sharer'))

if(!function_exists('the_post_format_gallery')) {
function the_post_format_gallery() 
{ ?>
<?php $gallery_shortcode = get_post_meta(get_the_ID(), '_format_gallery', true); ?>
		<?php if (!empty($gallery_shortcode)) { ?>
			<?php $gallery_collection = explode(',', $gallery_shortcode); ?>
			<?php if (is_array($gallery_collection) && !empty($gallery_collection)) { ?>
				<?php $count = 0; ?>
				<?php $carousel_id = 'klein-gallery-' . get_the_ID(); ?>
				<div id="<?php echo $carousel_id; ?>" class="carousel slide klein-carousel" data-ride="carousel">
					<div class="carousel-inner" role="listbox">
						<?php foreach($gallery_collection as $item_id) { ?>
							<?php $item_url = wp_get_attachment_image_src( $item_id, 'klein-thumbnail-large' ); ?>
							<?php if($item_url) { ?>
								<?php $count++; ?>
								<div class="item<?php echo $count == 1 ? ' active': ''; ?>">
									<img src="<?php echo esc_url($item_url[0]); ?>" alt=""/>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
					<a class="left carousel-control" href="#<?php echo $carousel_id; ?>" role="button" data-slide="prev">
						<span class="fa fa-angle-left" aria-hidden="true"></span>
						<span class="sr-only"><?php _e('Previous', 'klein'); ?></span>
					</a>
					<a class="right carousel-control" href="#<?php echo $carousel_id; ?>" role="button" data-slide="next">
						<span class="fa fa-angle-right" aria-hidden="true"></span>
						<span class="sr-only"><?php _e('Next', 'klein'); ?></span>
					</a>
				</div>
				<!-- Controls -->
			<?php } ?>
		<?php } ?>	
<?php }
} ?>
<?php if (!function_exists('klein_social_links_icon')) { ?>
<?php function klein_social_links_icon() { ?>
<?php $social_links = ot_get_option('social_media', array()); ?>
	<?php if(!empty($social_links)) { ?>
		<ul class="no-list no-mg no-pd">
			<?php foreach ($social_links as $social_link) { ?>
				<?php if($social_link['href']) {?>
					<li>
						<a href="<?php echo esc_url($social_link['href']); ?>" title="<?php echo esc_attr($social_link['name']); ?>">
							<i class="fa <?php echo esc_attr($social_link['title']); ?>"></i>
						</a>
					</li>	
				<?php } ?>
			<?php } ?>
		</ul>	
	<?php } ?>
<?php } ?>
<?php } 

if (!function_exists('klein_login_register_link')) {?>
<?php function klein_login_register_link() { ?>
		<?php 
			$login_enabled = ot_get_option('enable_login', 'yes');
			$register_enabled = ot_get_option('enable_register', 'yes');
		?>
		<!-- login -->
		<?php if ('on' === $login_enabled) { ?>
			<a data-toggle="modal" id="klein-login-btn" class="btn btn-primary" href="#klein_login_modal" title="<?php _e( 'Login', 'klein' ); ?>"><i class="icon-lock"></i> <?php _e( 'Login', 'klein' ); ?></a>
			<!-- the modal -->
			<?php klein_the_login_modal(); ?>
		<?php } ?>
		<!-- register -->

		<?php if ('on' === $register_enabled) { ?>
			<?php echo str_replace( '<a', '<a id="klein-register-btn" title="'.__('Register','klein').'" class="btn btn-primary" ', wp_register('', '', false)); ?>
		<?php } ?>
		
	<?php return; ?>
<?php } ?>
<?php } ?>
