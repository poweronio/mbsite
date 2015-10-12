<?php
/**
 * Theme Customization File
 *
 * @package klein
 */


function klein_register_theme_customizer( $wp_customize )
{

    // Theme settings color option
    $wp_customize->add_section( 'klein_color_options',
        array(
            'title'     => 'Colors',
            'priority'  => 200
        )
    );

    // Top Bar Background Settings 
    $klein_top_bar_background = array(
            'default' => '',
            'transport' => 'postMessage',
        );

        $wp_customize->add_setting('klein_top_bar_background', $klein_top_bar_background);

            $klein_footer_widget_background_settings_controls = array(
                    'label' => __('Top Bar Background', 'klein'),
                    'section' => 'klein_color_options',
                    'settings' => 'klein_top_bar_background',
                );

                    $wp_customize->add_control(
                            new WP_Customize_Color_Control(
                                    $wp_customize,
                                    'klein_top_bar__background',
                                    $klein_footer_widget_background_settings_controls
                                )
                        );

    // Top Bar Color
    $klein_top_bar_color = array(
            'default' => '',
            'transport' => 'postMessage',
        );

        $wp_customize->add_setting('klein_top_bar_color', $klein_top_bar_color);

            $klein_footer_widget_background_settings_controls = array(
                    'label' => __('Top Bar Links Color', 'klein'),
                    'section' => 'klein_color_options',
                    'settings' => 'klein_top_bar_color',
                );

                    $wp_customize->add_control(
                            new WP_Customize_Color_Control(
                                    $wp_customize,
                                    'klein_top_bar__color',
                                    $klein_footer_widget_background_settings_controls
                                )
                        );


    // Navigation background register setting
    $wp_customize->add_setting( 'klein_navigation_background',
        array(
            'default'    =>  '',
            'transport'  =>  'postMessage'
        )
    );

    // Navigation background settings control
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'navigation_background',
            array(
                'label'      => __( 'Navigation Background', 'klein' ),
                'section'    => 'klein_color_options',
                'settings'   => 'klein_navigation_background'
            )
        )
    );

    // Navigation foreground register settings
    $klein_navigation_color_settings = array(
        'transport' => 'postMessage'
    );

    $wp_customize->add_setting( 'klein_navigation_color', $klein_navigation_color_settings );

    // Navigation foreground settings color
    $klein_navigation_color_control = array(
        'label'      => __( 'Navigation Link', 'klein' ),
        'section'    => 'klein_color_options',
        'settings'   => 'klein_navigation_color'
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'navigation_link',
            $klein_navigation_color_control
        )
    );

    // Navigation hover color
    $klein_navigation_hover_settings = array(
        'transport' => 'refresh'
    );
    $wp_customize->add_setting( 'klein_navigation_hover', $klein_navigation_hover_settings );
    // Controls
    $klein_navigation_hover_controls = array(
        'label' => __( 'Navigation Hover', 'klein' ),
        'section' => 'klein_color_options',
        'settings' => 'klein_navigation_hover'
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'navigation_hover',
            $klein_navigation_hover_controls
        )
    );

    // Footer Widgets
    $klein_footer_widget_background_settings = array(
            'default' => '',
            'transport' => 'postMessage',
        );

        $wp_customize->add_setting('klein_footer_widget_background_settings', $klein_footer_widget_background_settings);

            $klein_footer_widget_background_settings_controls = array(
                    'label' => __('Footer Widgets Background', 'klein'),
                    'section' => 'klein_color_options',
                    'settings' => 'klein_footer_widget_background_settings',
                );

                    $wp_customize->add_control(
                            new WP_Customize_Color_Control(
                                    $wp_customize,
                                    'klein_footer_widget_background',
                                    $klein_footer_widget_background_settings_controls
                                )
                        );

    // Footer background color
    $klein_footer_background_settings = array(
        'default' => '',
        'transport' => 'postMessage'
    );

        $wp_customize->add_setting( 'klein_footer_background', $klein_footer_background_settings );

            // Controls
            $klein_footer_background_controls = array(
                'label' => __( 'Footer Background', 'klein' ),
                'section' => 'klein_color_options',
                'settings' => 'klein_footer_background'
            );

                $wp_customize->add_control(
                    new WP_Customize_Color_Control(
                        $wp_customize,
                        'footer_background',
                        $klein_footer_background_controls
                    )
                );

    // Footer color
    $klein_footer_color_settings = array(
        'default' => '',
        'transport' => 'postMessage'
    );

    $wp_customize->add_setting( 'klein_footer_color', $klein_footer_color_settings );
    
    // Controls
    $klein_footer_color_controls = array(
        'label' => __( 'Footer Color', 'klein' ),
        'section' => 'klein_color_options',
        'settings' => 'klein_footer_color'
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'klein_footer_color',
            $klein_footer_color_controls
        )
    );
}

