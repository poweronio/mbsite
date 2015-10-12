<?php

/*
*	Header Helper functions
*
* 	@version	1.0
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/

 /**
 * Get Logo Data
 */
function grve_get_logo_data( $logo_id, $retina_logo_id, $fallback_logo_url = '', $fallback_logo_data = '' ) {

	$logo_url = grve_option( $logo_id, '', 'url' );
	$logo_data = '';

	if ( empty( $logo_url ) ) {
		$logo_url = $fallback_logo_url;
		$logo_data = $fallback_logo_data;
	} else {
		$retina_logo = grve_option( $retina_logo_id, '' , 'url' );
		if ( !empty( $retina_logo ) ) {
			$logo_data .= ' data-at2x="' . esc_attr( $retina_logo ) . '"';
		} else {
			$logo_data .= ' data-no-retina=""';
		}
		$logo_width = grve_option( $logo_id, '', 'width' );
		$logo_height = grve_option( $logo_id, '', 'height' );
		if ( !empty( $logo_width ) && !empty( $logo_height ) ) {
			$logo_data .= ' width="' . esc_attr( $logo_width ) . '" height="' . esc_attr( $logo_height ) . '"';
			$logo_data .= ' style="height:' . esc_attr( $logo_height + 10 ) . 'px;"';
		}
	}

	return array(
		'url' => $logo_url,
		'data' => $logo_data,
	);

}

 /**
 * Prints correct title/subtitle for all cases
 */
function grve_header_title() {
	global $post;
	$page_title = $page_description = $page_reversed = '';

	//Shop
	if( grve_woocommerce_enabled() && is_shop() && !is_search() ) {

		$post_id = wc_get_page_id( 'shop' );
		$page_title   = get_the_title( $post_id );
		$page_description = get_post_meta( $post_id, 'grve_page_description', true );
		return array(
			'title' => $page_title,
			'description' => $page_description,
		);
	}
	//Events Calendar Overview Pages
	if ( grve_events_calendar_is_overview() ) {
		return array(
			'title' => tribe_get_events_title( true ),
			'description' => '',
		);
	}

	//Main Pages
	if ( is_front_page() && is_home() ) {
		// Default homepage
		$page_title = get_bloginfo( 'name' );
		$page_description = get_bloginfo( 'description' );
	} else if ( is_front_page() ) {
		// static homepage
		$page_title = get_bloginfo( 'name' );
		$page_description = get_bloginfo( 'description' );
	} else if ( is_home() ) {
		// blog page
		$page_title = get_bloginfo( 'name' );
		$page_description = get_bloginfo( 'description' );
	} else if( is_search() ) {
		$page_description = __( 'Search Results for :', GRVE_THEME_TRANSLATE );
		$page_title = esc_attr( get_search_query() );
		$page_reversed = 'reversed';
	} else if ( is_singular() ) {
		$post_id = $post->ID;
		$post_type = get_post_type( $post_id );
		//Single Post
		if ( $post_type == 'page' && is_singular( 'page' ) ) {
			$page_title = get_the_title();
			$page_description = get_post_meta( $post_id, 'grve_page_description', true );
		} else if ( $post_type == 'portfolio' && is_singular( 'portfolio' ) ) {
			$page_title = get_the_title();
			$page_description = get_post_meta( $post_id, 'grve_portfolio_description', true );
		} else if ( grve_events_calendar_enabled() && $post_type == 'tribe_events' && is_singular( 'tribe_events' ) ) {
			$page_title = get_the_title();
			$page_description = tribe_events_event_schedule_details( $post_id, '', '' );
			if ( tribe_get_cost() ) {
				$page_description .= '<span class="grve-event-cost grve-bg-primary-1">' . tribe_get_cost( null, true ) . '</span>';
			}
		} else if ( grve_events_calendar_enabled() && $post_type == 'tribe_organizer' && is_singular( 'tribe_organizer' ) ) {
			$page_title = get_the_title();
			$page_description = grve_event_organizer_title_meta();
		} else {
			$page_title = get_the_title();
		}


	} else if ( is_archive() ) {
		//Post Categories
		if ( is_category() ) {
			$page_title = single_cat_title("", false);
			$page_description = category_description();
		} else if ( is_tag() ) {
			$page_description = __( "Posts Tagged :", GRVE_THEME_TRANSLATE );
			$page_title = single_tag_title("", false);
			$page_reversed = 'reversed';
		} else if ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			$page_description = __( "Posts By :", GRVE_THEME_TRANSLATE );
			$page_title = $userdata->display_name;
			$page_reversed = 'reversed';
		} else if ( is_day() ) {
			$page_description = __( "Daily Archives :", GRVE_THEME_TRANSLATE );
			$page_title = get_the_time( 'l, F j, Y' );
			$page_reversed = 'reversed';
		} else if ( is_month() ) {
			$page_description = __( "Monthly Archives :", GRVE_THEME_TRANSLATE );
			$page_title = get_the_time( 'F Y' );
			$page_reversed = 'reversed';
		} else if ( is_year() ) {
			$page_description = __( "Yearly Archives :", GRVE_THEME_TRANSLATE );
			$page_title = get_the_time( 'Y' );
			$page_reversed = 'reversed';
		} else {
			if( grve_woocommerce_enabled() && is_tax() ) {
				$page_title = single_term_title( "", false );
			} else {
				$page_title = __( "Archives", GRVE_THEME_TRANSLATE );
			}
		}
	} else {
		$page_title = get_bloginfo( 'name' );
		$page_description = get_bloginfo( 'description' );
	}

	return array(
		'title' => $page_title,
		'description' => $page_description,
		'reversed' => $page_reversed,
	);


}

 /**
 * Check title visibility
 */
