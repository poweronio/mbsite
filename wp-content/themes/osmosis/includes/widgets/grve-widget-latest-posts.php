<?php
/**
 * Plugin Name: Greatives Latest Posts
 * Description: A widget that displays latest posts.
 * @author		Greatives Team
 * @URI			http://greatives.eu
 */

add_action( 'widgets_init', 'grve_widget_latest_posts' );

function grve_widget_latest_posts() {
	register_widget( 'GRVE_Widget_Latest_Posts' );
}

class GRVE_Widget_Latest_Posts extends WP_Widget {

	function GRVE_Widget_Latest_Posts() {
		$widget_ops = array(
			'classname' => 'grve-latest-news',
			'description' => __( 'A widget that displays latest posts', GRVE_THEME_TRANSLATE),
		);
		$control_ops = array(
			'width' => 300,
			'height' => 400,
			'id_base' => 'grve-widget-latest-posts',
		);
		$this->WP_Widget( 'grve-widget-latest-posts', '(Greatives) ' . __( 'Latest Posts', GRVE_THEME_TRANSLATE ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		$image_size = 'grve-image-extrasmall-square';

		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters( 'widget_title', $instance['title'] );
		$num_of_posts = $instance['num_of_posts'];
		$show_image = $instance['show_image'];

		if( empty( $num_of_posts ) ) {
			$num_of_posts = 5;
		}

		echo $before_widget;

		// Display the widget title
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		$args = array(
			'post_type' => 'post',
			'post_status'=>'publish',
			'paged' => 1,
			'posts_per_page' => $num_of_posts,
		);
		//Loop posts
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
		?>
			<ul>
		<?php
		while ( $query->have_posts() ) : $query->the_post();

			$grve_link = get_permalink();
			$grve_target = '_self';

			if ( 'link' == get_post_format() ) {
				$grve_link = get_post_meta( get_the_ID(), 'grve_post_link_url', true );
				$new_window = get_post_meta( get_the_ID(), 'grve_post_link_new_window', true );
				if( empty( $grve_link ) ) {
					$grve_link = get_permalink();
				}

				if( !empty( $new_window ) ) {
					$grve_target = '_blank';
				}
			}

		?>

				<li <?php post_class(); ?>>
					<?php if( $show_image && '1' == $show_image ) { ?>
						<a href="<?php echo esc_url( $grve_link ); ?>" target="<?php echo $grve_target; ?>" title="<?php the_title_attribute(); ?>">
						<?php if ( has_post_thumbnail() ) { ?>
							<?php the_post_thumbnail( $image_size ); ?>
						<?php } else { ?>
							<img width="80" height="80" src="<?php echo get_template_directory_uri() . '/images/empty/grve-image-extrasmall-square.jpg'; ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>">
						<?php } ?>
						</a>
					<?php } ?>
					<div class="grve-news-content">
						<a href="<?php echo esc_url( $grve_link ); ?>" target="<?php echo $grve_target; ?>" class="grve-title" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						<div class="grve-latest-news-date"><?php echo get_the_date(); ?></div>
					</div>
				</li>

		<?php
		endwhile;
		?>
			</ul>
		<?php
		else :
		?>

				<?php _e( 'No Posts Found!', GRVE_THEME_TRANSLATE ); ?>

		<?php
		endif;

		wp_reset_postdata();
		echo $after_widget;
	}

	//Update the widget

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_of_posts'] = strip_tags( $new_instance['num_of_posts'] );
		$instance['show_image'] = strip_tags( $new_instance['show_image'] );

		return $instance;
	}


	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array(
			'title' => '',
			'num_of_posts' => '5',
			'show_image' => '0',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>


		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_of_posts' ); ?>"><?php echo __( 'Number of Posts:', GRVE_THEME_TRANSLATE ); ?></label>
			<select  name="<?php echo $this->get_field_name( 'num_of_posts' ); ?>" style="width:100%;">
				<?php
				for ( $i = 1; $i <= 20; $i++ ) {
					$selected = '';
					if ( $i == $instance['num_of_posts'] ) {
						$selected = 'selected="selected"';
					}
				?>
				    <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
				<?php
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php echo __( 'Show Featured Image:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id('show_image'); ?>" name="<?php echo $this->get_field_name('show_image'); ?>" type="checkbox" value="1" <?php checked( $instance['show_image'], 1 ); ?> />
		</p>

	<?php
	}
}

?>