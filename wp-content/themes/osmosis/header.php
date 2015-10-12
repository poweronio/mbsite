<!doctype html>

<!--[if lt IE 10]>
<html class="ie9 no-js grve-responsive" <?php language_attributes(); ?>>
<![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html class="no-js grve-responsive" <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">

		<!-- viewport -->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<!-- allow pinned sites -->
		<meta name="application-name" content="<?php bloginfo('name'); ?>" />
        <link href="../../../boilerplate.css" rel="stylesheet" type="text/css">
        <link href="../../../index.css" rel="stylesheet" type="text/css">
        <link href="../../../audio3_html5.css" rel="stylesheet" type="text/css">

		<?php
		$grve_favicon = grve_option('favicon','','url');
		if ( ! empty( $grve_favicon ) ) {
		?>
		<link href="<?php echo esc_url( $grve_favicon ); ?>" rel="icon" type="image/x-icon">
		<?php
		}
		?>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        
        

		<?php wp_head(); ?>

<!--
        <script src="../../../respond.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <script src="../../../../js/jquery.mousewheel.min.js" type="text/javascript"></script>
        <script src="../../../../js/jquery.touchSwipe.min.js" type="text/javascript"></script>
        <script src="../../../js/audio3_html5.js" type="text/javascript"></script>
        <script>
		jQuery(function() {

			jQuery('#audio3_html5_white').audio3_html5({
				skin: 'whiteControllers',
				playerPadding:7,
				autoPlay:true,
				
				playlistPadding:0,				
				playlistBgColor:'#000000',
				playlistRecordBgOffColor:'#000000',
				playlistRecordBgOnColor:'#000000',
				playlistRecordBottomBorderOffColor:'#323232',
				playlistRecordBottomBorderOnColor:'#323232',
				playlistRecordTextOffColor:'#7b7b7b',
				playlistRecordTextOnColor:'#0cbbfe'	
			});		
			
			
		});
	</script>
-->
	</head>

	<body id="grve-body" <?php body_class(); ?>>

		<?php
			$grve_logo_align = grve_option( 'logo_align', 'left' );
			$grve_menu_align = grve_option( 'menu_align', 'right' );

			$grve_header_menu_options_align = grve_option( 'header_menu_options_align', 'right' );
			$grve_header_menu_options = grve_visibility( 'header_menu_options_enabled' ) ? $grve_header_menu_options_align : 'no';

			if( 'no' != $grve_header_menu_options ) {
				if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_menu_items' ) ) {
					$grve_header_menu_options = 'no';
				} else {
					if ( grve_woocommerce_enabled() ) {
						if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_menu_items' ) ) {
							$grve_header_menu_options = 'no';
						}
					}
				}
			}

			$grve_menu_type = grve_option( 'menu_type', 'simply' );
			$grve_go_to_section = '';
			$grve_disable_sticky = '';
			if ( is_singular( 'page' ) || is_singular( 'portfolio' ) ) {
				$grve_menu_type = grve_post_meta( 'grve_main_navigation_menu_type', $grve_menu_type );
				$grve_go_to_section = grve_post_meta( 'grve_go_to_section', $grve_go_to_section );
				$grve_disable_sticky = grve_post_meta( 'grve_disable_sticky', $grve_disable_sticky );
			} else {
				if ( grve_woocommerce_enabled() ) {
					if ( is_shop() && !is_search() ) {
						$grve_menu_type = grve_post_meta_shop( 'grve_main_navigation_menu_type', $grve_menu_type );
						$grve_go_to_section = grve_post_meta_shop( 'grve_go_to_section', $grve_go_to_section );
						$grve_disable_sticky = grve_post_meta_shop( 'grve_disable_sticky', $grve_disable_sticky );
					}
				}
			}

			$grve_safe_button_align = grve_option( 'safe_button_align', 'right' );
			$grve_safe_button = grve_visibility( 'safe_button_enabled' ) ? $grve_safe_button_align : 'no';

			if( 'no' != $grve_safe_button ) {
				if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_safe_button' ) ) {
					$grve_safe_button = 'no';
				} else {
					if ( grve_woocommerce_enabled() ) {
						if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_safe_button' ) ) {
							$grve_safe_button = 'no';
						}
					}
				}
			}

			$grve_feature_data = grve_get_feature_data();
			$grve_logo_background = grve_option( 'logo_background' );

			$grve_sticky_header_type = grve_option( 'header_sticky_type', 'simply');
			$grve_sticky_header = grve_visibility( 'header_sticky_enabled' ) ? $grve_sticky_header_type : 'none';
			if ( 'none' != $grve_sticky_header && 'yes' == $grve_disable_sticky ) {
				$grve_sticky_header = 'none';
			}

			$grve_top_bar = grve_visibility( 'top_bar_enabled' ) ? 'yes' : 'no';

			if( 'no' != $grve_top_bar ) {
				if ( is_singular() && 'yes' == grve_post_meta( 'grve_disable_top_bar' ) ) {
					$grve_top_bar = 'no';
				} else {
					if ( grve_woocommerce_enabled() ) {
						if ( is_shop() && !is_search() && 'yes' == grve_post_meta_shop( 'grve_disable_top_bar' ) ) {
							$grve_top_bar = 'no';
						}
					}
				}
			}

			$grve_back_to_top = grve_visibility( 'back_to_top_enabled' ) ? 'yes' : 'no';
			$grve_main_menu = grve_get_header_nav();
		?>

		<?php
			if ( $grve_main_menu != 'disabled' ) {

			$grve_menu_responsive_style = grve_option( 'menu_responsive_style', '1' );
		?>

		<!-- Responsive Menu -->
		<nav id="grve-main-menu-responsive" class="grve-style-<?php echo esc_attr( $grve_menu_responsive_style ); ?>">
			<a class="grve-close-menu-button grve-icon-close" href="#"></a>
			<div class="grve-menu-scroll">
				<?php grve_print_header_menu_options(); ?>
				<?php grve_header_nav( $grve_main_menu ); ?>
			</div>
		</nav>
		<!-- End Responsive Menu -->

		<?php
			}
		?>
		<!-- Theme Wrapper -->
		<div id="grve-theme-wrapper">

			<header id="grve-header" data-gotosection="<?php echo esc_attr( $grve_go_to_section ); ?>" data-fullscreen="<?php echo esc_attr( $grve_feature_data['data_fullscreen'] ); ?>" data-overlap="<?php echo esc_attr( $grve_feature_data['data_overlap'] ); ?>" data-sticky-header="<?php echo esc_attr( $grve_sticky_header ); ?>" data-logo-background="<?php echo esc_attr( $grve_logo_background ); ?>" data-logo-align="<?php echo esc_attr( $grve_logo_align ); ?>" data-menu-align="<?php echo esc_attr( $grve_menu_align ); ?>" data-menu-type="<?php echo esc_attr( $grve_menu_type ); ?>" data-safebutton="<?php echo esc_attr( $grve_safe_button ); ?>" data-topbar="<?php echo esc_attr( $grve_top_bar ); ?>" data-menu-options="<?php echo esc_attr( $grve_header_menu_options ); ?>" data-header-position="<?php echo esc_attr( $grve_feature_data['data_header_position'] ); ?>" data-backtotop="<?php echo esc_attr( $grve_back_to_top ); ?>" class="<?php echo esc_attr( $grve_feature_data['header_style'] ); ?>">
				<?php grve_print_header_top_bar(); ?>
				<?php
					if ( 'bellow-feature' == $grve_feature_data['data_header_position'] ) {
						grve_print_header_feature();
					}
				?>
				<!-- Logo, Main Navigation, Header Options -->
				<div id="grve-inner-header">

					<div class="grve-container">

				<?php
					$logo_text_tag_start = '<h1 class="grve-logo grve-logo-text">';
					$logo_tag_start = '<h1 class="grve-logo">';
					$logo_tag_end = '</h1>';
					if ( 0 == grve_option( 'logo_header_tag', 1 ) ) {
						$logo_text_tag_start = '<div class="grve-logo grve-logo-text">';
						$logo_tag_start = '<div class="grve-logo">';
						$logo_tag_end = '</div>';
					}
					
					if ( grve_visibility( 'logo_as_text_enabled' ) ) {
				?>
						<?php echo $logo_text_tag_start; ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo('name'); ?></a>
						<?php echo $logo_tag_end; ?>	
				<?php
					} else {
				?>
						<?php echo $logo_tag_start; ?>	
				<?php
						$grve_default_logo = grve_option( 'logo','','url' );
						if ( !empty( $grve_default_logo ) ) {
							$grve_logo = grve_get_logo_data( 'logo', 'retina_logo' );
							$grve_logo_dark = grve_get_logo_data( 'logo_dark', 'retina_logo_dark', $grve_logo['url'], $grve_logo['data'] );
							$grve_logo_light = grve_get_logo_data( 'logo_light', 'retina_logo_light', $grve_logo['url'], $grve_logo['data'] );
							$grve_logo_sticky = grve_get_logo_data( 'logo_sticky', 'retina_logo_sticky', $grve_logo['url'], $grve_logo['data'] );
							
							$grve_logo = apply_filters( 'grve_header_logo', $grve_logo );
							$grve_logo_dark = apply_filters( 'grve_header_logo_dark', $grve_logo_dark );
							$grve_logo_light = apply_filters( 'grve_header_logo_light', $grve_logo_light );
							$grve_logo_sticky = apply_filters( 'grve_header_logo_sticky', $grve_logo_sticky );
				?>
							<a class="grve-default" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $grve_logo['url'] ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"<?php echo $grve_logo['data']; ?>></a>
							<a class="grve-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $grve_logo_dark['url'] ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"<?php echo $grve_logo_dark['data']; ?>></a>
							<a class="grve-light" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $grve_logo_light['url'] ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"<?php echo $grve_logo_light['data']; ?>></a>
							<a class="grve-sticky" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $grve_logo_sticky['url'] ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"<?php echo $grve_logo_sticky['data']; ?>></a>
				<?php
						}
				?>
							<span><?php bloginfo('name'); ?></span>
						<?php echo $logo_tag_end; ?>
				<?php
					}
				?>


					<?php grve_print_header_menu_options(); ?>

					<?php
						if ( $grve_main_menu != 'disabled' ) {
					?>

						<!-- Main Menu -->
						<?php $grve_submenu_animation = grve_option( 'submenu_animation', 'none' ); ?>
						<?php $grve_submenu_pointer = grve_option( 'submenu_pointer', 'none' ); ?>
						<?php $grve_responsive_menu_selection = grve_option( 'menu_responsive_toggle_selection', 'icon' ); ?>
						<?php $grve_responsive_menu_text = grve_option( 'menu_responsive_toggle_text'); ?>

						<?php if ( 'icon' == $grve_responsive_menu_selection ) { ?>
							<div class="grve-responsive-menu-button">
								<div class="grve-menu-button">
									<div class="grve-menu-button-line"></div>
									<div class="grve-menu-button-line"></div>
									<div class="grve-menu-button-line"></div>
								</div>
							</div>
						<?php } else {?>
							<div class="grve-responsive-menu-text">
								<?php echo $grve_responsive_menu_text; ?>
							</div>
						<?php } ?>

						<nav id="grve-main-menu" class="grve-menu-pointer-<?php echo esc_attr( $grve_submenu_pointer ); ?>" data-animation-style="<?php echo esc_attr( $grve_submenu_animation ); ?>">
							<?php grve_header_nav( $grve_main_menu ); ?>
						</nav>
						<!-- End Main Menu -->

					<?php
						} else {

							do_action( 'grve_header_container_custom_menu_integration' );
						}
					?>

					</div>

					<?php grve_print_header_safe_options(); ?>
					<?php
						do_action( 'grve_header_inner_custom_menu_integration' );
						if ( class_exists('UberMenu') && 'ubermenu' == grve_option( 'menu_header_integration', 'default' )) {
							uberMenu_direct( 'grve_header_nav' );
						}
					?>

				</div>
				<div class="clear"></div>

				<!-- End Logo, Main Navigation, Header Options -->

				<?php
					if ( 'above-feature' == $grve_feature_data['data_header_position'] ) {
						grve_print_header_feature();
					}
				?>
				<!-- End Feature Section -->

				<?php grve_print_header_newsletter_modal(); ?>

				<?php grve_print_header_social_modal(); ?>

				<?php grve_print_header_search_modal(); ?>

				<?php grve_print_header_shop_modal(); ?>

				<?php grve_print_header_language_selector_modal(); ?>

				<?php do_action( 'grve_header_modal_container' ); ?>

				<div class="grve-popup-overlay"></div>

			</header>