function grve_check_title_visibility() {

	$blog_title = grve_option( 'blog_title', 'sitetitle' );

	if ( is_front_page() && is_home() ) {
		// Default homepage
		if ( 'none' == $blog_title ) {
			return false;
		}
	} elseif ( is_front_page() ) {
		// static homepage
		if ( 'yes' == grve_post_meta( 'grve_disable_title' ) ) {
			return false;
		}
	} elseif ( is_home() ) {
		// blog page
		if ( 'none' == $blog_title ) {
			return false;
		}
	} else {
		if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_title' ) ) {
			return false;
		}
		if( grve_woocommerce_enabled() ) {
			// Product / Disabled Title in Shop
			if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_title' ) ) {
				return false;
			} else {
				if ( is_product() ) {
					return grve_visibility( 'product_title_visibility' );
				}
				if( is_product_category() || is_product_tag() ) {
					return grve_visibility( 'product_tax_title_visibility' );
				}
			}
		}

		if ( grve_events_calendar_enabled() ) {
			return true;
		}
	}

	return true;

}

/**
 * Prints Title Background Image Container
 */
function grve_print_title_bg_image_container( $bg_image, $grve_custom_bg = array() ) {

	$bg_mode = grve_array_value( $grve_custom_bg, 'mode' );
	if ( !empty( $bg_mode ) ) {
		$bg_position = grve_array_value( $grve_custom_bg, 'position', 'center-center' );
		$bg_image = grve_array_value( $grve_custom_bg, 'image' );
	}
	if ( 'featured' == $bg_mode && has_post_thumbnail() ) {
		$media_id = get_post_thumbnail_id();
		$full_src = wp_get_attachment_image_src( $media_id, 'grve-image-fullscreen' );
		$image_url = esc_url( $full_src[0] );
	} else if ( 'custom' == $bg_mode && !empty( $bg_image ) ) {
		$image_url = $bg_image;
	} else {
		$media = grve_option( $bg_image, '', 'media' );
		if( isset( $media['id'] ) && !empty( $media['id'] ) ) {
			$media_id = $media['id'];
			$bg_position = grve_option( $bg_image, 'center center', 'background-position' );
			$bg_position = str_replace( " ", "-", $bg_position );
			$full_src = wp_get_attachment_image_src( $media_id, 'grve-image-fullscreen' );
			$image_url = esc_url( $full_src[0] );
		}
	}

	if( !empty( $image_url ) ) {
		echo '<div class="grve-bg-image grve-bg-position-' . esc_attr( $bg_position ) . '" style="background-image: url(' . $image_url . ');"></div>';
	}

}

 /**
 * Prints title/subtitle ( Page )
 */
