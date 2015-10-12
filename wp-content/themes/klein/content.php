<?php
/**
 * Standard Post Layout
 *
 * @package klein.
 */ 
?>


	<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'blog-list' ) ); ?>>
		
		<?php if (has_post_thumbnail()){ ?>
			<h3 class="entry-title">
				<a class="primary" title="<?php echo esc_attr(the_title()); ?>" href="<?php echo esc_url(the_permalink()); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
			<div class="row entry-content archive-excerpt">
				<div class="col-sm-8">
					<?php the_excerpt(); ?>
				</div>
				<div class="col-sm-4">
					<?php echo the_post_thumbnail('klein-thumbnail'); ?>
				</div>
			</div>
		<?php } else { ?>
			<h3 class="entry-title">
				<a class="primary" title="<?php echo esc_attr(the_title()); ?>" href="<?php echo esc_url(the_permalink()); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
			<div class="entry-content archive-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php } ?>
		<div class="entry-meta sr-only">
			<?php klein_entry_meta(); ?>
		</div>
		<!--author-->
		<div class="archive-entry-author">
			<div class="author-avatar pull-left">
				<?php $author_link = ''; ?>
				<?php if (function_exists('bp_core_fetch_avatar')) { ?>
					<?php $author_link = bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ;?>
					<a title="<?php _e('View Profile', 'klein'); ?>" href="<?php echo esc_url($author_link); ?>">
						<?php echo bp_core_fetch_avatar( array( 'type' => 'full', 'item_id' => $post->post_author ) ); ?>
					</a>
				<?php } else { ?>
					<?php $author_link = get_author_posts_url( get_the_author_meta( 'ID' ) ) ;?>
					<a title="<?php _e('View Posts', 'klein'); ?>" href="<?php echo esc_url($author_link); ?>">
						<?php echo get_avatar($post->post_author, 32); ?>
					</a>
				<?php } ?>
				
			</div>
			<div class="author-meta pull-left">
				<a class="author-name block" title="<?php _e('View Profile', 'klein'); ?>" href="<?php echo esc_url($author_link); ?>">
					<span class="by-label smaller"><?php _e('by', 'klein'); ?></span> <span class="smaller"><?php the_author_link(); ?></span>
				</a>
				<span class="smaller archive-date block"><?php the_time( get_option( 'date_format' ) ); ?></span>
			</div>

			<div class="readmore">
				<a href="<?php echo esc_url(the_permalink()); ?>" title="<?php _e('Continue Reading', 'klein'); ?>" class="btn btn-primary">
					<?php _e('Continue Reading', 'klein'); ?>
				</a>
			</div>

			<div class="clearfix"></div>
		</div>
		<!--author end-->
	</article><!-- #post-## -->