add_action( 'customize_register', 'klein_register_theme_customizer' );

function klein_customizer_css()
{
?>
    <style type="text/css">
	    <?php $klein_navigation_background = get_theme_mod( 'klein_navigation_background' ); ?>
	    <?php if (!empty($klein_navigation_background)) { ?>
             #bp-klein-top-bar, .desktop-menu ul.menu.desktop li.columnize > ul.sub-menu,.desktop-menu ul.sub-menu li a { background: <?php echo $klein_navigation_background; ?>; }
        <?php } ?>

        <?php
    /**
     * Footer Background
     */
    $klein_footer_background = get_theme_mod('klein_footer_background');
    if (!empty($klein_footer_background)) { ?>
		#footer {background: <?php echo get_theme_mod( 'klein_footer_background' ); ?>;}
	<?php } ?>
	<?php
    /**
     * Footer Color
     */
    $klein_footer_color = get_theme_mod('klein_footer_color');
    if (!empty($klein_footer_color)) { ?>
         #footer{ color: <?php echo get_theme_mod( 'klein_footer_color' ); ?>; }
<?php }
    /**
     * Navigation Background
     */
     $klein_navigation_background = get_theme_mod('klein_navigation_background');
    
     if (!empty($klein_navigation_background)) {
?>
          .desktop-menu ul.sub-menu li:first-child:before,
          .desktop-menu ul.children li:first-child:before{
                border-bottom-color: <?php echo get_theme_mod( 'klein_navigation_background' ); ?>
              }
          .desktop-menu ul.sub-menu > li > ul.sub-menu > li:first-child:before, .desktop-menu ul.children > li > ul.children > li:first-child:before{
                border-right-color: <?php echo get_theme_mod( 'klein_navigation_background' ); ?>
              }
<?php } ?>

<?php
    /**
     * Top Bar Background ul li a
     */
    $kleinTopBarBackground = get_theme_mod('klein_top_bar_background');
        if (!empty($kleinTopBarBackground)) {
            echo sprintf('#klein-top-links {background-color:%s}', $kleinTopBarBackground);
        }
    /**
     * Top Bar Color
     */
    $kleinTopBarColor = get_theme_mod('klein_top_bar_color');
        if (!empty($kleinTopBarBackground)) {
            echo sprintf('#klein-top-links ul li a {color: %s;}', $kleinTopBarColor);
        }
    /**
     * Navigation Color
     */
     $klein_navigation_color = get_theme_mod('klein_navigation_color');
     if (!empty($klein_navigation_color)) { ?>
		.desktop-menu ul.menu.desktop li a{ color: <?php echo get_theme_mod('klein_navigation_color'); ?> }
<?php } ?>

<?php /*
       * Navigation Hover
       */
       $klein_navigation_hover = get_theme_mod('klein_navigation_hover');
       if (!empty($klein_navigation_hover)) {
       ?>
		.desktop-menu ul.sub-menu li a:hover{
			background: <?php echo get_theme_mod( 'klein_navigation_hover'); ?>
		}
<?php } ?>
    <?php $klein_footer_widgets_background = get_theme_mod('klein_footer_widget_background_settings'); ?>
    <?php if (!empty($klein_footer_widgets_background)) { ?>
        #footer-widgets { background:<?php echo $klein_footer_widgets_background; ?>; }
    <?php } ?>
    </style>
    <?php
}


add_action( 'wp_head', 'klein_customizer_css' );

function klein_customizer_live_preview()
{
    wp_enqueue_script(
        'klein-theme-customizer',
        get_template_directory_uri() . '/js/customizer.js?__xcache=' . time(),
        array( 'jquery', 'customize-preview' ),
        '0.3.0',
        true
    );
}


add_action( 'customize_preview_init', 'klein_customizer_live_preview' );
?>