function grve_print_header_title( $mode = '') {

	if ( grve_check_title_visibility() ) {

		$page_title_extra_class = '';
		$header_data = grve_header_title();

		if ( 'blog' == $mode ) {
			$page_title_height = grve_option( 'blog_title_height', '350' );
			$page_title_alignment = grve_option( 'blog_title_alignment', 'center' );
			$page_title_color = grve_option( 'blog_title_color', 'light' );
			$page_description_color = grve_option( 'blog_description_color', 'light' );
			$page_title_extra_class = 'grve-blog-title';
			$bg_image = 'blog_title_background';

		} elseif ( 'event-tax' == $mode ) {
			$page_title_height = grve_option( 'event_tax_title_height', '350' );
			$page_title_alignment = grve_option( 'event_tax_title_alignment', 'center' );
			$page_title_color = grve_option( 'event_tax_title_color', 'light' );
			$page_description_color = grve_option( 'event_tax_description_color', 'light' );
			$page_title_extra_class = 'grve-event-tax-title';
			$bg_image = 'event_tax_title_background';

		} elseif ( 'event' == $mode ) {
			$page_title_height = grve_option( 'event_title_height', '350' );
			$page_title_alignment = grve_option( 'event_title_alignment', 'center' );
			$page_title_color = grve_option( 'event_title_color', 'light' );
			$page_description_color = grve_option( 'event_description_color', 'light' );
			$page_title_extra_class = 'grve-event-title';
			$bg_image = 'event_title_background';

		} elseif ( 'forum' == $mode ) {
			$page_title_height = grve_option( 'forum_title_height', '350' );
			$page_title_alignment = grve_option( 'forum_title_alignment', 'center' );
			$page_title_color = grve_option( 'forum_title_color', 'light' );
			$page_description_color = grve_option( 'forum_description_color', 'light' );
			$page_title_extra_class = 'grve-forum-title';
			$bg_image = 'forum_title_background';
			$header_data['description'] = '';
			if ( !is_singular() ) {
				$header_data['title'] = __( 'Forums' , GRVE_THEME_TRANSLATE );
			}
			if ( function_exists('bbp_is_single_user_edit') && (bbp_is_single_user_edit() || bbp_is_single_user() ) ) {
				$user_info = get_userdata( bbp_get_displayed_user_id() );
				$header_data['title'] = __("Profile for User:", GRVE_THEME_TRANSLATE ) . " " . $user_info->display_name;
				if ( bbp_is_single_user_edit() ) {
					$header_data['title'] = __("Edit profile for User:", GRVE_THEME_TRANSLATE ) . " " . $user_info->display_name;
				}
			}
		} else {
			$page_title_height = grve_option( 'page_title_height', '350' );
			$page_title_alignment = grve_option( 'page_title_alignment', 'center' );
			$page_title_color = grve_option( 'page_title_color', 'light' );
			$page_description_color = grve_option( 'page_description_color', 'light' );
			$bg_image = 'page_title_background';
		}


		$header_title = isset( $header_data['title'] ) ? $header_data['title'] : '';
		$header_description = isset( $header_data['description'] ) ? $header_data['description'] : '';
		$header_reversed = isset( $header_data['reversed'] ) ? $header_data['reversed'] : '';

?>
	<!-- Page Title -->
	<div id="grve-page-title" class="grve-align-<?php echo esc_attr( $page_title_alignment ); ?> <?php echo esc_attr( $page_title_extra_class ); ?>" style="height:<?php echo esc_attr( $page_title_height ); ?>px;">
		<div id="grve-page-title-content" data-height="<?php echo esc_attr( $page_title_height ); ?>">
			<?php do_action( 'grve_page_title_top' ); ?>
			<div class="grve-container">
				<?php if ( empty( $header_reversed ) ) { ?>
					<h1 class="grve-title grve-<?php echo esc_attr( $page_title_color ); ?>"><span><?php echo $header_title; ?></span></h1>
					<?php if ( !empty( $header_description ) ) { ?>
					<div class="grve-description grve-<?php echo esc_attr( $page_description_color ); ?>"><?php echo $header_description; ?></div>
					<?php } ?>
				<?php } else { ?>
					<?php if ( !empty( $header_description ) ) { ?>
					<div class="grve-description grve-<?php echo esc_attr( $page_description_color ); ?>"><?php echo $header_description; ?></div>
					<?php } ?>
					<h1 class="grve-title grve-<?php echo esc_attr( $page_title_color ); ?>"><span><?php echo $header_title; ?></span></h1>
				<?php } ?>
			</div>
			<?php do_action( 'grve_page_title_bottom' ); ?>
		</div>
		<?php grve_print_title_bg_image_container( $bg_image ); ?>
	</div>
	<!-- End Page Title -->
<?php
	}
}

 /**
 * Prints title/subtitle ( Portfolio )
 */
function grve_print_portfolio_header_title( $position = 'top' ) {

	if ( grve_check_title_visibility() ) {

		$portfolio_style = grve_option( 'portfolio_style', 'default' );
		if ( 'simple' == $portfolio_style ) {
?>
			<!-- Post Title -->
			<h1 class="grve-portfolio-simple-title"><span><?php the_title(); ?></span></h1>
			<!-- End Post Title -->
<?php
		} else {
			if ( 'content' != $position ) {
				$page_title_height = grve_option( 'portfolio_title_height', '350' );
				$page_title_alignment = grve_option( 'portfolio_title_alignment', 'left' );
				$page_title_color = grve_option( 'portfolio_title_color', 'light' );
				$page_description_color = grve_option( 'portfolio_description_color', 'light' );
				$bg_image = 'portfolio_title_background';

				$header_data = grve_header_title();
				$header_title = isset( $header_data['title'] ) ? $header_data['title'] : '';
				$header_description = isset( $header_data['description'] ) ? $header_data['description'] : '';
?>
			<!-- Portfolio Title -->
			<div id="grve-portfolio-title" class="grve-align-<?php echo esc_attr( $page_title_alignment ); ?>" style="height:<?php echo esc_attr( $page_title_height ); ?>px;">
				<div id="grve-portfolio-title-content" data-height="<?php echo esc_attr( $page_title_height ); ?>">
					<?php do_action( 'grve_portfolio_title_top' ); ?>
					<div class="grve-container">
						<h1 class="grve-title grve-<?php echo esc_attr( $page_title_color ); ?>"><span><?php echo $header_title; ?></span></h1>
						<?php if ( !empty( $header_description ) ) { ?>
						<div class="grve-description grve-<?php echo esc_attr( $page_description_color ); ?>"><?php echo $header_description; ?></div>
						<?php } ?>
					</div>
					<?php do_action( 'grve_portfolio_title_bottom' ); ?>
				</div>
				<?php grve_print_title_bg_image_container( $bg_image ); ?>
			</div>
			<!-- End Portfolio Title -->
<?php
			}
		}
	}
}

 /**
 * Prints title/subtitle ( Post )
 */
