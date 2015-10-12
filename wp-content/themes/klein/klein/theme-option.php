<?php
/**
 * Theme Option
 * 
 * @package Klein
 * @since 1.0
 */

// ---------------------------------------------------------------

/**
 * Bootstrap Theme Options
 */
add_action( 'admin_init', 'custom_theme_options', 1 );

/**
 * One Click Install
 */
require get_template_directory() .'/klein/one-click-install/init.php';

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {

    // Get a copy of the saved settings array.
    $saved_settings = get_option( 'option_tree_settings', array() );

    // one click install template
    ob_start();
       // $oci_template = Radium_Theme_Importer::intro_html();
    $oci_template_output = ob_get_clean();

    // shortcode reference template
    ob_start();
    include_once dirname( __FILE__ ) . '/theme-option-shortcodes-ref.php';
    $shortcode_reference = ob_get_clean();

    /**
      * Custom settings array that will eventually be 
      * passes to the OptionTree Settings API Class.
      */
    $klein_options_settings = array(
     'contextual_help' => array(
        'content' => array( 
            array(
                'id' => 'general_help',
                'title' => 'General',
                'content' => '<p>Thank you for purchasing Klein WordPress Theme. Please visit http://dunhakdis.ticksy.com for support and inquiry.</p>'
                )
            ),
        'sidebar'  => '<p>:)</p>',
     ),
     'sections'        => array(
        array(
           'id'          => 'general',
           'title'       => 'General'
           ),
        array(
           'id'          => 'header',
           'title'       => 'Header'
           ),
        array(
            'id'          => 'logo',
            'title'       => 'Adjust Logo'
            ),
        array(
           'id'          => 'social_media',
           'title'       => 'Social Media'
           ),
        array(
           'id'			=> 'layouts',
           'title'		=> 'Page Layouts'
           ),
        array(
           'id'			=> 'skin',
           'title'		=> 'Theme Colors'
           ),
        array(
            'id'      => 'misc',
            'title'   => 'Miscellaneous'
            ),
        array(
           'id'          => 'custom_css',
           'title'       => 'Custom CSS'
           ),
        array(
            'id'          => 'shortcode_reference',
            'title'       => 'Shortcodes'
            ),
        ),
     'settings'   => array(

      array(
        'id' => 'shortcode_reference_page',
        'label' => 'Shortcode Reference',
        'desc' => $shortcode_reference,
        'type' => 'textblock',
        'section' => 'shortcode_reference'
        ),
      array(
       'id'          => 'favicon',
       'label'       => 'Favicon',
       'desc'        => 'Select an image to use as favicon for your site. Please upload an icon file (.ico), other file types might not be supported in other browsers such as IE. Recommended size is 16x16 (pixels). <a href="http://www.thesitewizard.com/archive/favicon.shtml" target="_blank" title="About Favicons">Click here to learn more about favicons.</a>',
       'std'         => get_template_directory_uri() . '/favicon.ico',
       'type'        => 'upload',
       'section'     => 'general',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),
      array(
       'id'          => 'logo',
       'label'       => 'Site Logo',
       'desc'        => 'Ideal image dimensions is 135x50 (pixels). Use your favorite image manipulation tool in-order to re-size or crop your logo.',
       'std'         => get_template_directory_uri() . '/logo.png',
       'type'        => 'upload',
       'section'     => 'general',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),
      array(
       'id'          => 'container',
       'label'       => 'Container',
       'desc'        => 'Select what\'s the right layout for your site.',
       'std'         => 'boxed',
       'type'        => 'select',
       'section'     => 'layouts',
       'class'       => '',
       'choices'     => array(
        array( 'value' => 'fluid', 'label' => 'Fluid' ),
        array( 'value' => 'boxed', 'label' => 'Boxed' ),
        )

       ),
      array(
       'id'          => 'base_preset',
       'label'       => 'Base Preset',
       'desc'        => 'Base preset skin you want your site to have.',
       'std'         => '',
       'type'        => 'select',
       'section'     => 'skin',
       'class'       => '',
       'choices'     => array(
        array( 'value' => 'default', 'label' => 'Default' ),
        array( 'value' => 'turquoise', 'label' => 'Turquoise' ),
        array( 'value' => 'alizarin', 'label' => 'Alizarin' ),
        array( 'value' => 'amethyst', 'label' => 'Amethyst' ),
        array( 'value' => 'asbestos', 'label' => 'Asbestos' ),
        array( 'value' => 'carrot', 'label' => 'Carrot' ),
        array( 'value' => 'emerald', 'label' => 'Emerald' ),
        array( 'value' => 'peter-river', 'label' => 'Peter River' ),
        array( 'value' => 'sun-flower', 'label' => 'Sun Flower' )
        )

       ),
      array(
       'id'          => 'dark_layout_enable',
       'label'       => 'Dark Layout',
       'desc'        => 'Do you want to enable dark layout?',
       'type'        => 'checkbox',
       'section'     => 'skin',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),
      array(
       'id'          => 'background',
       'label'       => 'Background (Boxed Container)',
       'desc'        => 'Upload a new background for your WordPress site.',
       'std'         => '',
       'type'        => 'background',
       'section'     => 'skin',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),

      array(
       'id'          => 'header_fonts',
       'label'       => 'Header Fonts',
       'desc'        => 'Leave blank to use the default font (Source Sans Pro). Otherwise, select the font you want to apply for the headings.',
       'std'         => '',
       'type'        => 'typography',
       'section'     => 'general',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),

      array(
       'id'          => 'body_fonts',
       'label'       => 'Body Fonts',
       'desc'        => 'Leave blank to use the default font (Source Sans Pro). Otherwise, select the font you want to apply for the body.',
       'std'         => '',
       'type'        => 'typography',
       'section'     => 'general',
       'class'       => '',
       'choices'     => array(
        array( 
         'value' => 'yes',
         'label' => 'Yes' 
         )
        )
       ),

      /*Header Options*/
      array(
       'id'          => 'header_style',
       'label'       => 'Header Style',
       'desc'        => 'Select your favourite header style.',
       'type'        => 'select',
       'section'     => 'header',
       'class'       => '',
       'std' => 'style-3',
       'choices'     => array(
         array(
          'value' => 'default',
          'label' => 'Header Style 1'
          ),
         array(
          'value' => 'minimal',
          'label' => 'Header Style 2 (Minimal)'
          ),
         array(
          'value' => 'style-3',
          'label' => 'Header Style 3 (Social Links + Top Links Menu)'
          ),
         array(
          'value' => 'style-4',
          'label' => 'Header Style 4 (Top Links Menu + Social Links)'
          ),
         array(
          'value' => 'style-5',
          'label' => 'Header Style 5 (Social Links + Top Links Menu | Below Main Menu)'
          ),
         array(
          'value' => 'style-6',
          'label' => 'Header Style 6 (Top Links Menu + Social Links | Below Main Menu)'
          ),
         )
       ),
      array(
         'id'          => 'enable_login',
         'label'       => 'Login Button',
         'desc'        => 'Hide/Show login modal on the header',
         'std'         => 'on',
         'type'        => 'on-off',
         'section'     => 'header',
         'class'       => '',
       ),
      array(
         'id'          => 'enable_register',
         'label'       => 'Register Button',
         'desc'        => 'Hide/Show login register button on the header. Please do not forget to enable the registration inside "General Settings" > "Membership"',
         'std'         => 'on',
         'type'        => 'on-off',
         'section'     => 'header',
         'class'       => '',
       ),
      array(
         'id'          => 'modal_text',
         'label'       => 'Login Modal Context',
         'desc'        => 'The text to show when user clicks the "Login" button in the header.',
         'std'         => 'Sign in to our website with your favourite social media account. It\'s just one click away!',
         'type'        => 'textarea-simple',
         'section'     => 'header',
         'class'       => '',
       ),
/*End Header Options*/

/*Social Media Options*/
array(
 'id' => 'social_media',
 'section' => 'social_media',
 'type' => 'social-links',
 ),
/*Social Media Options ~END*/

array(
 'id'          => 'smooth_scroll_enable',
 'label'       => 'Smooth Scrolling',
 'desc'        => 'Do you want to activate Smooth Scrolling?',
 'type'        => 'checkbox',
 'section'     => 'general',
 'class'       => '',
 'std'		=> true,
 'choices'     => array(
  array( 
   'value' => 'yes',
   'label' => 'Yes' 
   )
  )
 ),
array(
 'id'          => 'copyright_text',
 'label'       => 'Copyright Text',
 'desc'        => 'Copyright notice and other cool stuff.',
 'std'         => 'Copyright &copy; 2013 Klein WordPress Theme (Co. Reg. No. 123456789). All Rights Reserved. Your Company Inc.',
 'type'        => 'textarea',
 'section'     => 'general',
 'class'       => '',
 'choices'     => array(
  array( 
   'value' => 'yes',
   'label' => 'Yes' 
   )
  )
 ),
/*Logo Adjust*/
array(
  'id'          => 'logo_adjust_top',
  'label'       => 'Logo Top Spacing',
  'desc'        => 'Add Top Spacing to your Logo.',
  'std'       => 0,
  'type'        => 'numeric-slider',
  'section'     => 'logo',
  'class'       => '',
  'min_max_step' => '-25, 50,1'
  ),
array(
  'id'          => 'logo_adjust_left',
  'label'       => 'Logo Left Spacing',
  'desc'        => 'Add Left Spacing to your Logo.',
  'std'         => 0,
  'type'        => 'numeric-slider',
  'section'     => 'logo',
  'class'       => '',
  'min_max_step' => '-25, 50,1'
  ),

/*Miscellanous*/
array(
  'id'          => 'menu_search',
  'label'       => 'Menu Search',
  'desc'        => 'Enable or Disable the search feature in the menu.',
  'std'         => 'on',
  'type'        => 'on-off',
  'section'     => 'misc',
  'class'       => '',
  ),
array(
  'id'          => 'sticky_menu',
  'label'       => 'Sticky Menu',
  'desc'        => 'Enable or Disable Sticky Menu.',
  'std'         => 'on',
  'type'        => 'on-off',
  'section'     => 'misc',
  'class'       => '',
  ),
array(
  'id'          => 'back_to_top',
  'label'       => 'Back to Top',
  'desc'        => 'Enable or Disable Back to Top arrow.',
  'std'         => 'on',
  'type'        => 'on-off',
  'section'     => 'misc',
  'class'       => '',
  ),
array(
  'id'          => 'sharer',
  'label'       => 'Blog Sharing',
  'desc'        => 'Enable or Disable "Blog Sharing" below each post.',
  'std'         => 'on',
  'type'        => 'on-off',
  'section'     => 'misc',
  'class'       => '',
  ),
array(
  'id'          => 'author_profile',
  'label'       => 'Author Profile',
  'desc'        => 'Enable or Disable "Author Profile" below each post.',
  'std'         => 'on',
  'type'        => 'on-off',
  'section'     => 'misc',
  'class'       => '',
  ),
array(
 'id'          => 'css',
 'label'       => 'CSS',
 'desc'        => 'Custom CSS here, it\'s a good practice that you do your changes here and not directly edit css files inside the theme.<br><br><strong>Caveats:</strong>There is a dynamic.css file in the root of this theme. This file handles the css codes you enter into this textarea. Some server may restrict file writing due to permission issues. Please set the permission of the said file to 0777. If this doesn\'t work, you may try playing with different permission. 0777 permission often works on my end.',
 'std'         => '',
 'type'        => 'css',
 'section'     => 'custom_css',
 'class'       => '',
 'choices'     => array(
  array( 
   'value' => 'yes',
   'label' => 'Yes' 
   )
  )
 )

)
);

  // if plugin gears is installed
  // add the configuration for each module

if( class_exists( 'Gears' ) ){

	// facebook connect section
    $klein_options_settings['sections'][] = array(
        'id'          => 'facebook_connect',
        'title'       => 'FB Sign-in'
        );


	$klein_options_settings['settings'][] = array(
		'id'          => 'is_fb_enabled',
		'label'       => 'Enable Facebook Connect',
		'desc'        => 'Check to enable Facebok Connect/Register. Make sure to enable the registration in general settings.',
		'type'        => 'checkbox',
		'section'     => 'facebook_connect',
		'choices'     => array(
			array(
				'value' => 'yes',
				'label' => 'Yes' 
				)
			)
		);

	$klein_options_settings['settings'][] = array(
		'id'          => 'gears_fb_btn_label',
		'label'       => 'Button Label',
		'desc'        => 'Enter a custom label for your facebook connect button to replace the default text (Connect w/ Facebook)',
		'type'        => 'text',
		'section'     => 'facebook_connect'
		);

	$klein_options_settings['settings'][] = array(
		'id'          => 'application_secret',
		'label'       => 'Application Secret',
		'desc'        => '',
		'type'        => 'text',
		'section'     => 'facebook_connect'
		);

	$klein_options_settings['settings'][] = array(
		'id'          => 'application_id',
		'label'       => 'Application ID',
		'desc'        => 'Enter your Facebook <b>App ID</b> and <b>App Secret</b> in the following text field. <a href="http://goo.gl/LTtQFK" target="_blank">Click here to locate your App ID and Key.</a>',
		'type'        => 'text',
		'section'     => 'facebook_connect'
		);

	$klein_options_settings['settings'][] = array(
		'id'          => 'application_secret',
		'label'       => 'Application Secret',
		'desc'        => '',
		'type'        => 'text',
		'section'     => 'facebook_connect'
		);

    // google connect section
    
    $klein_options_settings['sections'][] = array(
        'id'          => 'gp_connect',
        'title'       => 'Google Sign-in'
        );

     $klein_options_settings['settings'][] = array(
        'id'          => 'google_api_enabled',
        'label'       => 'Enable Google Connect',
        'desc'        => 'Check to enable Google Connect. Make sure to enable the registration in general settings.',
        'type'        => 'checkbox',
        'section'     => 'gp_connect',
        'choices'     => array(
            array(
                'value' => 'yes',
                'label' => 'Yes' 
                )
            )
        );

    $klein_options_settings['settings'][] = array(
        'id'          => 'google_api_button_label',
        'label'       => 'Button Label',
        'std' => 'Google+',
        'desc'        => 'Enter a custom label for your Google Connect button to replace the default text (Google+)',
        'type'        => 'text',
        'section'     => 'gp_connect'
     );
    
    $klein_options_settings['settings'][] = array(
        'id'          => 'google_api_client_id',
        'label'       => 'Client ID',
        'desc'        => 'Please provide your Google App Client ID. Kindly head over to <a target="_blank" href="console.developers.google.com/project" title="Google Developers Console"> Google Developers Console</a> to find your \'Client ID\'',
        'type'        => 'text',
        'section'     => 'gp_connect'
        );

    $klein_options_settings['settings'][] = array(
        'id'          => 'google_api_client_secret',
        'label'       => 'Client Secret',
        'desc'        => 'Please provide your Google App Client Secret. Kindly head over to <a target="_blank" href="console.developers.google.com/project" title="Google Developers Console"> Google Developers Console</a> to find your \'Client Secret\'',
        'type'        => 'text',
        'section'     => 'gp_connect'
        );

    $google_api_callback_uri = admin_url('admin-ajax.php?action=clientConnectionInit');

    $klein_options_settings['settings'][] = array(
        'id'          => 'google_api_callback_instruction',
        'desc'       => '<p><strong><span style="color:#E74C3C;">IMPORTANT</span>: In your Google Developer Console. Please set the "REDIRECT URIS" to the following:</strong></p><strong>REDIRECT URIS: </strong>
                         <code>'.$google_api_callback_uri.'</code>',
        'type'        => 'textblock',
        'section'     => 'gp_connect'
       );


}

 // buddypress options
if( class_exists( 'BuddyPress' ) )
{
	
	$klein_options_settings['settings'][] = array(
		'id'          => 'bp_layout',
		'label'       => 'BuddyPress Default',
		'desc'        => 'Default layout for BuddyPress pages such as member\'s profile page, groups, etc.',
		'std'         => '',
		'type'        => 'select',
		'section'     => 'layouts',
		'class'       => '',
		'choices'     => array(
			array( 'value' => 'right-sidebar', 'label' => 'Sidebar Right' ),
			array( 'value' => 'left-sidebar', 'label' => 'Sidebar Left' ),
			array( 'value' => 'full-width', 'label' => 'Full Width' ),
			)
		);
}

// Woocommerce options
if( class_exists( 'Woocommerce' ) )
{

	$klein_options_settings['settings'][] = array(
		'id'          => 'wc_layout',
		'label'       => 'WooCommerce Product Index',
		'desc'        => 'Default Layout for WooCommerce Index.',
		'std'         => '',
		'type'        => 'select',
		'section'     => 'layouts',
		'class'       => '',
		'choices'     => array(
			array( 'value' => 'content-sidebar', 'label' => 'Sidebar Right' ),
			array( 'value' => 'sidebar-content', 'label' => 'Sidebar Left' ),
			array( 'value' => 'sidebar-content-sidebar', 'label' => 'Sidebar Content Sidebar' ),
			array( 'value' => 'content-sidebar-sidebar', 'label' => 'Content Dual Sidebar' ),
			array( 'value' => 'sidebar-sidebar-content', 'label' => 'Dual Sidebar Content' ),
			array( 'value' => 'full-content', 'label' => 'Full Width' )
			)
		);
}

// bbPress options
if( class_exists( 'bbPress' ) )
{

	$klein_options_settings['settings'][] = array(
		'id'          => 'bbp_layout',
		'label'       => 'bbPress Forum Index',
		'desc'        => 'Default Layout for bbPress Forum Index.',
		'std'         => '',
		'type'        => 'select',
		'section'     => 'layouts',
		'class'       => '',
		'choices'     => array(
			array( 'value' => 'content-sidebar', 'label' => 'Sidebar Right' ),
			array( 'value' => 'sidebar-content', 'label' => 'Sidebar Left' ),
			array( 'value' => 'sidebar-content-sidebar', 'label' => 'Sidebar Content Sidebar' ),
			array( 'value' => 'content-sidebar-sidebar', 'label' => 'Content Dual Sidebar' ),
			array( 'value' => 'sidebar-sidebar-content', 'label' => 'Dual Sidebar Content' ),
			array( 'value' => 'full-content', 'label' => 'Full Width' )
			)
		);
}
  // if settings are not the same as the record update the DB
if ( $saved_settings !== $klein_options_settings ) {
	update_option( 'option_tree_settings', $klein_options_settings ); 
}
}
?>