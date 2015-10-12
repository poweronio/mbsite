<?php 
if(!function_exists('register_geodir_cpt_widgets')){
	function register_geodir_cpt_widgets(){	
		/**
		* Geodirectory CPT image listings widget *
		**/
		class geodir_cpt_listings extends WP_Widget {

            function __construct() {
                $widget_ops = array('classname' => 'geodir_cpt_listings', 'description' => __('GD > GeoDirectory Custom Post Type Listings', GEODIR_CP_TEXTDOMAIN) );
                parent::__construct(
                    'cpt_listings', // Base ID
                    __('GD > CPT Listings', GEODIR_CP_TEXTDOMAIN), // Name
                    $widget_ops// Args
                );
            }
			
			public function widget($args, $instance)  {
				global $wp, $load_cpt_widget;
				extract($args, EXTR_SKIP);
				
				$title = empty($instance['title']) ? __('GD Listings',GEODIR_CP_TEXTDOMAIN) : apply_filters('geodir_cpt_widget_title', __($instance['title'],GEODIR_CP_TEXTDOMAIN));
				$cpt_img_width = !isset($instance['cpt_img_width']) ? 90 : apply_filters('geodir_cpt_widget_img_width', $instance['cpt_img_width']);
				$cpt_img_height = !isset($instance['cpt_img_height']) ? 90 : apply_filters('geodir_cpt_widget_img_height', $instance['cpt_img_height']);
				$cpt_hide_name = !isset($instance['cpt_hide_name']) ? false : apply_filters('geodir_cpt_widget_hide_name', $instance['cpt_hide_name']);
				$cpt_exclude = empty($instance['cpt_exclude']) ? array() : apply_filters('geodir_cpt_widget_exclude', $instance['cpt_exclude']);
				
				$post_types = geodir_get_posttypes('array');
				
				// Exclude CPT to hide from display.
				if ( !empty( $cpt_exclude ) ) {
					foreach ( $cpt_exclude as $cpt ) {
						if ( isset( $post_types[$cpt] ) )
							unset( $post_types[$cpt] );
					}
				}
				
				if ( empty( $post_types ) ) {
					return; // If no CPT to display
				}
								
				echo $before_widget;				
				$img_width = (float)$cpt_img_width > 10 ? (float)$cpt_img_width : 90;
				$img_height = (float)$cpt_img_height > 10 ? (float)$cpt_img_height : 90;
				?>
				<?php echo $before_title.__($title).$after_title;?>
				<div class="gd-cpt-widget-box clearfix">
					<div class="gd-cpt-widget-list">
					<?php 
					foreach ($post_types as $cpt => $cpt_info ) { 
						$cpt_name = $cpt_info['labels']['name'];
						$cpt_url = get_post_type_archive_link($cpt);
						$image_url = get_option('geodir_cpt_img_' . $cpt);
						$image_url = apply_filters('geodir_cpt_img_url', $image_url, $cpt);
						$cpt_image = $image_url ? '<img class="gd-cpt-img" src="' . $image_url . '" style="width:' . $img_width . 'px;height:' . $img_height . 'px;" />' : $cpt_name;
						$show_cpt_name = !$cpt_hide_name ? '<div class="gd-cpt-name">' . $cpt_name . '</div>' : '';
					?>
					<div class="gd-cpt-wrow" style="width:<?php echo ($img_width + 2);?>px;height:<?php echo ($img_height + 2);?>px;"><a href="<?php echo $cpt_url;?>" title="<?php echo esc_attr($cpt_name);?>"><?php echo $cpt_image;?><?php echo $show_cpt_name;?></a></div>
					<?php } ?>
					</div>
					<?php if (!$load_cpt_widget) { ?>
					<style>
					.gd-cpt-widget-box {}
					.gd-cpt-widget-list {margin: auto; padding: 0 ;text-align: center;}
					.gd-cpt-widget-list .gd-cpt-wrow {position: relative;float: none;display: inline-block;overflow: hidden;margin: 3px;border: solid 1px #e1e1e1;text-align: center;}
					.gd-cpt-widget-list .gd-cpt-wrow:hover {border: solid 1px #aaa;}
					.gd-cpt-wrow .gd-cpt-img {border: none;margin: auto;padding: 0;}
					.gd-cpt-widget-list .gd-cpt-wrow a {display: block;width: 100%;height: 100%;text-decoration: none}
					.gd-cpt-widget-list .gd-cpt-wrow .gd-cpt-name {font-size: 13px;display: block;position: absolute;bottom: 0;left:0;text-align:center;width:100%;overflow: hidden;white-space: nowrap;opacity: 0.7;filter: alpha(opacity=70);background-color: #333;color: #fff;line-height: 20px}
					</style>
					<?php } ?>
				</div>
				<?php
				$load_cpt_widget = true;
				echo $after_widget;
			}
			
			public function update($new_instance, $old_instance) {
				$instance = $old_instance;
				
				$cpt_img_width = (float)$new_instance['cpt_img_width'];
				$cpt_img_height = (float)$new_instance['cpt_img_height'];
								
				$instance['title'] = strip_tags($new_instance['title']);
				$instance['cpt_exclude'] = isset($new_instance['cpt_exclude']) ? $new_instance['cpt_exclude'] : '';
				$instance['cpt_img_width'] = $cpt_img_width > 10 ? $cpt_img_width : 90;
				$instance['cpt_img_height'] = $cpt_img_height > 10 ? $cpt_img_height : 90;
				$instance['cpt_hide_name'] = (bool)$new_instance['cpt_hide_name'];
				return $instance;
			}
			
			public function form($instance) {
				$instance = wp_parse_args( (array)$instance, array( 'title' => '', 'cpt_exclude' => array(), 'cpt_img_width' => 90, 'cpt_img_height' => 90, 'cpt_hide_name' => false ) );
				
				$title = strip_tags($instance['title']);
				$cpt_exclude = $instance['cpt_exclude'];
				$cpt_img_width = (float)$instance['cpt_img_width'];
				$cpt_img_height = (float)$instance['cpt_img_height'];
				$cpt_hide_name = (bool)$instance['cpt_hide_name'];
				$cpt_img_width = $cpt_img_width > 10 ? $cpt_img_width : 90;
				$cpt_img_height = $cpt_img_height > 10 ? $cpt_img_height : 90;
				
				$post_types = geodir_get_posttypes( 'array' );
				?>
				<p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', GEODIR_CP_TEXTDOMAIN);?>
						<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
					</label>
				</p>
				<p id="wgt_cpt_exclude" style="margin-bottom:3px">
					<label for="<?php echo $this->get_field_id('cpt_exclude'); ?>"><?php _e('Exclude CPT:', GEODIR_CP_TEXTDOMAIN);?>
					<?php if ( !empty( $post_types ) ) { foreach ( $post_types as $post_type => $cpt_info ) { $checked = !empty( $cpt_exclude ) && in_array( $post_type, $cpt_exclude ) ? 'checked="checked"' : ''; $cpt_name = __( $cpt_info['labels']['name'], GEODIR_CP_TEXTDOMAIN ); ?>
					<p style="margin:0;padding:0 0 0 20px">
					<label for="<?php echo $this->get_field_id('cpt_exclude');?>_<?php echo $post_type;?>">
					<input type="checkbox" id="<?php echo $this->get_field_id('cpt_exclude');?>_<?php echo $post_type;?>" name="<?php echo $this->get_field_name('cpt_exclude'); ?>[]" <?php echo $checked;?> value="<?php echo $post_type;?>"/>&nbsp;<?php echo wp_sprintf( __( 'Exclude %s', GEODIR_CP_TEXTDOMAIN ), $cpt_name );?>
					</label>
					</p>
					<?php } } ?>
					</label>
				</p>
				<p style="padding:0" class="description"><?php _e('Exclude CPT to hide from CPT listings.', GEODIR_CP_TEXTDOMAIN);?></p>
				<p>
				  <label for="<?php echo $this->get_field_id('cpt_img_width'); ?>">
				  <?php _e('Image width:', GEODIR_CP_TEXTDOMAIN);?>
				  <input class="widefat" id="<?php echo $this->get_field_id('cpt_img_width'); ?>" name="<?php echo $this->get_field_name('cpt_img_width'); ?>" type="text" value="<?php echo $cpt_img_width; ?>"/>
				  </label>
				</p>
				<p style="padding:0" class="description"><?php _e('Width of image to display in widget. Ex: 90', GEODIR_CP_TEXTDOMAIN);?></p>
				<p>
				  <label for="<?php echo $this->get_field_id('cpt_img_height'); ?>">
				  <?php _e('Image height:', GEODIR_CP_TEXTDOMAIN);?>
				  <input class="widefat" id="<?php echo $this->get_field_id('cpt_img_height'); ?>" name="<?php echo $this->get_field_name('cpt_img_height'); ?>" type="text" value="<?php echo $cpt_img_height; ?>"/>
				  </label>
				</p>
				<p style="padding:0" class="description"><?php _e('Height of image to display in widget. Ex: 90', GEODIR_CP_TEXTDOMAIN);?></p>
				<p>
					<label for="<?php echo $this->get_field_id('cpt_hide_name'); ?>">
						<?php _e('Hide CPT name:', GEODIR_CP_TEXTDOMAIN);?>
						<input type="checkbox" id="<?php echo $this->get_field_id('cpt_hide_name'); ?>" name="<?php echo $this->get_field_name('cpt_hide_name'); ?>" <?php if ($cpt_hide_name) echo 'checked="checked"';?> value="1"/>
					</label>
				</p>
				<p style="padding:0" class="description"><?php _e('If checked then custom post type name will not displayed.', GEODIR_CP_TEXTDOMAIN);?></p>
				
				<?php  
			} 
		}
		register_widget('geodir_cpt_listings');
	}
}