function grve_print_post_header_title( $position = 'top' ) {

	if ( grve_check_title_visibility() ) {

		$post_title_height = grve_option( 'post_title_height', '350' );
		$post_title_color = grve_option( 'post_title_color', 'light' );
		$post_style = grve_option( 'post_style', 'default' );
		$bg_image = 'post_title_background';

		if ( 'simple' == $post_style ) {
?>
			<!-- Post Title -->
			<h1 class="grve-post-simple-title" itemprop="name headline"><span><?php the_title(); ?></span></h1>
			<!-- End Post Title -->
<?php
		} else {
			if ( 'content' != $position ) {
				global $post;
				$grve_post_title_bg = get_post_meta( $post->ID, 'grve_post_title_bg', true );
				$bg_mode = grve_array_value( $grve_post_title_bg, 'mode' );
				if ( !empty( $bg_mode ) ) {
					$post_title_height = grve_array_value( $grve_post_title_bg, 'height', '350' );
				}
?>
			<!-- Post Title -->
			<div id="grve-post-title" class="grve-align-center" style="height:<?php echo esc_attr( $post_title_height ); ?>px;">
				<div id="grve-post-title-content" data-height="<?php echo esc_attr( $post_title_height ); ?>">
					<?php do_action( 'grve_post_title_top' ); ?>
					<div class="grve-container">
						<h1 class="grve-title grve-<?php echo esc_attr( $post_title_color ); ?>" itemprop="name headline"><span><?php the_title(); ?></span></h1>
						<?php
							if ( 'default' == $post_style ) {
								grve_print_post_social( $post_title_color );
							}
						?>
					</div>
					<?php do_action( 'grve_post_title_bottom' ); ?>
				</div>
				<?php grve_print_title_bg_image_container( $bg_image, $grve_post_title_bg ); ?>
			</div>
			<!-- End Post Title -->
<?php
			}
		}
	}
}

 /**
 * Prints title( WooCommerce Product )
 */
function grve_print_product_header_title( $mode = '') {

	$page_title_extra_class = '';

	if ( grve_check_title_visibility() ) {

		if ( 'taxonomy' == $mode ) {
			$page_title_height = grve_option( 'product_tax_title_height', '350' );
			$page_title_alignment = grve_option( 'product_tax_title_alignment', 'center' );
			$page_title_color = grve_option( 'product_tax_title_color', 'light' );
			$page_title_extra_class = 'grve-product-tax-title';
			$bg_image = 'product_tax_title_background';
		} else {
			$page_title_height = grve_option( 'product_title_height', '350' );
			$page_title_alignment = grve_option( 'product_title_alignment', 'center' );
			$page_title_color = grve_option( 'product_title_color', 'light' );
			$bg_image = 'product_title_background';
		}

		$header_data = grve_header_title();
		$header_title = isset( $header_data['title'] ) ? $header_data['title'] : '';

?>
	<!-- Product Title -->
	<div id="grve-product-title" class="grve-align-<?php echo esc_attr( $page_title_alignment ); ?> <?php echo esc_attr( $page_title_extra_class ); ?>" style="height:<?php echo esc_attr( $page_title_height ); ?>px;">
		<div id="grve-product-title-content" data-height="<?php echo esc_attr( $page_title_height ); ?>">
			<?php do_action( 'grve_product_title_top' ); ?>
			<div class="grve-container">
				<h1 class="grve-title grve-<?php echo esc_attr( $page_title_color ); ?>"><span><?php echo $header_title; ?></span></h1>
			</div>
			<?php do_action( 'grve_product_title_bottom' ); ?>
		</div>
		<?php grve_print_title_bg_image_container( $bg_image ); ?>
	</div>
	<!-- End Product Title -->
<?php
	}
}

/**
 * Prints header top bar text
 */
