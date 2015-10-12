<?php
/**
 * Portfolio Shortcode
 */

if( !function_exists( 'grve_portfolio_shortcode' ) ) {

	function grve_portfolio_shortcode( $attr, $content ) {

		$portfolio_row_start = $allow_filter = $class_fullwidth = $el_class = '';

		extract(
			shortcode_atts(
				array(
					'categories' => '',
					'portfolio_style' => 'grid',
					'portfolio_columns' => '3',
					'portfolio_image_mode' => '',
					'portfolio_link_type' => 'item',
					'portfolio_link_type_title' => 'More Details',
					'portfolio_filter' => '',
					'portfolio_filter_align' => 'left',
					'filter_order_by' => '',
					'filter_order' => 'ASC',
					'item_gutter' => 'yes',
					'item_spinner' => 'no',
					'items_per_page' => '4',
					'items_to_show' => '12',
					'hide_portfolio_title' => '',
					'hide_portfolio_caption' => '',
					'hide_portfolio_like' => '',
					'portfolio_hover_style' => 'hover-style-1',
					'zoom_effect' => 'in',
					'overlay_color' => 'dark',
					'overlay_opacity' => '60',
					'order_by' => 'date',
					'order' => 'DESC',
					'disable_pagination' => '',
					'slideshow_speed' => '3000',
					'auto_play' => 'yes',
					'navigation_type' => '1',
					'navigation_color' => 'light',
					'pause_hover' => 'no',

					'margin_bottom' => '',
					'el_class' => '',
				),
				$attr
			)
		);

		$portfolio_classes = array( 'grve-element' );
		$data_string = '';

		switch( $portfolio_style ) {
			case 'carousel':
				$data_string = ' data-items="' . esc_attr( $items_per_page ) . '" data-slider-autoplay="' . esc_attr( $auto_play ) . '" data-slider-speed="' . esc_attr( $slideshow_speed ) . '" data-slider-pause="' . esc_attr( $pause_hover ) . '"';
				array_push( $portfolio_classes, 'grve-carousel-wrapper' );
				if ( 'popup' == $portfolio_link_type ) {
					array_push( $portfolio_classes, 'grve-gallery-popup' );
				}
				$disable_pagination = 'yes';
				break;
			case 'masonry':
				$portfolio_row_start = '<div class="grve-isotope-container">';
				if ( 'popup' == $portfolio_link_type ) {
					$portfolio_row_start = '<div class="grve-isotope-container grve-gallery-popup">';
				}
				$data_string = ' data-gutter="' . esc_attr( $item_gutter ) . '" data-spinner="' . esc_attr( $item_spinner ) . '" data-type="' . esc_attr( $portfolio_columns ) . '-columns" data-layout="masonry"';
				array_push( $portfolio_classes, 'grve-portfolio' );
				array_push( $portfolio_classes, 'grve-isotope' );
				$allow_filter = 'yes';
				break;
			case 'multi-grid':
				$portfolio_row_start = '<div class="grve-isotope-container">';
				if ( 'popup' == $portfolio_link_type ) {
					$portfolio_row_start = '<div class="grve-isotope-container grve-gallery-popup">';
				}
				$data_string = ' data-gutter="' . esc_attr( $item_gutter ) . '" data-spinner="' . esc_attr( $item_spinner ) . '" data-type="' . esc_attr( $portfolio_columns ) . '-columns" data-layout="packery"';
				array_push( $portfolio_classes, 'grve-portfolio' );
				array_push( $portfolio_classes, 'grve-isotope' );
				$allow_filter = 'yes';
				break;
			case 'small-media':
				$portfolio_row_start = '<div class="grve-standard-container">';
				array_push( $portfolio_classes, 'grve-portfolio' );
				array_push( $portfolio_classes, 'grve-blog' );
				array_push( $portfolio_classes, 'grve-small-media' );
				array_push( $portfolio_classes, 'grve-non-isotope' );
				$allow_filter = 'yes';
				$portfolio_link_type = 'item';
				break;
			case 'grid':
			default:
				$portfolio_row_start = '<div class="grve-isotope-container">';
				if ( 'popup' == $portfolio_link_type ) {
					$portfolio_row_start = '<div class="grve-isotope-container grve-gallery-popup">';
				}
				$data_string = ' data-gutter="' . esc_attr( $item_gutter ) . '" data-spinner="' . esc_attr( $item_spinner ) . '" data-type="' . esc_attr( $portfolio_columns ) . '-columns" data-layout="fitRows"';
				array_push( $portfolio_classes, 'grve-portfolio' );
				array_push( $portfolio_classes, 'grve-isotope' );
				$allow_filter = 'yes';
				break;
		}


		if ( !empty ( $el_class ) ) {
			array_push( $portfolio_classes, $el_class);
		}
		$portfolio_class_string = implode( ' ', $portfolio_classes );

		$style = grve_osmosis_vce_build_margin_bottom_style( $margin_bottom );

		$portfolio_cat = "";
		$portfolio_category_ids = array();

		if( ! empty( $categories ) ) {
			$portfolio_category_ids = explode( ",", $categories );
			foreach ( $portfolio_category_ids as $category_id ) {
				$category_term = get_term( $category_id, 'portfolio_category' );
				if ( isset( $category_term) ) {
					$portfolio_cat = $portfolio_cat.$category_term->slug . ', ';
				}
			}
		}

		global $paged;
		$paged = 1;

		if ( 'yes' != $disable_pagination ) {
			if ( get_query_var( 'paged' ) ) {
				$paged = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$paged = get_query_var( 'page' );
			}
		}

		$args = array(
			'post_type' => 'portfolio',
			'post_status'=>'publish',
			'paged' => $paged,
			'portfolio_category' => $portfolio_cat,
			'posts_per_page' => $items_to_show,
			'orderby' => $order_by,
			'order' => $order,
		);

		$query = new WP_Query( $args );
		ob_start();
		if ( $query->have_posts() ) :
		?>
			<div class="<?php echo esc_attr( $portfolio_class_string ); ?>" style="<?php echo $style; ?>"<?php echo $data_string; ?>>
		<?php

		if ( 'yes' == $portfolio_filter && 'yes' == $allow_filter ) {

			$category_prefix = '.portfolio_category_';
			$category_filter_list = array();
			$category_filter_array = array();
			$all_string =  apply_filters( 'grve_vce_portfolio_string_all_categories', __( 'All', 'grve-osmosis-vc-extension' ) );
			$category_filter_string = '<li data-filter="*" class="selected">' . $all_string . '</li>';
			$category_filter_add = false;
			while ( $query->have_posts() ) : $query->the_post();

				if ( $portfolio_categories = get_the_terms( get_the_ID(), 'portfolio_category' ) ) {

					foreach($portfolio_categories as $category_term){
						$category_filter_add = false;
						if ( !in_array($category_term->term_id, $category_filter_list) ) {
							if( ! empty( $portfolio_category_ids ) ) {
								if ( in_array($category_term->term_id, $portfolio_category_ids) ) {
									$category_filter_add = true;
								}
							} else {
								$category_filter_add = true;
							}
							if ( $category_filter_add ) {
								$category_filter_list[] = $category_term->term_id;
								if ( 'title' == $filter_order_by ) {
									$category_filter_array[$category_term->name] = $category_term;
								} elseif ( 'slug' == $filter_order_by )  {
									$category_filter_array[$category_term->slug] = $category_term;
								} else {
									$category_filter_array[$category_term->term_id] = $category_term;
								}
							}
						}
					}
				}

			endwhile;


			if ( count( $category_filter_array ) > 1 ) {
				if ( '' != $filter_order_by ) {
					if ( 'ASC' == $filter_order ) {
						ksort( $category_filter_array );
					} else {
						krsort( $category_filter_array );
					}
				}
				foreach($category_filter_array as $category_filter){
					$category_filter_string .= '<li data-filter="' . $category_prefix . $category_filter->slug . '">' . $category_filter->name . '</li>';
				}
		?>
				<div class="grve-filter grve-align-<?php echo esc_attr( $portfolio_filter_align ); ?>">
					<ul>
						<?php echo $category_filter_string; ?>
					</ul>
				</div>
		<?php
			}
		}
		?>

			<?php echo $portfolio_row_start; ?>

		<?php

		if ( 'carousel' == $portfolio_style ) {
?>
			<?php if ( 0 != $navigation_type ) { ?>
			<div class="grve-carousel-navigation grve-<?php echo esc_attr( $navigation_color ); ?>" data-navigation-type="<?php echo esc_attr( $navigation_type ); ?>">
				<div class="grve-carousel-buttons">
					<div class="grve-carousel-prev grve-icon-nav-left"></div>
					<div class="grve-carousel-next grve-icon-nav-right"></div>
				</div>
			</div>
			<?php } ?>
			<div class="grve-carousel grve-carousel-element grve-portfolio"<?php echo $data_string; ?>>
<?php
		}

		$portfolio_index = 0;

		while ( $query->have_posts() ) : $query->the_post();
			$image_size = 'grve-image-small-rect-horizontal';
			$portfolio_index++;
			$portfolio_extra_class = '';

			$caption = get_post_meta( get_the_ID(), 'grve_portfolio_description', true );
			$details = get_post_meta( get_the_ID(), 'grve_portfolio_details', true );
			$link_mode = get_post_meta( get_the_ID(), 'grve_portfolio_link_mode', true );
			$link_url = get_post_meta( get_the_ID(), 'grve_portfolio_link_url', true );
			$new_window = get_post_meta( get_the_ID(), 'grve_portfolio_link_new_window', true );
			$link_class = get_post_meta( get_the_ID(), 'grve_portfolio_link_extra_class', true );


			if ( 'carousel' != $portfolio_style ) {
				$image_size = 'grve-image-small-square';
				$portfolio_extra_class = 'grve-isotope-item grve-portfolio-item ';

				if ( 'multi-grid' == $portfolio_style ) {
					$grve_packery_data = grve_osmosis_vce_get_packery_data( $portfolio_index, $portfolio_columns );
					$portfolio_extra_class .= $grve_packery_data['class'];
					$image_size = $grve_packery_data['image_size'];
				} elseif ( 'masonry' == $portfolio_style ) {
					if ( 'resize' == $portfolio_image_mode ) {
						$portfolio_extra_class .= 'grve-masonry-image';
						$image_size = 'large';
					} else {
						$grve_masonry_data = grve_osmosis_vce_get_masonry_data( $portfolio_index, $portfolio_columns );
						$portfolio_extra_class .= $grve_masonry_data['class'];
						$image_size = $grve_masonry_data['image_size'];
					}
				} elseif ( 'grid' == $portfolio_style ) {
					if ( 'resize' == $portfolio_image_mode ) {
						$image_size = 'large';
					}
				} else {
					$portfolio_extra_class = 'grve-non-isotope-item grve-blog-item grve-small-post';
					$image_size = 'grve-image-small-rect-horizontal';
					if ( 'resize' == $portfolio_image_mode ) {
						$image_size = 'large';
					}
				}
			} else {
				$portfolio_extra_class = 'grve-portfolio-item';
				echo '<div class="grve-carousel-item">';
			}

			//Portfolio Link

			$portfolio_link_exists = true;
			$grve_target = '_self';
			if( !empty( $new_window ) ) {
				$grve_target = '_blank';
			}

			ob_start();
			if ( 'small-media' == $portfolio_style ) {
				?>
					<a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
			} else {
				if ( 'popup' == $portfolio_link_type ) {
				?>
					<a title="<?php the_title(); ?>" href="<?php grve_osmosis_vce_print_portfolio_image( 'full', 'link' ); ?>">
				<?php
				}  else if ( 'custom-link' == $portfolio_link_type ) {
					if ( '' == $link_mode )	{
				?>
					<a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					} else if ( 'link' == $link_mode && !empty( $link_url ) ) {
				?>
					<a title="<?php the_title(); ?>" class="<?php echo esc_attr( $link_class ); ?>" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $grve_target ); ?>">
				<?php
					} else {
						$portfolio_link_exists = false;
					}
				} else {
				?>
					<a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
				}
			}

			$link_start = ob_get_clean();

			if ( $portfolio_link_exists ) {
				$link_end = '</a>';
			} else {
				$link_end = '';
			}

?>
					<article id="portfolio-<?php the_ID(); ?><?php echo uniqid('-'); ?>" <?php post_class( $portfolio_extra_class ); ?>>
						<?php
						if ( 'carousel' == $portfolio_style ) {
						?>
							<figure class="grve-hover-style-1 grve-image-hover grve-zoom-<?php echo esc_attr( $zoom_effect ); ?> grve-<?php echo esc_attr( $overlay_color ); ?>">
								<?php
									if( function_exists( 'grve_print_portfolio_like_counter' ) && 'yes' != $hide_portfolio_like ) {
										grve_print_portfolio_like_counter();
									}
								?>
								<div class="grve-media grve-<?php echo esc_attr( $overlay_color ); ?>-overlay grve-opacity-<?php echo esc_attr( $overlay_opacity ); ?>">
									<?php grve_osmosis_vce_print_portfolio_image( $image_size ); ?>
								</div>
								<figcaption>
									<?php if ( 'yes' != $hide_portfolio_title  ) { ?>
									<h6 class="grve-title grve-<?php echo esc_attr( $overlay_color ); ?>"><?php the_title(); ?></h6>
									<?php } ?>
									<?php if ( !empty( $caption ) && 'yes' != $hide_portfolio_caption  ) { ?>
									<span class="grve-caption grve-<?php echo esc_attr( $overlay_color ); ?>"><?php echo $caption; ?></span>
									<?php } ?>

									<?php
										if ( 'popup' == $portfolio_link_type ) {
									?>
										<a title="<?php the_title(); ?>" class="grve-portfolio-btns" href="<?php grve_osmosis_vce_print_portfolio_image( 'full', 'link' ); ?>"><?php echo $portfolio_link_type_title; ?></a>
									<?php
										} elseif ( $portfolio_link_exists ) {
											if ( 'custom-link' == $portfolio_link_type && 'link' == $link_mode) {
									?>
										<a class="grve-portfolio-btns" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $grve_target ); ?>"><?php echo $portfolio_link_type_title; ?></a>
									<?php
											} else {
									?>
										<a class="grve-portfolio-btns" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $portfolio_link_type_title; ?></a>
									<?php
											}
										}
									?>
								</figcaption>
							</figure>
						<?php

						} elseif( 'small-media' == $portfolio_style ) {
						?>
							<div class="grve-media grve-image-hover">
								<?php echo $link_start; ?><?php grve_osmosis_vce_print_portfolio_image( $image_size ); ?><?php echo $link_end; ?>
							</div>
							<div class="grve-post-content">
								<?php if ( 'yes' != $hide_portfolio_title  ) { ?>
								<?php echo $link_start; ?><h4 class="grve-title grve-light"><?php the_title(); ?></h4><?php echo $link_end; ?>
								<?php } ?>
								<div class="grve-post-meta">
									<?php if ( !empty( $caption ) && 'yes' != $hide_portfolio_caption  ) { ?>
									<span class="grve-caption grve-light"><?php echo $caption; ?></span>
									<?php } ?>
								</div>
								<?php if ( !empty( $details ) ) { ?>
									<p><?php echo $details; ?></p>
								<?php } ?>
									<a class="grve-read-more" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $portfolio_link_type_title; ?></a>
							</div>
						<?php
						} else {

							if ( 'hover-style-3' == $portfolio_hover_style ) {
						?>
							<?php echo $link_start; ?>
							<figure class="grve-hover-style-3 grve-image-hover grve-zoom-<?php echo esc_attr( $zoom_effect ); ?> grve-<?php echo esc_attr( $overlay_color ); ?>">
								<div class="grve-media grve-<?php echo esc_attr( $overlay_color ); ?>-overlay grve-opacity-<?php echo esc_attr( $overlay_opacity ); ?>">
									<?php if ( $portfolio_link_exists ) { ?>
									<span class="grve-portfolio-btns"><?php echo $portfolio_link_type_title; ?></span>
									<?php } ?>
									<?php grve_osmosis_vce_print_portfolio_image( $image_size ); ?>
								</div>
								<?php
									if( function_exists( 'grve_print_portfolio_like_counter' ) && 'yes' != $hide_portfolio_like ) {
										grve_print_portfolio_like_counter();
									}
								?>
								<figcaption>
									<?php if ( 'yes' != $hide_portfolio_title  ) { ?>
									<h6 class="grve-title grve-light"><?php the_title(); ?></h6>
									<?php } ?>
									<?php if ( !empty( $caption ) && 'yes' != $hide_portfolio_caption  ) { ?>
									<span class="grve-caption grve-light"><?php echo $caption; ?></span>
									<?php } ?>
								</figcaption>
							</figure>
							<?php echo $link_end; ?>
						<?php
							} else if ( 'hover-style-2' == $portfolio_hover_style ) {
						?>
							<?php echo $link_start; ?>
							<figure class="grve-hover-style-2 grve-<?php echo esc_attr( $overlay_color ); ?>">
								<div class="grve-media">
									<?php grve_osmosis_vce_print_portfolio_image( $image_size ); ?>
								</div>
								<figcaption class="grve-<?php echo esc_attr( $overlay_color ); ?>-overlay grve-opacity-<?php echo esc_attr( $overlay_opacity ); ?>">
									<div class="grve-content">
										<?php
											if( function_exists( 'grve_print_portfolio_like_counter' ) && 'yes' != $hide_portfolio_like ) {
												grve_print_portfolio_like_counter();
											}
										?>
										<?php if ( 'yes' != $hide_portfolio_title  ) { ?>
										<h6 class="grve-title grve-<?php echo esc_attr( $overlay_color ); ?>"><?php the_title(); ?></h6>
										<?php } ?>
										<?php if ( !empty( $caption ) && 'yes' != $hide_portfolio_caption  ) { ?>
										<span class="grve-caption grve-<?php echo esc_attr( $overlay_color ); ?>"><?php echo $caption; ?></span>
										<?php } ?>
									</div>
								</figcaption>
							</figure>
							<?php echo $link_end; ?>
						<?php
							} else {
							//Default Hover Style 1
						?>
							<?php echo $link_start; ?>
							<figure class="grve-hover-style-1 grve-image-hover grve-zoom-<?php echo esc_attr( $zoom_effect ); ?> grve-<?php echo esc_attr( $overlay_color ); ?>">
								<?php
									if( function_exists( 'grve_print_portfolio_like_counter' ) && 'yes' != $hide_portfolio_like ) {
										grve_print_portfolio_like_counter();
									}
								?>
								<div class="grve-media grve-<?php echo esc_attr( $overlay_color ); ?>-overlay grve-opacity-<?php echo esc_attr( $overlay_opacity ); ?>">
									<?php grve_osmosis_vce_print_portfolio_image( $image_size ); ?>
								</div>
								<figcaption>
									<?php if ( 'yes' != $hide_portfolio_title  ) { ?>
									<h6 class="grve-title grve-<?php echo esc_attr( $overlay_color ); ?>"><?php the_title(); ?></h6>
									<?php } ?>
									<?php if ( !empty( $caption ) && 'yes' != $hide_portfolio_caption  ) { ?>
									<span class="grve-caption grve-<?php echo esc_attr( $overlay_color ); ?>"><?php echo $caption; ?></span>
									<?php } ?>
									<?php if ( $portfolio_link_exists ) { ?>
									<span class="grve-portfolio-btns"><?php echo $portfolio_link_type_title; ?></span>
									<?php } ?>
								</figcaption>
							</figure>
							<?php echo $link_end; ?>
						<?php
							}
						}
						?>

					</article>
