<?php
    /**
     * Redux Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 2 of the License, or
     * any later version.
     * Redux Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package     ReduxFramework
     * @author      Dovy Paukstys (dovy)
     * @author      Kevin Provance (kprovance), who hacked at it a bit.
     * @version     1.0.1
     */
    /**
     * Class and Function List:
     * Function list:
     * - __construct()
     * - custom_upload_mimes()
     * - getFonts()
     * - addCustomFonts()
     * - ajax()
     * - getValidFiles()
     * - processWebfont()
     * - getMissingFiles()
     * - checkFontFileName()
     * - generateCSS()
     * - generateFontCSS()
     * - get_instance()
     * - overload_field_path()
     * - dynamic_section()
     * Classes list:
     * - ReduxFramework_extension_custom_fonts
     */
    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    // Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_extension_custom_fonts' ) ) {
        /**
         * Main ReduxFramework custom_fonts extension class
         *
         * @since       3.1.6
         */
        class ReduxFramework_extension_custom_fonts {

            /**
             * @var string
             */
            static $version = "1.0.4";
            // Protected vars
            /**
             * @var
             */
            protected $parent;

            /**
             * @var
             */
            public $extension_url;

            /**
             * @var string
             */
            public $extension_dir;

            /**
             * @var ReduxFramework_extension_custom_fonts
             */
            public static $theInstance;

            /**
             * @var array
             */
            public $custom_fonts = array();

            /**
             * @var array
             */
            private $filesystem = array();

            /**
             * Class Constructor. Defines the args for the extions class
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       array $sections   Panel sections.
             * @param       array $args       Class constructor arguments.
             * @param       array $extra_tabs Extra panel tabs.
             *
             * @return      void
             */
            public function __construct( $parent ) {
                $this->parent = $parent;

                $this->filesystem = $this->parent->filesystem->execute( 'object' );

                $this->upload_dir = ReduxFramework::$_upload_dir . 'custom-fonts/'; // $upload['basedir'] . '/redux_custom_fonts/';
                $this->upload_url = ReduxFramework::$_upload_url . 'custom-fonts/'; // $upload['baseurl'] . '/redux_custom_fonts/';

                //echo substr(sprintf('%o', fileperms($this->upload_dir )), -4);
                $this->getFonts();

                if ( file_exists( $this->upload_dir . 'fonts.css' ) ) {
                    if ( filemtime( $this->upload_dir . 'custom' ) > ( filemtime( $this->upload_dir . 'fonts.css' ) + 10 ) ) {
                        //echo "regen existing file";
                        $this->generateCSS();
                    }
                } else {
                    //echo "create non existing file";
                    $this->generateCSS();
                }

                if ( empty( $this->extension_dir ) ) {
                    $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                }

                $this->field_name = 'custom_fonts';

                self::$theInstance = $this;

                // Adds the local field
                add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array(
                    &$this,
                    'overload_field_path'
                ) );

                add_action( 'wp_ajax_redux_custom_fonts', array(
                    $this,
                    'ajax'
                ) );

                add_filter( "redux/{$this->parent->args['opt_name']}/field/typography/custom_fonts", array(
                    $this,
                    'addCustomFonts'
                ) );

                $this->dynamic_section();

                add_filter( "redux/options/{$this->parent->args['opt_name']}/section/redux_dynamic_font_control", array(
                    $this,
                    'remove_dynamic_section'
                ) );

                //require_once 'System.php'; // Wordpress core file
                //if ( class_exists('System') === true ) {
                add_filter( 'upload_mimes', array(
                    $this,
                    'custom_upload_mimes'
                ) );
                //}

                add_action( 'wp_enqueue_scripts', array( $this, '_enqueue_output' ), 150 );

                add_filter( 'tiny_mce_before_init', array( $this, 'extend_tinymce_dropdown' ) );


            }


            /**
             * Remove the dynamically added section if the field was used elsewhere
             *
             * @param $section
             *
             * @return array
             * @since  Redux_Framework 3.1.1
             */
            function remove_dynamic_section( $section ) {
                if ( isset( $this->parent->field_types[ $this->field_name ] ) ) {
                    $section = array();
                }

                return $section;
            }

            /**
             * Adds FontMeister fonts to the TinyMCE drop-down. Typekit fonts don't render properly in the drop-down and in the editor,
             * because Typekit needs JS and TinyMCE doesn't support that.
             *
             * @param $opt
             *
             * @return array
             */
            function extend_tinymce_dropdown( $opt ) {
                if ( ! is_admin() ) {
                    return $opt;
                }

                //print_r($this->custom_fonts);
                //return $opts;

                $theme_advanced_fonts = "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats";
                $mce_fonts            = array();
                $google_font_counter  = 0;
                $content_css          = array();
                $fontdeck_included    = false;

                foreach ( $this->custom_fonts as $font => $pieces ) {
                    $mce_fonts[] = $font;
                }
                $mce_fonts   = implode( ',', $mce_fonts );
                $content_css = implode( ',', $content_css );
                if ( trim( $mce_fonts ) != '' ) {
                    $theme_advanced_fonts .= $mce_fonts;
                }
                $opt['theme_advanced_fonts'] = $theme_advanced_fonts;
                if ( isset( $opt['content_css'] ) ) {
                    $opt['content_css'] .= $content_css;
                } else {
                    $opt['content_css'] = $content_css;
                }

                return $opt;

            }


            /**
             * Function to enqueue the custom fonts css
             */
            function _enqueue_output() {
                if ( file_exists( $this->upload_dir . 'fonts.css' ) ) {
                    wp_register_style(
                        'redux-custom-fonts-css',
                        $this->upload_url . 'fonts.css',
                        '',
                        filemtime( $this->upload_dir . 'fonts.css' ),
                        'all'
                    );

                    wp_enqueue_style( 'redux-custom-fonts-css' );
                }
            }

            /**
             * Adds the appropriate mime types to WordPress
             *
             * @param array $existing_mimes
             *
             * @return array
             */
            function custom_upload_mimes( $existing_mimes = array() ) {
                $existing_mimes['ttf']  = 'font/ttf';
                $existing_mimes['otf']  = 'font/otf';
                $existing_mimes['woff'] = 'application/font-woff';

                return $existing_mimes;
            }

            /**
             * Gets all the fonts in the custom_fonts directory
             */
            public function getFonts() {
                if ( ! empty( $this->custom_fonts ) ) {
                    return $this->custom_fonts;
                }

                $fonts = $this->filesystem->dirlist( $this->upload_dir, false, true );
                /*
                if (!empty($fonts)) {
                    foreach ($fonts as $font) {
                        if ($font['type'] == "d") {
                            if (!empty($font['name'])) {
                                $kinds = array();
                                foreach($font['files'] as $f) {
                                    $valid = $this->checkFontFileName($f);
                                    if ($valid) {
                                        array_push($kinds, $valid);
                                    }
                                }
                                $this->custom_fonts[$font['name']] = $kinds;
                                //array_push($this->custom_fonts, $font['name']);
                            }
                        }
                    }
                }
                */
                if ( ! empty( $fonts ) ) {
                    foreach ( $fonts as $section ) {
                        if ( $section['type'] == "d" && ! empty( $section['name'] ) ) {
                            if ( $section['name'] == "custom" ) {
                                $section['name'] = __( 'Custom Fonts', GRVE_THEME_TRANSLATE );
                            } else if ( $section['name'] == "fontsquirrel" ) {
                                $section['name'] = __( 'Fonts Squirrel', GRVE_THEME_TRANSLATE );
                            }
                            if ( ! isset( $section['files'] ) || empty( $section['files'] ) ) {
                                continue;
                            }
                            $this->custom_fonts[ $section['name'] ] = isset( $this->custom_fonts[ $section['name'] ] ) ? $this->custom_fonts[ $section['name'] ] : array();

                            $kinds = array();
                            foreach ( $section['files'] as $font ) {
                                if ( ! empty( $font['name'] ) ) {
                                    if ( ! isset( $font['files'] ) || empty( $font['files'] ) ) {
                                        continue;
                                    }
                                    $kinds = array();
                                    foreach ( $font['files'] as $f ) {
                                        $valid = $this->checkFontFileName( $f );
                                        if ( $valid ) {
                                            array_push( $kinds, $valid );
                                        }
                                    }
                                    $this->custom_fonts[ $section['name'] ][ $font['name'] ] = $kinds;
                                }

                            }
                        }
                    }
                }
            }

            /**
             * @param $custom_fonts
             *
             * @return array
             */
            public function addCustomFonts( $custom_fonts ) {
                if ( ! is_array( $custom_fonts ) || empty( $custom_fonts ) ) {
                    $custom_fonts = array();
                }
                $custom_fonts = wp_parse_args( $custom_fonts, $this->custom_fonts );

                return $custom_fonts;
            }

            /**
             * Ajax used within the panel to add and process the fonts
             */
            public function ajax() {
                if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], "redux_{$this->parent->args['opt_name']}_custom_fonts" ) ) {
                    //exit("Not a valid nonce");
                    die( 0 );
                }

                if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "delete" ) {
                    if ( $_REQUEST['section'] == __( 'Custom Fonts', GRVE_THEME_TRANSLATE ) ) {
                        $_REQUEST['section'] = "custom";
                    }
                    if ( $_REQUEST['section'] == __( 'Fonts Squirrel', GRVE_THEME_TRANSLATE ) ) {
                        $_REQUEST['section'] = "fontssquirrel";
                    }

                    try {
                        $this->filesystem->delete( $this->upload_dir . $_REQUEST['section'] . '/' . $_REQUEST['name'] . '/', true, 'd' );
                        $result = array(
                            'type' => "success"
                        );
                        echo json_encode( $result );

                    } catch ( Exception $e ) {
                        echo json_encode( array(
                            'type' => 'error',
                            'msg'  => __( 'Unable to delete font file(s).', GRVE_THEME_TRANSLATE )
                        ) );
                    }

                    die();
                }


                if ( ! isset( $_REQUEST['title'] ) ) {
                    $_REQUEST['title'] = "";
                }
                if ( isset( $_REQUEST['attachment_id'] ) && ! empty( $_REQUEST['attachment_id'] ) ) {
                    $this->processWebfont( $_REQUEST['attachment_id'], $_REQUEST['title'], $_REQUEST['mime'] );

                    $result = array(
                        'type' => "success"
                    );

                    echo json_encode( $result );
                }
                die();
            }

            /**
             * Get only valid files. Ensure everything is proper for processing.
             *
             * @param $path
             *
             * @return array
             */
            function getValidFiles( $path ) {
                $output = array();
                $path   = trailingslashit( $path );

                $files = $this->filesystem->dirlist( $path, false, true );

                foreach ( $files as $file ) {
                    if ( $file['type'] == "d" ) {
                        $output = array_merge( $output, $this->getValidFiles( $path . $file['name'] ) );
                    } elseif ( $file['type'] == "f" ) {
                        $valid = $this->checkFontFileName( $file );
                        if ( $valid ) {
                            $output[ $valid ] = trailingslashit( $path ) . $file['name'];
                        }
                    }
                }

                return $output;
            }

            /**
             * Take a valid web font and process the missing pieces.
             *
             * @param        $attachment_id
             * @param        $name
             * @param        $mime_type
             * @param string $subfolder
             */
            function processWebfont( $attachment_id, $name, $mime_type, $subfolder = 'custom/' ) {
                $missing = array();

                $complete = array(
                    'ttf',
                    'woff',
                    'eot',
                    'svg'
                );

                $subtype = explode( '/', $mime_type );
                $subtype = trim( max( $subtype ) );

                if ( ! is_dir( $this->upload_dir ) ) {
                    $this->parent->filesystem->execute( 'mkdir', $this->upload_dir );
                }
                if ( ! is_dir( $this->upload_dir . $subfolder ) ) {
                    $this->parent->filesystem->execute( 'mkdir', $this->upload_dir . $subfolder );
                }
                $temp = $this->upload_dir . 'temp';

                $path = get_attached_file( $attachment_id, false );

                if ( empty( $path ) ) {
                    echo json_encode( array(
                        'type' => 'error',
                        'msg'  => __( 'Attachment does not exist.', GRVE_THEME_TRANSLATE )
                    ) );
                    die();
                }
                $filename = explode( '/', $path );

                $filename = $filename[ ( count( $filename ) - 1 ) ];

                $fontname = ucfirst( str_replace( array(
                    '.zip',
                    '.ttf',
                    '.woff',
                    '.eot',
                    '.svg',
                    '.otf'
                ), '', strtolower( $filename ) ) );

                if ( ! isset( $name ) || empty( $name ) ) {
                    $name = $fontname;
                }
                if ( $subtype == "zip" ) {
                    if ( ! is_dir( $temp ) ) {
                        //$this->filesystem->mkdir($temp, FS_CHMOD_DIR);
                        $this->parent->filesystem->execute( 'mkdir', $temp );
                    }
                    $unzipfile = unzip_file( $path, $temp );
                    $output    = $this->getValidFiles( $temp );

                    if ( ! empty( $output ) ) {
                        foreach ( $complete as $test ) {
                            if ( ! isset( $output[ $test ] ) ) {
                                $missing[] = $test;
                            }
                        }

                        if ( ! is_dir( $this->upload_dir . $subfolder . $name . '/' ) ) {
                            //$this->filesystem->mkdir($this->upload_dir . $subfolder . $name . '/', FS_CHMOD_DIR);
                            $this->parent->filesystem->execute( 'mkdir', $this->upload_dir . $subfolder . $name . '/' );
                        }
                        foreach ( $output as $key => $value ) {
                            $param_array = array(
                                'destination' => $this->upload_dir . $subfolder . $name . '/' . $fontname . '.' . $key,
                                'overwrite'   => true,
                                'chmod'       => 755
                            );

                            $this->parent->filesystem->execute( 'copy', $value, $param_array );

                            //$this->filesystem->copy($value, , FS_CHMOD_DIR);
                        }
                        //$this->getMissingFiles( $name, $fontname, $missing, $output, $subfolder );
                    }
                    $this->filesystem->delete( $temp, true, 'd' );
                    $this->generateCSS();
                    wp_delete_attachment( $attachment_id, true );
                } else if ( $subtype == "ttf" || $subtype == "otf" || $subtype == "font-woff" ) {
                    foreach ( $complete as $test ) {
                        if ( ! isset( $output[ $test ] ) ) {
                            $missing[] = $test;
                        }
                    }

                    if ( ! is_dir( $this->upload_dir . $subfolder . $name . '/' ) ) {
                        //$this->filesystem->mkdir($this->upload_dir . $subfolder . $name . '/', FS_CHMOD_DIR);
                        $this->parent->filesystem->execute( 'mkdir', $this->upload_dir . $subfolder . $name . '/' );
                    }
                    //$this->filesystem->copy($path, $this->upload_dir . $subfolder . '/' . $name . '/' . $fontname . '.' . $subtype, FS_CHMOD_DIR);

                    $param_array = array(
                        'destination' => $this->upload_dir . $subfolder . '/' . $name . '/' . $fontname . '.' . $subtype,
                        'overwrite'   => true,
                        'chmod'       => 755
                    );

                    $this->parent->filesystem->execute( 'copy', $path, $param_array );

                    $output = array(
                        $subtype => $path
                    );
                    $this->getMissingFiles( $name, $fontname, $missing, $output, $subfolder );
                    $this->generateCSS();
                    wp_delete_attachment( $attachment_id, true );
                } else {
                    echo json_encode( array(
                        'type' => 'error',
                        'msg'  => __( 'File type not recognized.', GRVE_THEME_TRANSLATE )
                    ) );
                    die();
                }
            }

            /**
             * Ping the mashape (fontsquirrel) API to get the missing files.
             *
             * @param $name
             * @param $fontname
             * @param $missing
             * @param $output
             * @param $subfolder
             */
            private function getMissingFiles( $name, $fontname, $missing, $output, $subfolder ) {
                if ( ! isset( $name ) || empty( $name ) || ! isset( $missing ) || empty( $missing ) || ! is_array( $missing ) ) {
                    return;
                }
                $temp = $this->upload_dir . 'temp';

                if ( count( $output ) == 1 && isset( $output['eot'] ) ) {
                    echo json_encode( array(
                        'type' => 'error',
                        'msg'  => __( 'The font format .eot is not supported.', GRVE_THEME_TRANSLATE )
                    ) );
                    $this->filesystem->delete( $this->upload_dir . $subfolder . $name . '/', true, 'd' );
                    $this->filesystem->delete( $temp, true, 'd' );
                    die();
                }

                // Find a file to convert from
                foreach ( $output as $key => $value ) {
                    if ( $key == "eot" ) {
                        continue;
                    } else {
                        $main = $key;
                        break;
                    }
                }
                if ( ! isset( $main ) ) {
                    echo json_encode( array(
                        'type' => 'error',
                        'msg'  => __( 'No valid font file was found.', GRVE_THEME_TRANSLATE )
                    ) );
                    $this->filesystem->delete( $temp, true, 'd' );
                    $this->filesystem->delete( $this->upload_dir . $subfolder . $name . '/', true, 'd' );
                    die();
                }

                foreach ( $missing as $item ) {

                    $local_file = $this->upload_dir . $subfolder . $name . '/' . $fontname . '.' . $main; //path to a local file on your server

                    $post_fields = array(
                        'format' => $item
                    );

                    $boundary = wp_generate_password(24); // Just a random string, use something better than wp_generate_password() though.
                     $headers = array(
                         'content-type' => 'multipart/form-data; boundary=' . $boundary
                     );

                    $payload = '';

                    // First, add the standard POST fields:
                    foreach ( $post_fields as $tName => $value ) {
                        $payload .= '--' . $boundary;
                        $payload .= "\r\n";
                        $payload .= 'Content-Disposition: form-data; name="' . $tName .
                                    '"' . "\r\n\r\n";
                        $payload .= $value;
                        $payload .= "\r\n";
                    }
                    // Upload the file
                    if ( $local_file ) {
                        $payload .= '--' . $boundary;
                        $payload .= "\r\n";
                        $payload .= 'Content-Disposition: form-data; name="convert"; filename="' . basename( $local_file ) . '"' . "\r\n";
                            $payload .= "\r\n";
                            $payload .= file_get_contents( $local_file );
                            $payload .= "\r\n";
                    }

                    $payload .= '--' . $boundary . '--';

                    $response = wp_remote_post( 'http://fonts.redux.io', array(
                        'headers' => $headers,
                        'body' => $payload,
                        'timeout' => 120,
                    ) );

                    if ( ! empty( $response['body'] ) ) {

                        $param_array = array(
                            'content' => $response['body'],
                            'chmod'   => FS_CHMOD_FILE
                        );

                        $this->parent->filesystem->execute( 'put_contents', $this->upload_dir . $subfolder . $name . '/' . $fontname . '.' . $item, $param_array );
                    } else {
                        echo json_encode( array(
                            'type' => 'error',
                            'msg'  => __( 'You font could not be converted at this time. Please try again later.', GRVE_THEME_TRANSLATE )
                        ) );
                        $this->filesystem->delete( $this->upload_dir . $subfolder . $name . '/', true, 'd' );
                        $this->filesystem->delete( $temp, true, 'd' );
                        die();
                    }

                }

            }

            /**
             * Check if the file name is a valid font file.
             *
             * @param $file
             *
             * @return bool|string
             */
            private function checkFontFileName( $file ) {

                if ( strtolower( substr( $file['name'], - 5 ) ) == ".woff" ) {
                    return "woff";
                }
                $sub = strtolower( substr( $file['name'], - 4 ) );

                if ( $sub == ".ttf" ) {
                    return "ttf";
                }
                if ( $sub == ".eot" ) {
                    return "eot";
                }
                if ( $sub == ".svg" ) {
                    return "svg";
                }
                if ( $sub == ".otf" ) {
                    return "otf";
                }

                return false;
            }

            /**
             * Generate a new custom CSS file for enqueing on the frontend and backend.
             */
            private function generateCSS() {
                $fonts = $this->filesystem->dirlist( $this->upload_dir . 'custom/', false, true );

                if ( empty( $fonts ) ) {
                    return;
                }
                $css = "";

                foreach ( $fonts as $font ) {
                    if ( $font['type'] == "d" ) {
                        $css .= $this->generateFontCSS( $font['name'], $this->upload_dir . 'custom/' );
                    }
                }
                $param_array = array(
                    'content' => $css,
                    'chmod'   => FS_CHMOD_FILE
                );

                $this->parent->filesystem->execute( 'put_contents', $this->upload_dir . 'fonts.css', $param_array ); // put_contents($this->upload_dir . 'fonts.css', $css, FS_CHMOD_FILE);
            }

            /**
             * Process to actually construct the custom font css file.
             *
             * @param $name
             * @param $dir
             *
             * @return string
             */
            private function generateFontCSS( $name, $dir ) {
                $path = $dir . $name;

                $files = $this->filesystem->dirlist( $path, false, true );

                if ( empty( $files ) ) {
                    return;
                }
                $output = array();

                foreach ( $files as $file ) {
                    $output[ $this->checkFontFileName( $file ) ] = $file['name'];
                }

                $css = "@font-face {";

                $css .= "font-family:'{$name}';";

                $src = array();

                if ( isset( $output['eot'] ) ) {
                    $src[] = "url('{$this->upload_url}custom/{$name}/{$output['eot']}?#iefix') format('embedded-opentype')";
                }
                if ( isset( $output['woff'] ) ) {
                    $src[] = "url('{$this->upload_url}custom/{$name}/{$output['woff']}') format('woff')";
                }
                if ( isset( $output['ttf'] ) ) {
                    $src[] = "url('{$this->upload_url}custom/{$name}/{$output['ttf']}') format('truetype')";
                }
                if ( isset( $output['svg'] ) ) {
                    $src[] = "url('{$this->upload_url}custom/{$name}/{$output['svg']}#svg{$name}') format('svg')";
                }
                if ( ! empty( $src ) ) {
                    $css .= "src:" . implode( ", ", $src ) . ";";
                }
                // Replace font weight and style with sub-sets
                $css .= "font-weight: normal;";

                $css .= "font-style: normal;";

                $css .= "}";

                return $css;
            }

            /**
             * @return ReduxFramework_extension_custom_fonts
             */
            public static function get_instance() {
                return self::$theInstance;
            }

            /**
             * Forces the use of the embeded field path vs what the core typically would use
             *
             * @param $field
             *
             * @return string
             */
            public function overload_field_path( $field ) {
                return dirname( __FILE__ ) . '/' . $this->field_name . '/field_' . $this->field_name . '.php';
            }

            /**
             * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
             * Simply include this function in the child themes functions.php file.
             * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
             * so you must use get_template_directory_uri() if you want to use any of the built in icons
             */
            function dynamic_section() {

                if ( ! isset( $this->parent->fontControl ) ) {
                    $this->parent->sections[] = array(
                        'title'  => __( 'Font Control', GRVE_THEME_TRANSLATE ),
                        'desc'   => __( '<p class="description"></p>', GRVE_THEME_TRANSLATE ),
                        'icon'   => 'el-icon-font',
                        'id'     => 'redux_dynamic_font_control',
                        // Leave this as a blank section, no options just some intro text set above.
                        'fields' => array()
                    );

                    for ( $i = count( $this->parent->sections ); $i >= 1; $i -- ) {
                        if ( isset( $this->parent->sections[ $i ] ) && isset( $this->parent->sections[ $i ]['title'] ) && $this->parent->sections[ $i ]['title'] == __( 'Font Control', GRVE_THEME_TRANSLATE ) ) {
                            $this->parent->fontControl = $i;
                            break;
                        }
                    }
                    $this->parent->sections[ $this->parent->fontControl ]['fields'][] = array(
                        'id'   => 'redux_font_control',
                        'type' => 'custom_fonts'
                    );
                }
            }
        } // class
    } // if