function grve_print_header_top_bar_text( $text ) {
	if ( !empty( $text ) ) {
?>
		<li class="grve-topbar-item"><p><?php echo do_shortcode( $text ); ?></p></li>
<?php
	}
}

/**
 * Prints header top bar options
 */
function grve_print_header_top_bar_options( $options ) {

	if ( !empty( $options ) ) {

?>
		<li class="grve-topbar-item">
			<ul class="grve-options">
				<?php if ( isset( $options['search'] ) && 1 == $options['search'] ) { ?>
				<li><a href="#grve-search-modal" class="grve-icon-search grve-open-popup-link"></a></li>
				<?php } ?>
				<?php if ( isset( $options['newsletter'] ) && 1 == $options['newsletter'] ) { ?>
				<li><a href="#grve-newsletter-modal" class="grve-icon-envelope grve-open-popup-link"></a></li>
				<?php } ?>
			</ul>
		</li>
<?php
	}

}
/**
 * Prints header top bar socials
 */
function grve_print_header_top_bar_socials( $options ) {

	$social_options = grve_option('social_options');
	if ( !empty( $options ) && !empty( $social_options ) ) {
		?>
			<li class="grve-topbar-item">
				<ul class="grve-social">
		<?php
		foreach ( $social_options as $key => $value ) {
			if ( isset( $options[$key] ) && 1 == $options[$key] && $value ) {
				if ( 'skype' == $key ) {
					echo '<li><a href="' . $value . '" class="grve-icon-' . esc_attr( $key ) . '"></a></li>';
				} else {
					echo '<li><a href="' . esc_url( $value ) . '" target="_blank" class="grve-icon-' . esc_attr( $key ) . '"></a></li>';
				}
			}
		}
		?>
				</ul>
			</li>
		<?php
	}

}

/**
 * Prints header top bar language selector
 */
function grve_print_header_top_bar_language_selector() {

	//start language selector output buffer
    ob_start();

	$languages = '';

	//Polylang
	if( function_exists( 'pll_the_languages' ) ) {
		$languages = pll_the_languages( array( 'raw'=>1 ) );

		$lang_option_current = $lang_options = '';

		foreach ( $languages as $l ) {

			if ( !$l['current_lang'] ) {
				$lang_options .= '<li>';
				$lang_options .= '<a href="' . $l['url'] . '" class="grve-language-item">';
				$lang_options .= '<img src="' . $l['flag'] . '" alt="' . $l['name'] . '"/>';
				$lang_options .= $l['name'];
				$lang_options .= '</a>';
				$lang_options .= '</li>';
			} else {
				$lang_option_current .= '<a href="#" class="grve-language-item">';
				$lang_option_current .= '<img src="' . $l['flag'] . '" alt="' . $l['name'] . '"/>';
				$lang_option_current .= $l['name'];
				$lang_option_current .= '</a>';
			}
		}

	}

	//WPML
	if ( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {

		$languages = icl_get_languages( 'skip_missing=0' );
		if ( ! empty( $languages ) ) {

			$lang_option_current = $lang_options = '';

			foreach ( $languages as $l ) {

				if ( !$l['active'] ) {
					$lang_options .= '<li>';
					$lang_options .= '<a href="' . $l['url'] . '" class="grve-language-item">';
					$lang_options .= '<img src="' . $l['country_flag_url'] . '" alt="' . $l['language_code'] . '"/>';
					$lang_options .= $l['native_name'];
					$lang_options .= '</a>';
					$lang_options .= '</li>';
				} else {
					$lang_option_current .= '<a href="#" class="grve-language-item">';
					$lang_option_current .= '<img src="' . $l['country_flag_url'] . '" alt="' . $l['language_code'] . '"/>';
					$lang_option_current .= $l['native_name'];
					$lang_option_current .= '</a>';
				}
			}
		}
	}
	if ( ! empty( $languages ) ) {

?>
	<li class=" grve-topbar-item">
		<ul class="grve-language">
			<li>
				<?php echo $lang_option_current; ?>
				<ul>
					<?php echo $lang_options; ?>
				</ul>
			</li>
		</ul>
	</li>
<?php
	}
	//store the language selector buffer and clean
	$grve_lang_selector_out = ob_get_clean();
	echo apply_filters( 'grve_header_top_bar_language_selector', $grve_lang_selector_out );
}


/**
 * Prints header top bar
 */
function grve_print_header_top_bar() {

	if ( grve_visibility( 'top_bar_enabled' ) ) {
		if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_top_bar' ) ) {
			return;
		}
		if( grve_woocommerce_enabled() ) {
			// Disabled top Bar in Shop
			if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_top_bar' ) ) {
				return false;
			}
		}
?>
		<!-- Top Bar -->
		<div id="grve-top-bar">

			<div class="grve-container">

				<?php
				if ( grve_visibility( 'top_bar_left_enabled' ) ) {
				?>
				<ul class="grve-bar-content grve-left-side">
					<?php

						//Top Left First Item Hook
						do_action( 'grve_header_top_bar_left_first_item' );

						//Top Left Text
						$grve_left_text = grve_option('top_bar_left_text');
						grve_print_header_top_bar_text( $grve_left_text );

						//Top Left Options
						$top_bar_left_options = grve_option('top_bar_left_options');
						grve_print_header_top_bar_options( $top_bar_left_options );

						//Top Left Language selector
						if ( isset( $top_bar_left_options['language'] ) && 1 == $top_bar_left_options['language'] ) {
							grve_print_header_top_bar_language_selector();
						}

						//Top Left Social
						if ( grve_visibility( 'top_bar_left_social_visibility' ) ) {
							$top_bar_left_social_options = grve_option('top_bar_left_social_options');
							grve_print_header_top_bar_socials( $top_bar_left_social_options );
						}

						//Top Left Last Item Hook
						do_action( 'grve_header_top_bar_left_last_item' );

					?>
				</ul>
				<?php
					}
				?>

				<?php
				if ( grve_visibility( 'top_bar_right_enabled' ) ) {
				?>
				<ul class="grve-bar-content grve-right-side">
					<?php

						//Top Right First Item Hook
						do_action( 'grve_header_top_bar_right_first_item' );

						//Top Right Text
						$grve_right_text = grve_option('top_bar_right_text');
						grve_print_header_top_bar_text( $grve_right_text );

						//Top Right Options
						$top_bar_right_options = grve_option('top_bar_right_options');
						grve_print_header_top_bar_options( $top_bar_right_options );

						//Top Right Language selector
						if ( isset( $top_bar_right_options['language'] ) && 1 == $top_bar_right_options['language'] ) {
							grve_print_header_top_bar_language_selector();
						}
						//Top Right Social
						if ( grve_visibility( 'top_bar_right_social_visibility' ) ) {
							$top_bar_right_social_options = grve_option('top_bar_right_social_options');
							grve_print_header_top_bar_socials( $top_bar_right_social_options );
						}

						//Top Right Last Item Hook
						do_action( 'grve_header_top_bar_right_last_item' );

					?>


				</ul>
				<?php
					}
				?>
			</div>

		</div>
		<!-- End Top Bar -->
<?php

	}
}