<?php
			if ( 'carousel' == $portfolio_style ) {
				echo '</div>';
			}

		endwhile;

		?>
				</div>
<?php
			if ( 'yes' != $disable_pagination ) {
				$total = $query->max_num_pages;
				$big = 999999999; // need an unlikely integer
				if( $total > 1 )  {
					 echo '<div class="grve-pagination">';

					 if( !$current_page = $paged )
						 $current_page = 1;
					 if( get_option('permalink_structure') ) {
						 $format = 'page/%#%/';
					 } else {
						 $format = '&paged=%#%';
					 }
					 echo paginate_links(array(
						'base'			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'		=> $format,
						'current'		=> max( 1, $paged ),
						'total'			=> $total,
						'mid_size'		=> 2,
						'type'			=> 'list',
						'prev_text'	=> '<i class="grve-icon-nav-left"></i>',
						'next_text'	=> '<i class="grve-icon-nav-right"></i>',
						'add_args' => false,
					 ));
					 echo '</div>';
				}
			}
?>
			</div>

		<?php

		else :
		endif;
		wp_reset_postdata();

		return ob_get_clean();

	}
	add_shortcode( 'grve_portfolio', 'grve_portfolio_shortcode' );

}

/**
 * Add shortcode to Visual Composer
 */

vc_map( array(
	"name" => __( "Portfolio", "grve-osmosis-vc-extension" ),
	"description" => __( "Display Portfolio element in multiple styles", "grve-osmosis-vc-extension" ),
	"base" => "grve_portfolio",
	"class" => "",
	"icon"      => "icon-wpb-grve-portfolio",
	"category" => __( "Content", "js_composer" ),
	"params" => array(
		array(
			"type" => "dropdown",
			"heading" => __( "Style", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_style",
			"admin_label" => true,
			'value' => array(
				__( 'Grid' , 'grve-osmosis-vc-extension' ) => 'grid',
				__( 'Multi Grid' , 'grve-osmosis-vc-extension' ) => 'multi-grid',
				__( 'Masonry' , 'grve-osmosis-vc-extension' ) => 'masonry',
				__( 'Small Media' , 'grve-osmosis-vc-extension' ) => 'small-media',
				__( 'Carousel' , 'grve-osmosis-vc-extension' ) => 'carousel',
			),
			"description" => __( "Select a style for your portfolio", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Columns", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_columns",
			"value" => array( '2', '3', '4' ),
			"std" => 4,
			"description" => __( "Select your Porfolio Columns.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Image Mode", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_image_mode",
			'value' => array(
				__( 'Auto Crop', 'grve-osmosis-vc-extension' ) => '',
				__( 'Resize', 'grve-osmosis-vc-extension' ) => 'resize',
			),
			"description" => __( "Select your Portfolio Image Mode.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'masonry', 'small-media' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Link Type", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_link_type",
			"admin_label" => true,
			'value' => array(
				__( 'Classic Portfolio' , 'grve-osmosis-vc-extension' ) => 'item',
				__( 'Gallery Usage' , 'grve-osmosis-vc-extension' ) => 'popup',
				__( 'Custom Link' , 'grve-osmosis-vc-extension' ) => 'custom-link',
			),
			"description" => __( "Select the link type of your portfolio items.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'carousel' ) ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Link Type Title", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_link_type_title",
			"value" => "More Details",
			"description" => __( "Enter your title for your link.", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "grve_multi_checkbox",
			"heading" => __("Portfolio Categories", "grve-osmosis-vc-extension" ),
			"param_name" => "categories",
			"value" => grve_osmosis_vce_get_portfolio_categories(),
			"description" => __( "Select all or multiple categories.", "grve-osmosis-vc-extension" ),
			"admin_label" => true,
			"group" => __( "Categories", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Filter", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_filter",
			"description" => __( "If selected, an isotope filter will be displayed.", "grve-osmosis-vc-extension" ),
			"value" => Array( __( "Enable Portfolio Filter ( Only for All or Multiple Categories )", "grve-osmosis-vc-extension" ) => 'yes' ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'small-media' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Filter Order By", "grve-osmosis-vc-extension" ),
			"param_name" => "filter_order_by",
			"value" => array(
				__( "Default ( Unordered )", "grve-osmosis-vc-extension" ) => '',
				__( "ID", "grve-osmosis-vc-extension" ) => 'id',
				__( "Slug", "grve-osmosis-vc-extension" ) => 'slug',
				__( "Title", "grve-osmosis-vc-extension" ) => 'title',
			),
			"description" => '',
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'small-media' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Filter Order", "grve-osmosis-vc-extension" ),
			"param_name" => "filter_order",
			"value" => array(
				__( "Ascending", "grve-osmosis-vc-extension" ) => 'ASC',
				__( "Descending", "grve-osmosis-vc-extension" ) => 'DESC',
			),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'small-media' ) ),
			"description" => '',
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Filter Alignment", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_filter_align",
			"value" => array(
				__( "Left", "grve-osmosis-vc-extension" ) => 'left',
				__( "Right", "grve-osmosis-vc-extension" ) => 'right',
				__( "Center", "grve-osmosis-vc-extension" ) => 'center',
			),
			"description" => '',
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'small-media' ) ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Enable Loader", "grve-osmosis-vc-extension" ),
			"param_name" => "item_spinner",
			"description" => __( "If selected, this will enable a graphic spinner before load.", "grve-osmosis-vc-extension" ),
			"value" => Array( __( "Enable Loader.", "grve-osmosis-vc-extension" ) => 'yes' ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry' ) ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Disable Pagination", "grve-osmosis-vc-extension" ),
			"param_name" => "disable_pagination",
			"description" => __( "If selected, pagination will not be shown.", "grve-osmosis-vc-extension" ),
			"value" => Array( __( "Disable Pagination.", "grve-osmosis-vc-extension" ) => 'yes' ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'small-media' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Items per page", "grve-osmosis-vc-extension" ),
			"param_name" => "items_per_page",
			"value" => array( '3', '4', '5' ),
			"description" => __( "Number of images per page", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Gutter between images", "grve-osmosis-vc-extension" ),
			"param_name" => "item_gutter",
			"value" => array(
				__( "Yes", "grve-osmosis-vc-extension" ) => 'yes',
				__( "No", "grve-osmosis-vc-extension" ) => 'no',
			),
			"description" => __( "Add gutter among images.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry' ) ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Items to show", "grve-osmosis-vc-extension" ),
			"param_name" => "items_to_show",
			"value" => '12',
			"description" => __( "Maximum Portfolio Items to Show", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Hide Portfolio Title", "grve-osmosis-vc-extension" ),
			"param_name" => "hide_portfolio_title",
			"value" => Array( __( "If selected, portfolio title will be hidden", "grve-osmosis-vc-extension" ) => 'yes' ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Hide Portfolio Description", "grve-osmosis-vc-extension" ),
			"param_name" => "hide_portfolio_caption",
			"value" => Array( __( "If selected, portfolio description will be hidden", "grve-osmosis-vc-extension" ) => 'yes' ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Hide Portfolio Likes", "grve-osmosis-vc-extension" ),
			"param_name" => "hide_portfolio_like",
			"value" => Array( __( "If selected, portfolio likes will be hidden", "grve-osmosis-vc-extension" ) => 'yes' ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Hover Style", "grve-osmosis-vc-extension" ),
			"param_name" => "portfolio_hover_style",
			'value' => array(
				__( 'Style 1' , 'grve-osmosis-vc-extension' ) => 'hover-style-1',
				__( 'Style 2' , 'grve-osmosis-vc-extension' ) => 'hover-style-2',
				__( 'Style 3' , 'grve-osmosis-vc-extension' ) => 'hover-style-3',
			),
			"description" => __( "Select hover style for portfolio items.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Image Zoom Effect", "grve-osmosis-vc-extension" ),
			"param_name" => "zoom_effect",
			"value" => array(
				__( "Zoom In", "grve-osmosis-vc-extension" ) => 'in',
				__( "Zoom Out", "grve-osmosis-vc-extension" ) => 'out',
				__( "None", "grve-osmosis-vc-extension" ) => 'none',
			),
			"description" => __( "Choose the image zoom effect.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_hover_style", 'value' => array( 'hover-style-1' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Overlay Color", "grve-osmosis-vc-extension" ),
			"param_name" => "overlay_color",
			"value" => array(
				__( "Dark", "grve-osmosis-vc-extension" ) => 'dark',
				__( "Light", "grve-osmosis-vc-extension" ) => 'light',
				__( "Primary 1", "grve-osmosis-vc-extension" ) => 'primary-1',
				__( "Primary 2", "grve-osmosis-vc-extension" ) => 'primary-2',
				__( "Primary 3", "grve-osmosis-vc-extension" ) => 'primary-3',
				__( "Primary 4", "grve-osmosis-vc-extension" ) => 'primary-4',
				__( "Primary 5", "grve-osmosis-vc-extension" ) => 'primary-5',
			),
			"description" => __( "Choose the image color overlay.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Overlay Opacity", "grve-osmosis-vc-extension" ),
			"param_name" => "overlay_opacity",
			"value" => array( '0', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100' ),
			"std" => 80,
			"description" => __( "Choose the opacity for the overlay.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'grid', 'multi-grid', 'masonry', 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Autoplay", "grve-osmosis-vc-extension" ),
			"param_name" => "auto_play",
			"value" => array(
				__( "Yes", "grve-osmosis-vc-extension" ) => 'yes',
				__( "No", "grve-osmosis-vc-extension" ) => 'no',
			),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Slideshow Speed", "grve-osmosis-vc-extension" ),
			"param_name" => "slideshow_speed",
			"value" => '3000',
			"description" => __( "Slideshow Speed in ms.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		array(
			"type" => 'checkbox',
			"heading" => __( "Pause on Hover", "grve-osmosis-vc-extension" ),
			"param_name" => "pause_hover",
			"value" => Array( __( "If selected, carousel will be paused on hover", "grve-osmosis-vc-extension" ) => 'yes' ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Navigation Type", "grve-osmosis-vc-extension" ),
			"param_name" => "navigation_type",
			'value' => array(
				__( 'Style 1' , 'grve-osmosis-vc-extension' ) => '1',
				__( 'Style 2' , 'grve-osmosis-vc-extension' ) => '2',
				__( 'Style 3' , 'grve-osmosis-vc-extension' ) => '3',
				__( 'Style 4' , 'grve-osmosis-vc-extension' ) => '4',
				__( 'No Navigation' , 'grve-osmosis-vc-extension' ) => '0',
			),
			"description" => __( "Select your Navigation type.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Navigation Color", "grve-osmosis-vc-extension" ),
			"param_name" => "navigation_color",
			'value' => array(
				__( 'Light' , 'grve-osmosis-vc-extension' ) => 'light',
				__( 'Dark' , 'grve-osmosis-vc-extension' ) => 'dark',
			),
			"description" => __( "Select the background Navigation color.", "grve-osmosis-vc-extension" ),
			"dependency" => Array( 'element' => "portfolio_style", 'value' => array( 'carousel' ) ),
		),
		$grve_vce_add_order_by,
		$grve_vce_add_order,
		$grve_vce_add_margin_bottom,
		$grve_vce_add_el_class,
	)
) );

?>