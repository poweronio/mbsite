<?php
/**
 * Plugin Name: Greatives Instagram Feed
 * Description: A widget that displays latest posts.
 * @author		Greatives Team
 * @URI			http://greatives.eu
 */

add_action( 'widgets_init', 'grve_widget_instagram_feed' );

function grve_widget_instagram_feed() {
	register_widget( 'GRVE_Widget_Instagram_Feed' );
}

class GRVE_Widget_Instagram_Feed extends WP_Widget {

	function GRVE_Widget_Instagram_Feed() {
		$widget_ops = array(
			'classname' => 'grve-instagram-feed',
			'description' => __( 'A widget that displays instagram feed', GRVE_THEME_TRANSLATE),
		);
		$control_ops = array(
			'width' => 300,
			'height' => 400,
			'id_base' => 'grve-widget-instagram-feed',
		);
		$this->WP_Widget( 'grve-widget-instagram-feed', '(Greatives) ' . __( 'Instagram Feed', GRVE_THEME_TRANSLATE ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters( 'widget_title', $instance['title'] );
		$username = $instance['username'];
		$limit = $instance['limit'];
		$order_by = $instance['order_by'];
		$order = $instance['order'];
		$target = $instance['target'];
		$cache = $instance['cache'];

		if( !isset( $cache ) ) {
			$cache = '';
		}

		if( empty( $limit ) ) {
			$limit = 9;
		}

		echo $before_widget;

		// Display the widget title
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		if ( !empty( $username ) ) {

			$media_array = $this->grve_get_instagram_array( $username, $limit, $order_by, $order, $cache );
			$output = '';

			if ( is_wp_error( $media_array ) ) {

			   echo $media_array->get_error_message();

			} else {

			?>
				<ul class="grve-instagram-images">
			<?php
				foreach ($media_array as $item) {
					$output .= '<li>';
					$output .= '  <a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'">';
					$output .= '    <img src="'. esc_url( $item['thumbnail']['url'] ) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'"/>';
					$output .= '  </a>';
					$output .= '</li>';
				}
				echo $output;
			?>
				</ul>
			<?php
			}
		}

		echo $after_widget;
	}

	//Get instagram array
	function grve_get_instagram_array( $username, $limit, $order_by, $order, $cache ) {

		$username = strtolower( $username );

		if ( false === ( $instagram = get_transient('grve-instagram-feed-'.sanitize_title_with_dashes( $username ) ) ) || empty( $cache ) ) {

			$remote = wp_remote_get('http://instagram.com/'.trim($username) );

			if ( is_wp_error( $remote ) ) {
	  			return new WP_Error( 'site_down', __( 'Unable to communicate with Instagram!', GRVE_THEME_TRANSLATE ) );
			}
  			if ( 200 != wp_remote_retrieve_response_code( $remote ) ) {
  				return new WP_Error('invalid_response', __( 'Instagram invalid response!', GRVE_THEME_TRANSLATE ) );
			}

			$shards = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], TRUE );

			if ( !$insta_array ) {
	  			return new WP_Error('bad_json', __( 'Instagram has returned invalid data!', GRVE_THEME_TRANSLATE ) );
			}

			$images = $insta_array['entry_data']['UserProfile'][0]['userMedia'];

			$instagram = array();

			foreach ( $images as $image ) {

				if ($image['user']['username'] == $username) {

					$image['link']                          = preg_replace( "/^http:/i", "", $image['link'] );
					$image['images']['thumbnail']           = preg_replace( "/^http:/i", "", $image['images']['thumbnail'] );
					$image['images']['standard_resolution'] = preg_replace( "/^http:/i", "", $image['images']['standard_resolution'] );

					$instagram[] = array(
						'description'   => $image['caption']['text'],
						'link'          => $image['link'],
						'time'          => $image['created_time'],
						'comments'      => $image['comments']['count'],
						'likes'         => $image['likes']['count'],
						'thumbnail'     => $image['images']['thumbnail'],
						'large'         => $image['images']['standard_resolution'],
						'type'          => $image['type']
					);
				}
			}

			//Instagram Order
			if ( 'none' != $order_by ) {
				foreach ($instagram as $key => $row) {
					$time[$key] = $row['time'];
					$comments[$key]  = $row['comments'];
					$likes[$key] = $row['likes'];
				}
				if ( 'ASC' == $order ) {
					$order = SORT_ASC;
				} else {
					$order = SORT_DESC;
				}
				if ( 'datetime' == $order_by ) {
					$order_by = $time;
				} elseif ( 'comments' == $order_by ) {
					$order_by = $comments;
				} elseif ( 'likes' == $order_by ) {
					$order_by = $likes;
				}
				array_multisort( $order_by, $order, $instagram );
			}

			$instagram = base64_encode( serialize( $instagram ) );
			if( !empty( $cache ) ) {
				set_transient('grve-instagram-feed-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'grve_instagram_cache_time', HOUR_IN_SECONDS ) );
			}
		}

		$instagram = unserialize( base64_decode( $instagram ) );

		return array_slice( $instagram, 0, $limit );
	}


	//Update the widget

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['order_by'] = strip_tags( $new_instance['order_by'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['target'] = strip_tags( $new_instance['target'] );
		$instance['cache'] = strip_tags( $new_instance['cache'] );

		return $instance;
	}


	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array(
			'title' => '',
			'limit' => '9',
			'order_by' => 'none',
			'order' => 'ASC',
			'target' => '_blank',
			'cache' => '1',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = esc_attr($instance['title']);
		$username = esc_attr($instance['username']);
		$limit = absint($instance['limit']);
		$order_by = esc_attr($instance['order_by']);
		$order = esc_attr($instance['order']);
		$target = esc_attr($instance['target']);
		$cache = esc_attr($instance['cache']); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $username; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php echo __( 'Number of Images:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $limit; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php echo __( 'Order By:', GRVE_THEME_TRANSLATE ); ?></label>
			<select id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>" style="width:100%;">
				<option value="none" <?php selected('none', $order_by) ?>><?php _e( 'None', GRVE_THEME_TRANSLATE ); ?></option>
				<option value="datetime" <?php selected('datetime', $order_by) ?>><?php _e( 'Recent', GRVE_THEME_TRANSLATE ); ?></option>
				<option value="likes" <?php selected('likes', $order_by) ?>><?php _e( 'Likes', GRVE_THEME_TRANSLATE ); ?></option>
				<option value="comments" <?php selected('comments', $order_by) ?>><?php _e( 'Comments', GRVE_THEME_TRANSLATE ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php echo __( 'Order:', GRVE_THEME_TRANSLATE ); ?></label>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>" style="width:100%;">
				<option value="ASC" <?php selected('ASC', $order) ?>><?php _e( 'Ascending', GRVE_THEME_TRANSLATE ); ?></option>
				<option value="DESC" <?php selected('DESC', $order) ?>><?php _e( 'Descending', GRVE_THEME_TRANSLATE ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php echo __( 'Link Target:', GRVE_THEME_TRANSLATE ); ?></label>
			<select id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" style="width:100%;">
				<option value="_self" <?php selected('_self', $target) ?>><?php _e( 'Same Page', GRVE_THEME_TRANSLATE ); ?></option>
				<option value="_blank" <?php selected('_blank', $target) ?>><?php _e( 'New Page', GRVE_THEME_TRANSLATE ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cache' ); ?>"><?php echo __( 'Caching:', GRVE_THEME_TRANSLATE ); ?></label>
			<input id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="checkbox" value="1" <?php checked( $cache, 1 ); ?> />
		</p>
		<p>
			<em><?php echo __( 'Note: Uncheck caching if you want to test your configuration. It is recommended to leave caching enabled to increase performance. Caching timeout is 60 minutes.', GRVE_THEME_TRANSLATE ); ?></em>
		</p>

	<?php
	}
}

?>