/**
 * Prints header safe buttons e.g: social, language selector, search
 */
function grve_print_header_safe_options() {

	if ( grve_visibility( 'safe_button_enabled' ) ) {
		$safe_button_options = grve_option('safe_button_options');

		if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_safe_button' ) ) {
			return false;
		}
		if( grve_woocommerce_enabled() ) {
			if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_safe_button' ) ) {
				return false;
			}
		}
?>
		<!-- Safe Options -->
		<ul id="grve-header-options">
			<li>
				<a class="grve-open-button grve-icon-safebutton" href="#"></a>
				<nav class="grve-options-wrapper">
					<ul class="grve-options">
<?php

						//Safe Button First Item Hook
						do_action( 'grve_header_safebutton_first_item' );

						if ( !empty( $safe_button_options ) ) {
							foreach ( $safe_button_options as $key => $value ) {
								if( 1 == $value ) {
									if ( 'search' == $key ) {
									?>
										<li><a href="#grve-search-modal" class="grve-open-popup-link"><i class="grve-icon grve-icon-search"></i><span><?php echo grve_option( 'safe_button_option_search_text', '&nbsp;' ); ?></span></a></li>
									<?php
									} else if ( 'language' == $key ) {
									?>
										<li><a href="#grve-language-modal" class="grve-open-popup-link"><i class="grve-icon grve-icon-globe "></i><span><?php echo grve_option( 'safe_button_option_language_text', '&nbsp;' ); ?></span></a></li>
									<?php
									} else if ( 'newsletter' == $key ) {
									?>
										<li><a href="#grve-newsletter-modal" class="grve-open-popup-link"><i class="grve-icon grve-icon-envelope"></i><span><?php echo grve_option( 'safe_button_option_newsletter_text', '&nbsp;' ); ?></span></a></li>
									<?php
									}
								}
							}
						}

						if ( grve_visibility( 'safe_button_social_visibility' ) ) {
						?>
							<li><a href="#grve-share-modal" class="grve-open-popup-link"><i class="grve-icon grve-icon-socials"></i><span><?php echo grve_option( 'safe_button_option_social_text', '&nbsp;' ); ?></span></a></li>
						<?php
						}

						//Safe Button Last Item Hook
						do_action( 'grve_header_safebutton_last_item' );
?>
					</ul>
				</nav>
			</li>
		</ul>
		<!-- End Safe Options -->
<?php
	}

}

/**
 * Prints header safe buttons e.g: social, language selector, search
 */
