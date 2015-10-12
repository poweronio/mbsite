<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package klein
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<!-- favicon -->
<?php $favicon_default = get_template_directory_uri() . '/favicon.ico'; ?>
<?php $favicon = ot_get_option( 'favicon', $favicon_default ); ?>
<link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $favicon; ?>" type="image/x-icon" />
<!-- favicon end -->
<!-- custom background css -->
<?php klein_custom_background(); ?>
<!-- custom background css end -->
<!-- custom typography settings -->
<?php klein_custom_typography(); ?>
<!-- custom typography settings end -->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	$container_class = ot_get_option( 'container', 'boxed' );
?>
<div id="page" class="hfeed site <?php echo $container_class; ?>">
	<?php do_action( 'before' ); ?>
	<div id="bp-klein-top-bar">
		<nav id="bp-klein-user-bar" class="container" role="navigation">
			<div class="row">
				<div class="col-xs-4 col-sm-2">
					<div class="site-branding" id="site-name">
						<a id="logo-anchor" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) . ' - ' . get_bloginfo( 'description' ) ); ?>" rel="home">
							<?php
								$default_logo = get_template_directory_uri() . '/logo.png';
								$logo = ot_get_option( 'logo', $default_logo );
							?>
							<h1><?php bloginfo( 'name' ); ?></h1>
							<img src="<?php echo $logo; ?>" alt="<?php echo get_bloginfo( 'name' ); ?> - <?php echo get_bloginfo( 'description' ); ?>"/>
						</a>
					</div>
				</div>
				<?php $menu_cols = klein_the_menu_columns(); ?>
				<div class="desktop-menu col-xs-<?php echo esc_attr($menu_cols['menu']); ?> visible-sm visible-md visible-lg">
						<div class="pull-right">
						<?php
							$defaults = array(
								'theme_location'  => 'primary',
								'container'       => 'div',
								'container_class' => 'navigation-desktop',
								'container_id'    => 'main-menu-desktop',
								'menu_class'      => 'menu desktop',
								'echo' => 0,
								'fallback_cb' => false
							);
						?>
						<?php $main_menu = wp_nav_menu( $defaults ); ?>
						<?php if (empty($main_menu)) { ?>
							<ul class="no-mg-bottom top-links-menu-container no-list">
								<li>
									<a class="mg-top-5 btn btn-primary" title="<?php _e('Select Top Links Menu', 'klein');?>" href="<?php echo admin_url(); ?>/nav-menus.php?action=locations">
										<i class="fa fa-info-circle"></i>
										<?php _e('Select/Create new Main Menu', 'klein');?>
									</a>
								</li>
							</ul>
						<?php } else {?> 
							<?php echo $main_menu; ?>
						<?php } ?>
						</div>
						<div class="clearfix"></div>
				</div>
				
				<div id="bp-klein-user-bar-action" class="col-sm-<?php echo esc_attr($menu_cols['actions']); ?> col-xs-6">
					<div class="pull-right">
						<?php if( is_user_logged_in() ){ ?>
							<?php
								global $current_user;
								get_currentuserinfo();
								$user_login = $current_user->user_login;
							?>
							<?php if( function_exists('bp_core_get_user_domain' ) ){ ?>
								
								<div class="top-profile-avatar">
									<?php echo get_avatar($current_user->ID, 32); ?>
								</div>
								
								<?php klein_user_nav(); ?>

							<?php } ?>
						<?php }else{ ?>
							<?php klein_login_register_link(); ?>
						<?php } ?>
					</div>
				</div>

				<div class="small-screen-device-nav col-xs-2 visible-xs">
						<nav id="site-navigation" class="main-navigation pull-right" role="navigation">
							<div id="menu">
								<div data-dropdown=".menu.mobile" class="nav-btn fa fa-reorder" aria-hidden="true"></div>
							</div>
						</nav><!-- #site-navigation -->
						<?php
							$defaults = array(
								'theme_location'  => 'primary',
								'container'       => 'ul',
								'container_class' => 'navigation',
								'container_id'    => 'main-menu',
								'menu_class'      => 'menu mobile clearfix',
							);
						?>
				</div>
				<div class="mobile-menu-container">
					<?php wp_nav_menu( $defaults ); ?>
				</div>	

			</div>
		</nav>
	</div>
		
	<div class="clearfix"></div>
	
	<?php 
	// Revolution Slider Support
	// for front-page-rev-slider.php
	// page template
	if( klein_has_rev_slider() ){ ?>
		<div id="klein-rev-slider">
			<?php $rev_slider_id = ot_get_option( 'front_page_slider_id', '' ); ?>
			<?php putRevSlider( $rev_slider_id ); ?>
		</div>	
	<?php }?>
	<?php if(function_exists('is_buddypress')) { ?>
		<?php if( is_buddypress() ) { ?>
			<div id="main" class="bp-container site-main">
		<?php } else { ?>
			<div id="main" class="container site-main">
		<?php } ?>
	<?php } else { ?>
			<div id="main" class="container no-bp site-main">
	<?php } ?>
		