function grve_print_header_menu_options() {

	if ( grve_visibility( 'header_menu_options_enabled' ) ) {

		if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_menu_items' ) ) {
			return false;
		}
		if( grve_woocommerce_enabled() ) {
			if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_menu_items' ) ) {
				return false;
			}
		}

		$header_menu_options = grve_option('header_menu_options');

?>
		<!-- Menu Options -->
		<ul class="grve-menu-options">
<?php
			do_action( 'grve_header_menu_options_first_item' );

			if ( !empty( $header_menu_options ) ) {
				foreach ( $header_menu_options as $key => $value ) {
					if( 1 == $value ) {
						if ( 'cart' == $key && grve_woocommerce_enabled() ) {
							global $woocommerce;
						?>
							<li><a href="#grve-shop-modal" class="grve-icon-shopping-cart grve-open-popup-link"><span class="grve-purchased-items"><?php echo $woocommerce->cart->cart_contents_count; ?></span></a></li>
						<?php
						} else if ( 'search' == $key ) {
						?>
							<li><a href="#grve-search-modal" class="grve-icon-search grve-open-popup-link"></a></li>
						<?php
						} else if ( 'language' == $key ) {
						?>
							<li><a href="#grve-language-modal" class="grve-icon-globe grve-open-popup-link"></a></li>
						<?php
						} else if ( 'newsletter' == $key ) {
						?>
							<li><a href="#grve-newsletter-modal" class="grve-icon-envelope grve-open-popup-link"></a></li>
						<?php
						}
					}
				}
			}

			if ( grve_visibility( 'header_menu_social_visibility' ) ) {
				$header_social_options = grve_option('header_menu_social_options');
				$social_options = grve_option('social_options');
				if ( !empty( $header_social_options ) && !empty( $social_options ) ) {

					foreach ( $social_options as $key => $value ) {
						if ( isset( $header_social_options[$key] ) && 1 == $header_social_options[$key] && $value ) {
							if ( 'skype' == $key ) {
								echo '<li><a href="' . $value . '" class="grve-icon-' . esc_attr( $key ) . '"></a></li>';
							} else {
								echo '<li><a href="' . esc_url( $value ) . '" target="_blank" class="grve-icon-' . esc_attr( $key ) . '"></a></li>';
							}
						}
					}

				}
			}

			do_action( 'grve_header_menu_options_last_item' );
?>
		</ul>
		<!-- End Menu Options -->
<?php

	}

}

/**
 * Prints Header Newsletter modal
 */
function grve_print_header_newsletter_modal() {
?>
		<div id="grve-newsletter-modal" class="grve-modal">
			<div class="grve-modal-content">
				<a href="#" class="grve-close-modal grve-icon-close"></a>
				<div class="grve-newsletter">
					<?php
					if ( class_exists( 'MC4WP_Lite' ) ) {
						echo do_shortcode('[mc4wp_form]');
					}
					?>
				</div>
			</div>
		</div>
<?php
}

/**
 * Prints Header Search modal
 */
function grve_print_header_search_modal() {
		$form = '';
?>
		<div id="grve-search-modal" class="grve-modal">
			<div class="grve-modal-content">
				<a href="#" class="grve-close-modal grve-icon-close"></a>
				<?php echo grve_wpsearch( $form ); ?>
			</div>
		</div>
<?php
}

/**
 * Prints Header Social modal
 */
function grve_print_header_social_modal() {

	if ( grve_visibility('safe_button_social_visibility') ) {
		global $grve_social_list;
		$options = grve_option('safe_button_social_options');
		$social_options = grve_option('social_options');

		echo '<div id="grve-share-modal" class="grve-modal">';
			echo '<div class="grve-modal-content">';
			echo '<a href="#" class="grve-close-modal grve-icon-close"></a>';
			if ( !empty( $options ) && !empty( $social_options ) ) {
				echo '<ul class="grve-social">';
				foreach ( $social_options as $key => $value ) {
					if ( isset( $options[$key] ) && 1 == $options[$key] && $value ) {
						if ( 'skype' == $key ) {
							echo '<li><a href="' . $value . '">' . $grve_social_list[$key] . '</a></li>';
						} else {
							echo '<li><a href="' . esc_url( $value ) . '" target="_blank">' . $grve_social_list[$key] . '</a></li>';
						}
					}
				}
				echo '</ul>';
			}
			echo '</div>';
		echo '</div>';
	}
}

/**
 * Prints Shop modal
 */
function grve_print_header_shop_modal() {

	if ( grve_woocommerce_enabled() ) {
?>

	<div id="grve-shop-modal" class="grve-modal">
		<div class="grve-modal-content">
			<a href="#" class="grve-close-modal grve-icon-close"></a>
			<div class="grve-cart-popup">
				<div class="widget_shopping_cart_content"></div>
			</div>
		</div>
	</div>

<?php
	}
}

/**
 * Prints header language selector
 * WPML is required
 * Can be used to add custom php code for other translation flags.
 */
function grve_print_header_language_selector_modal() {

?>
		<div id="grve-language-modal" class="grve-modal">
<?php
		//start language selector output buffer
		ob_start();


?>
			<div class="grve-modal-content">
				<a href="#" class="grve-close-modal grve-icon-close"></a>
				<ul class="grve-language">


<?php
			//Polylang
			if( function_exists( 'pll_the_languages' ) ) {
				$languages = pll_the_languages( array( 'raw'=>1 ) );
				if ( ! empty( $languages ) ) {
					foreach ( $languages as $l ) {
						echo '<li>';
						if ( !$l['current_lang'] ) {
							echo '<a href="' . $l['url'] . '">';
						} else {
							echo '<a href="#" class="active">';
						}
						echo $l['name'];

						echo '</a></li>';
					}
				}
			}

			//WPML
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
				$languages = icl_get_languages( 'skip_missing=0' );
				if ( ! empty( $languages ) ) {
					foreach ( $languages as $l ) {
						echo '<li>';
						if ( !$l['active'] ) {
							echo '<a href="' . $l['url'] . '">';
						} else {
							echo '<a href="#" class="active">';
						}
						echo $l['native_name'];

						echo '</a></li>';
					}
				}
			}
?>
				</ul>
			</div>
<?php

		//store the language selector buffer and clean
		$grve_lang_selector_out = ob_get_clean();
		echo apply_filters( 'grve_header_language_selector', $grve_lang_selector_out );
?>
		</div>
<?php

}

/**
 * Prints Header navigation for articles ( Posts / Portfolio Items )
 */
function grve_print_header_item_navigation( $element_class = "grve-nav-wrapper") {
	global $post;

	if ( is_singular() ) {
		$post_id = $post->ID;
		$post_type = get_post_type( $post_id );

		if ( ( $post_type == 'post' && is_singular( 'post' ) && grve_visibility( 'post_nav_visibility', '1' ) ) ||
			( $post_type == 'portfolio' && is_singular( 'portfolio' ) ) ||
			( $post_type == 'testimonial' && is_singular( 'testimonial' ) ) ) {

			$grve_in_same_term = false;
			$grve_backlink = '';

			if ( $post_type == 'portfolio' ) {
				$grve_in_same_term = grve_visibility( 'portfolio_nav_same_term', '0' );
				$prev_post = get_adjacent_post( $grve_in_same_term, '', true, 'portfolio_category');
				$next_post = get_adjacent_post( $grve_in_same_term, '', false, 'portfolio_category');
				$grve_backlink = grve_option( 'portfolio_backlink' );
			} elseif ( $post_type == 'post' ) {
				$grve_in_same_term = grve_visibility( 'post_nav_same_term', '0' );
				$prev_post = get_adjacent_post( $grve_in_same_term, '', true);
				$next_post = get_adjacent_post( $grve_in_same_term, '', false);
			} else {
				$prev_post = get_adjacent_post( $grve_in_same_term, '', true);
				$next_post = get_adjacent_post( $grve_in_same_term, '', false);
			}
			echo '<div class="' . $element_class . '">';
			if ( $prev_post || $next_post || !empty( $grve_backlink ) ) {
				echo '<ul class="grve-post-nav">';
				if ( $next_post ) {
					grve_print_item_nav_link( $next_post->ID, 'next', 'hello' );
				}

				if ( $post_type == 'portfolio' ) {
					if ( !empty( $grve_backlink ) ) {
						$portfolio_backlink_url = get_permalink( $grve_backlink );
					?>
						<li><a href="<?php echo $portfolio_backlink_url; ?>" class="grve-icon-th-large grve-backlink"></a></li>
					<?php
					}
				}

				if ( $prev_post ) {
					grve_print_item_nav_link( $prev_post->ID, 'prev' );
				}
				echo '</ul>';
			}
			echo '</div>';

		}
	}
}

function grve_print_item_nav_link( $post_id,  $direction, $title = '' ) {

	$icon_class = 'nav-right';
	if ( 'prev' == $direction ) {
		$icon_class = 'nav-left';
	}
?>
	<li><a href="<?php echo get_permalink( $post_id ); ?>" class="grve-icon-<?php echo $icon_class; ?>" title="<?php echo esc_attr($title); ?>"></a></li>
<?php
}

/**
 * Prints Tracking code for analytics e.g: Google Analytics
 */
add_action('wp_head', 'grve_print_tracking_code');
if ( !function_exists('grve_print_tracking_code') ) {

	function grve_print_tracking_code() {

		$tracking_code = grve_option( 'tracking_code' );
		if ( !empty( $tracking_code ) ) {
?>
		<script type='text/javascript'>

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo grve_option( 'tracking_code' );?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
<?php
		}
	}
}

?>