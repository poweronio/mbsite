<?php
/*
Plugin Name: GD Booster
Plugin URI: http://wpgeodirectory.com/
Description: GD Booster wraps some of the smartest caching, compression and minifying methods available today for WordPress, modded to be 100% GeoDirectory compatible.
Version: 1.0.9
Author: GeoDirectory
Author URI: http://wpgeodirectory.com/
License: GPLv3
 
GD Booster is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
GD Booster is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with GD Booster. If not, see license.txt.
*/

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));
	
define( 'GEODIR_GD_BOOSTER_VERSION', '1.0.9' );
if ( !defined( 'GEODIR_GD_BOOSTER_TEXTDOMAIN' ) ) {
	define( 'GEODIR_GD_BOOSTER_TEXTDOMAIN', 'geodir-gd-booster' );
}
if ( !defined( 'GD_BOOSTER_CACHE_DIR' ) ) {
	define( 'GD_BOOSTER_CACHE_DIR', str_replace('\\','/',dirname(__FILE__)).'/../../booster_cache' );
}

if(require(dirname(__FILE__).'/includes/wp-php53.php')) { // TRUE if running PHP v5.3+.
	require_once dirname(__FILE__).'/geodir-gd-booster.inc.php';
	
	if ( defined( 'GEODIR_GD_BOOSTER_ENABLE' ) && GEODIR_GD_BOOSTER_ENABLE ) {
		/* gd-booster */
		require_once dirname(__FILE__).'/booster_inc.php';
		
		add_action('wp_footer','gd_booster_wp', 100001);
	}
}
else wp_php53_notice('GD Booster');

if ( is_admin() ){
require_once('gd_update.php');	
}

function gd_booster_htaccess() {
	$wp_htacessfile = get_home_path().'.htaccess';
	$booster_htacessfile = rtrim(str_replace('\\','/',realpath(dirname(__FILE__))),'/').'/htaccess/.htaccess';
	if(file_exists($booster_htacessfile))
	{
		if(file_exists($wp_htacessfile) && is_writable($wp_htacessfile))
		{
			$wp_htacessfile_contents = file_get_contents($wp_htacessfile);
			$wp_htacessfile_contents = preg_replace('/#GEODIR-GD-Booster Start#################################################.*#GEODIR-GD-Booster End#################################################/ims','',$wp_htacessfile_contents);
			$wp_htacessfile_contents = $wp_htacessfile_contents.file_get_contents($booster_htacessfile);
		}
		else $wp_htacessfile_contents = file_get_contents($booster_htacessfile);
		@file_put_contents($wp_htacessfile,$wp_htacessfile_contents);
	}
	@mkdir(GD_BOOSTER_CACHE_DIR,0777);
	@chmod(GD_BOOSTER_CACHE_DIR,0777);
}

function gd_booster_cleanup() {
	// Remove entries from .htaccess
	$wp_htacessfile = get_home_path().'.htaccess';
	if(file_exists($wp_htacessfile) && is_writable($wp_htacessfile))
	{
		$wp_htacessfile_contents = file_get_contents($wp_htacessfile);
		$wp_htacessfile_contents = preg_replace('/#GEODIR-GD-Booster Start#################################################.*#GEODIR-GD-Booster End#################################################/ims','',$wp_htacessfile_contents);
		@file_put_contents($wp_htacessfile,$wp_htacessfile_contents);
	}
	
	// Remove all cache files
	$handle=opendir(GD_BOOSTER_CACHE_DIR);
	while(false !== ($file = readdir($handle)))
	{
		if($file[0] != '.' && is_file(GD_BOOSTER_CACHE_DIR.'/'.$file)) unlink(GD_BOOSTER_CACHE_DIR.'/'.$file);
	}
	closedir($handle);
}

function gd_booster_wp() {
	// Dump output buffer
	if($out = ob_get_contents())
	{
		// Check for right PHP version
		if(strnatcmp(phpversion(),'5.0.0') >= 0)
		{ 
			$booster_cache_dir = GD_BOOSTER_CACHE_DIR;
			$js_plain = '';
			$booster_out = '';
			$booster_folder = explode('/',rtrim(str_replace('\\','/',realpath(dirname(__FILE__))),'/'));
			$booster_folder = $booster_folder[count($booster_folder) - 1];
			$booster = new GDBooster();
			if(!is_dir($booster_cache_dir)) 
			{
				@mkdir($booster_cache_dir,0777);
				@chmod($booster_cache_dir,0777);
			}
			if(is_dir($booster_cache_dir) && is_writable($booster_cache_dir) && substr(decoct(fileperms($booster_cache_dir)),1) == "0777")
			{
				$booster_cache_reldir = $booster->getpath(str_replace('\\','/',realpath($booster_cache_dir)),str_replace('\\','/',dirname(__FILE__)));
			}
			else 
			{
				$booster_cache_dir = rtrim(str_replace('\\','/',dirname(__FILE__)),'/').'/../../booster_cache';
				$booster_cache_reldir = '../../booster_cache';
			}
			$booster->booster_cachedir = $booster_cache_reldir;
			$booster->js_minify = TRUE;
			$booster->js_closure_compiler = FALSE;
			
			// Get Domainname
			$host = isset($_SERVER['SCRIPT_URI']) ? parse_url($_SERVER['SCRIPT_URI'], PHP_URL_HOST) : $_SERVER['HTTP_HOST'];
			// Convert siteurl into a regex-safe expression
			$host = str_replace(array('/', '.'), array('\/', '\.'), $host);
			
			$http_host = isset($_SERVER['SCRIPT_URI']) ? parse_url($_SERVER['SCRIPT_URI'], PHP_URL_HOST) : $_SERVER['HTTP_HOST'];
			
			// exclude js/css
			$exclude_js_css = $booster->geodir_exclude_js_css();
			$exclude_js = !empty($exclude_js_css) && isset($exclude_js_css['js']) ? $exclude_js_css['js'] : array();
			$exclude_css = !empty($exclude_js_css) && isset($exclude_js_css['css']) ? $exclude_js_css['css'] : array();
	
			// Calculate relative path from root to Booster directory
			$root_to_booster_path = $booster->getpath(str_replace('\\','/',dirname(__FILE__)),str_replace('\\','/',dirname(realpath(ABSPATH))));
			
			if(preg_match_all('/<head.*<\/head>/ims',$out,$headtreffer,PREG_PATTERN_ORDER) > 0)
			{
				$pagetreffer = $out;
				// Prevent processing of (conditional) comments
				$pagetreffer = preg_replace('/<!--.+?-->/ims','',$pagetreffer);
				
				// Detect charset
				if(preg_match('/<meta http-equiv="Content-Type" content="text\/html; charset=(.+?)" \/>/',$pagetreffer,$charset))
				{
					$pagetreffer = str_replace($charset[1],'',$pagetreffer);
					$charset = $charset[1];
				}
				else $charset = '';
				
				// CSS part
				$css_rel_files = array();
				$css_abs_files = array();
				$css_external_files = array();
				

				// Continue with external files
				preg_match_all('/<link[^>]*?href=[\'"]*?([^\'"]+?\.css)[\'"]*?[^>]*?>/ims',$pagetreffer,$treffer,PREG_PATTERN_ORDER);
				for($i=0;$i < count($treffer[0]);$i++) 
				{
					// Get media-type
					if(preg_match('/media=[\'"]*([^\'"]+)[\'"]*/ims',$treffer[0][$i],$mediatreffer)) 
					{
						$media = preg_replace('/[^a-z]+/i','',$mediatreffer[1]);
						if(trim($media) == '') $media = 'all';
					}
					else $media = 'all';
	
					// Get relation
					if(preg_match('/rel=[\'"]*([^\'"]+)[\'"]*/ims',$treffer[0][$i],$reltreffer)) $rel = $reltreffer[1];
					else $rel = 'stylesheet';
	
					// Convert file's URI into an absolute local path
					if (strpos($treffer[1][$i],'https:') !== false) {
						$filename = preg_replace('/^https:\/\/[^\/]+/',rtrim($_SERVER['DOCUMENT_ROOT'],'/'),$treffer[1][$i]);
					}else{
						$http_host = isset($_SERVER['SCRIPT_URI']) ? parse_url($_SERVER['SCRIPT_URI'],PHP_URL_HOST) : $_SERVER['HTTP_HOST'];
						// http or https
						$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
						if ( strpos( $treffer[1][$i], "//" . $http_host ) === 0 ) {
							$treffer[1][$i] = preg_replace( '/^\/\/'.$http_host.'[^\/]*/', $protocol . $http_host, $treffer[1][$i] );
						}
						
						if ( strpos( $treffer[1][$i], $http_host ) === 0 ) {
							$treffer[1][$i] = preg_replace( '/^'.$http_host.'[^\/]*/', $protocol . $http_host, $treffer[1][$i] );
						}
						$filename = preg_replace('/^http:\/\/[^\/]+/',rtrim($_SERVER['DOCUMENT_ROOT'],'/'),$treffer[1][$i]);
					}
					//$filename = preg_replace('/^http:\/\/[^\/]+/',rtrim($_SERVER['DOCUMENT_ROOT'],'/'),$treffer[1][$i]);
					// Remove any parameters from file's URI
					$filename = preg_replace('/\?.*$/','',$filename);
					// If file exists
					//$booster_out .= "###".$filename;
					// If file is external
					if( substr($filename,0,7) == 'http://' || substr($filename,0,8) == 'https://' || substr($filename,0,2) == '//' ) {
						// exclude js files
						if (basename($filename) != '' && gd_booster_exclude_file($treffer[1][$i], $exclude_css)) {
							$css_exclude_files[] = $treffer[0][$i];
								
							if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
								$out = str_replace($treffer[0][$i], '<!-- Excluded by GD Booster '.$treffer[0][$i].' -->', $out);
							} else {
								$out = str_replace(array($treffer[0][$i]."\r\n", $treffer[0][$i]."\r", $treffer[0][$i]."\n", $treffer[0][$i]),'',$out);
							}
						} else {
							// Skip processing of external files altogether
							$css_external_files[] = $treffer[0][$i];
							$debug_text = '';
							if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
								$debug_text = '<!-- Processed by GD Booster external file '.$treffer[0][$i].' -->';
							}
							$out = str_replace( $treffer[0][$i], $debug_text, $out );
						}
					} else if(file_exists($filename)) {
						// If its a normal CSS-file
						if(substr($filename,strlen($filename) - 4,4) == '.css' && file_exists($filename))
						{
							// exclude css files
							if (basename($filename) != '' && gd_booster_exclude_file($treffer[1][$i], $exclude_css)) {
								$css_exclude_files[] = $treffer[0][$i];
									
								if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
									$out = str_replace($treffer[0][$i], '<!-- Excluded by GD Booster '.$treffer[0][$i].' -->', $out);
								} else {
									$out = str_replace(array($treffer[0][$i]."\r\n", $treffer[0][$i]."\r", $treffer[0][$i]."\n", $treffer[0][$i]),'',$out);
								}
							} else {
								// Put file-reference inside a comment
								if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
									$out = str_replace($treffer[0][$i],'<!-- Processed by GD Booster '.$treffer[0][$i].' -->',$out);
								} else {
									$out = str_replace(array($treffer[0][$i]."\r\n", $treffer[0][$i]."\r", $treffer[0][$i]."\n", $treffer[0][$i]),'',$out);
								}
			
								// Calculate relative path from Booster to file
								$booster_to_file_path = $booster->getpath(str_replace('\\','/',dirname($filename)),str_replace('\\','/',dirname(__FILE__)));
								$filename = $booster_to_file_path.'/'.basename($filename);
				
								// Create sub-arrays if not yet there
								if(!isset($css_rel_files[$media])) $css_rel_files[$media] = array();
								if(!isset($css_abs_files[$media])) $css_abs_files[$media] = array();
								if(!isset($css_rel_files[$media][$rel])) $css_rel_files[$media][$rel] = array();
								if(!isset($css_abs_files[$media][$rel])) $css_abs_files[$media][$rel] = array();
								
								// Enqueue file to respective array
								array_push($css_rel_files[$media][$rel],$filename);
								array_push($css_abs_files[$media][$rel],rtrim(str_replace('\\','/',dirname(realpath(ABSPATH))),'/').'/'.$root_to_booster_path.'/'.$filename);
							}
						}
						else $out = str_replace($treffer[0][$i],$treffer[0][$i].'<!-- GD Booster skipped '.$filename.' -->',$out);
					}
					// Leave untouched but put calculated local file name into a comment for debugging
					else $out = str_replace($treffer[0][$i],$treffer[0][$i].'<!-- GD Booster had a problems finding '.$filename.' -->',$out);
				}
				
				// Start width inline-files
				preg_match_all('/<style[^>]*>(.*?)<\/style>/ims',$pagetreffer,$treffer,PREG_PATTERN_ORDER);
				for($i=0;$i<count($treffer[0]);$i++) 
				{
					// Get media-type
					if(preg_match('/media=[\'"]*([^\'"]+)[\'"]*/ims',$treffer[0][$i],$mediatreffer)) 
					{
						$media = preg_replace('/[^a-z]+/i','',$mediatreffer[1]);
						if(trim($media) == '') $media = 'all';
					}
					else $media = 'all';
					$rel = 'stylesheet';
					
					// Create sub-arrays if not yet there
					if(!isset($css_rel_files[$media])) $css_rel_files[$media] = array();
					if(!isset($css_abs_files[$media])) $css_abs_files[$media] = array();
					if(!isset($css_rel_files[$media][$rel])) $css_rel_files[$media][$rel] = array();
					if(!isset($css_abs_files[$media][$rel])) $css_abs_files[$media][$rel] = array();

					// Save plain CSS to file to keep everything in line
					$css_plain_filename = md5($treffer[1][$i]).'_plain.css';
					
					$filename = $booster_cache_dir.'/'.$css_plain_filename;
					if ( !file_exists( $filename ) ) {
						@file_put_contents( $filename, $treffer[1][$i] );
					}
					
					@chmod($filename,0777);
		
					// Enqueue file to array
					$booster_to_file_path = $booster->getpath( str_replace( '\\','/', dirname( $filename ) ),str_replace( '\\', '/', dirname( __FILE__ ) ) );
					
					// Calculate relative path from Booster to file
					$booster_to_file_path = $booster->getpath(str_replace('\\','/',dirname($filename)),str_replace('\\','/',dirname(__FILE__)));
					$filename = $booster_to_file_path.'/'.$css_plain_filename;
					
					array_push($css_rel_files[$media][$rel],$filename);
					array_push($css_abs_files[$media][$rel],rtrim(str_replace('\\','/',dirname(realpath(ABSPATH))),'/').'/'.$root_to_booster_path.'/'.$filename);

					$debug_text = '';
					if ( GEODIR_GD_BOOSTER_DEBUGGING_ENABLE ) {
						$debug_text = '<!-- Moved to file by GD Booster '.$css_plain_filename.' -->';
					}
					$pagetreffer = str_replace( $treffer[0][$i], $debug_text, $pagetreffer );
					$out = str_replace( $treffer[0][$i], $debug_text, $out );					
				}
	
				// Creating Booster markup for each media and relation seperately
				$links = '';
				reset($css_rel_files);
				for($i=0;$i < count($css_rel_files);$i++) 
				{
					$media_rel = $css_rel_files[key($css_rel_files)];
					$media_abs = $css_abs_files[key($css_rel_files)];
					reset($media_rel);
					for($j=0;$j<count($media_rel);$j++) 
					{
						$booster->getfilestime($media_rel[key($media_rel)],'css');

						$media_rel[key($media_rel)] = implode(',',$media_rel[key($media_rel)]);
						$media_abs[key($media_rel)] = implode(',',$media_abs[key($media_rel)]);
						$link = '<link type="text/css" rel="'.key($media_rel).
						'" media="'.key($css_rel_files).
						'" href="'.get_option('siteurl').'/wp-content/plugins/'.
						$booster_folder.
						'/booster_css.php'.
						'?'.//($booster->mod_rewrite ? '/' : '?').
						'dir='.htmlentities(str_replace('..','%3E',$media_rel[key($media_rel)])).
						'&amp;cachedir='.htmlentities(str_replace('..','%3E',$booster_cache_reldir),ENT_QUOTES).
						($booster->debug ? '&amp;debug=1' : '').
						($booster->librarydebug ? '&amp;librarydebug=1' : '').
						'&amp;nocache='.$booster->filestime.'" />';
						
						if(key($css_rel_files) != 'print')
						{
							$links .= $link."\r\n";
						}
						else
						{
							$links .= '<noscript>'.$link.'</noscript>'."\r\n";
							$js_plain .= 'jQuery(document).ready(function () {
								jQuery("head").append("'.addslashes($link).'");
							});
							';
						}
						$links .= "\r\n";
						next($media_rel);
					}
					next($css_rel_files);
				}

				// Insert markup for normal browsers and IEs (CC's now replacing former UA-sniffing)
				if($charset != '') $booster_out .= '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'."\r\n";
				$booster_out .= '<!--[if IE]><![endif]-->'."\r\n";
				$booster_out .= '<!--[if (gte IE 8)|!(IE)]><!-->'."\r\n";
				if (!empty($css_external_files)) {
					$booster_out .= "\r\n" . implode("\r\n", $css_external_files) . "\r\n";
				}
				$booster_out .= $links;
				$booster_out .= '<!--<![endif]-->'."\r\n";
				$booster_out .= '<!--[if lte IE 7 ]>'."\r\n";
				$booster_out .= str_replace('booster_css.php','booster_css_ie.php',$links);
				$booster_out .= '<![endif]-->'."\r\n";
				if (!empty($css_exclude_files)) {
					$booster_out .= implode("\r\n", $css_exclude_files);
				}
				
				// Injecting the result
				$out = str_replace('</title>',"</title>\r\n".$booster_out,$out);
				$booster_out = '';				
				
				// JS-part
				$js_rel_files = array();
				$js_abs_files = array();
				$js_parameters = array();
				$js_exclude_files = array();
				$js_external_files = array();
				
				preg_match_all('/<script[^>]*>(.*?)<\/script>/ims', $pagetreffer, $treffer, PREG_PATTERN_ORDER);
				
				for ($i = 0; $i < count($treffer[0]); $i++ ) {
					$element = $treffer[0][$i];
					$inline_script = $treffer[1][$i];

					$should_continue = apply_filters('geodir_booster_script_continue', false, $element);
					
					if ($should_continue) {
                        continue;
                    }
					if ( strpos($element, 'application/ld+json') !== false ) { // Skip for application/ld+json script
						continue;
					}
					
					// Handle inline script
					if (trim($inline_script) != '') {
						// Save plain JS to file to keep everything in line
						$js_plain_filename = md5($inline_script) . '_plain.js';
						
						$filename = $booster_cache_dir . '/' . $js_plain_filename;
						
						if ( !file_exists( $filename ) ) {
							@file_put_contents( $filename, trim($inline_script) );
						}						
						@chmod( $filename, 0777 );
						
						// Enqueue file to array
						$booster_to_file_path = $booster->getpath( str_replace( '\\','/', dirname( $filename ) ),str_replace( '\\', '/', dirname( __FILE__ ) ) );
						$booster_filename = $booster_to_file_path . '/' . $js_plain_filename;
						
						array_push( $js_rel_files, $booster_filename );
						array_push( $js_abs_files, rtrim( str_replace( '\\', '/', dirname( realpath( ABSPATH ) ) ), '/') . '/' . $root_to_booster_path . '/' . $booster_filename );
						$debug_text = '';
						if ( GEODIR_GD_BOOSTER_DEBUGGING_ENABLE ) {
							$debug_text = '<!-- Moved to file by GD Booster ' . $js_plain_filename . ' -->';
						}
						
						$out = str_replace( $element, $debug_text, $out );												
					} else { // Handle script files
						if ( preg_match( '/<script.*?src=[\'"]*([^\'"]+\.js)\??([^\'"]*)[\'"]*.*?<\/script>/ims', $element, $src_matches ) ) { // .js file
							$filename = $src_matches[1];
							
							// Convert file's URI into an absolute local path
							if ( strpos( $filename, 'https:' ) !== false ) {
								$filename = preg_replace( '/^https:\/\/'.$host.'[^\/]*/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'), $filename );
							} else {
								// http or https
								$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
								if ( strpos( $filename, "//" . $http_host ) === 0 ) {
									$filename = preg_replace( '/^\/\/'.$http_host.'[^\/]*/', $protocol . $http_host, $filename );
								}
								
								if ( strpos( $filename, $http_host ) === 0 ) {
									$srctreffer[1] = preg_replace( '/^'.$http_host.'[^\/]*/', $protocol . $http_host, $filename );
								}
								$filename = preg_replace( '/^http:\/\/'.$host.'[^\/]*/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'), $filename );
							}
							
							// exclude js files
							if (basename($filename) != '' && gd_booster_exclude_file($filename, $exclude_js)) {
								$js_exclude_files[] = $element;
								
								if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
									$out = str_replace($element, '<!-- Excluded by GD Booster ' . $element . ' -->', $out);
								} else {
									$out = str_replace(array($element . "\r\n", $element . "\r", $element . "\n", $element), '', $out);
								}
							} else {
								if ( is_file($filename) && file_exists($filename) ) {
									// Remove any parameters from file's URI
									$filename = preg_replace('/\?.*$/', '', $filename);
													
									// Calculate relative path from Booster to file
									$booster_to_file_path = $booster->getpath( str_replace( '\\','/', dirname( $filename ) ),str_replace( '\\', '/', dirname( __FILE__ ) ) );
									$booster_filename = $booster_to_file_path . '/' . basename($filename);
						
									array_push( $js_rel_files, $booster_filename );
									array_push( $js_abs_files, rtrim( str_replace( '\\', '/', dirname( realpath( ABSPATH ) ) ), '/') . '/' . $root_to_booster_path . '/' . $booster_filename );
									
									// Put file-reference inside a comment
									if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
										$out = str_replace($element, '<!-- Processed by GD Booster ' . $element . ' -->', $out);
									} else {
										$out = str_replace(array($element . "\r\n", $element . "\r", $element . "\n", $element), '', $out);
									}
								} else { // External file
									// Skip processing of external files altogether
									$js_external_files[] = $element;
									
									$debug_text = '';
									if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
										$debug_text = '<!-- Processed by GD Booster external file ' . $element . ' -->';
									}
									$out = str_replace( $element, $debug_text, $out );
								}
							}
						} else { // Not .js file
							if ( preg_match( '/<script.*?src=[\'"]*([^\'"]+\.*)\??([^\'"]*)[\'"]*.*?<\/script>/ims', $element, $src_custom_matches ) ) {
								$src_filename = $src_custom_matches[1];
								$filename = $src_filename;
								// Remove any parameters from file's URI
								$filename = preg_replace('/\?.*$/', '', $filename);
							
								// Convert file's URI into an absolute local path
								if ( strpos( $filename, 'https:' ) !== false ) {
									$filename = preg_replace( '/^https:\/\/'.$host.'[^\/]*/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'), $filename );
								} else {
									// http or https
									$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
									if ( strpos( $filename, "//" . $http_host ) === 0 ) {
										$filename = preg_replace( '/^\/\/'.$http_host.'[^\/]*/', $protocol . $http_host, $filename );
									}
									
									if ( strpos( $filename, $http_host ) === 0 ) {
										$srctreffer[1] = preg_replace( '/^'.$http_host.'[^\/]*/', $protocol . $http_host, $filename );
									}
									$filename = preg_replace( '/^http:\/\/'.$host.'[^\/]*/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'), $filename );
								}
								
								if ( is_file($filename) && file_exists($filename) ) {
									// Save plain JS to file to keep everything in line
									$js_plain_filename = md5($src_filename) . '_plain_custom.js';
									
									$filename = $booster_cache_dir . '/' . $js_plain_filename;
									
									if ( !file_exists( $filename ) ) {
										@file_put_contents( $filename, trim($src_filename) );
									}									
									@chmod( $filename, 0777 );
									
									// Enqueue file to array
									$booster_to_file_path = $booster->getpath( str_replace( '\\','/', dirname( $filename ) ),str_replace( '\\', '/', dirname( __FILE__ ) ) );
									$booster_filename = $booster_to_file_path . '/' . $js_plain_filename;
									
									array_push( $js_rel_files, $booster_filename );
									array_push( $js_abs_files, rtrim( str_replace( '\\', '/', dirname( realpath( ABSPATH ) ) ), '/') . '/' . $root_to_booster_path . '/' . $booster_filename );
									
									$debug_text = '';
									if ( GEODIR_GD_BOOSTER_DEBUGGING_ENABLE ) {
										$debug_text = '<!-- Moved to file by GD Booster ' . $js_plain_filename . ' -->';
									}

									$out = str_replace( $element, $debug_text, $out );
								} else { // External file
									// Skip processing of external files altogether
									$js_external_files[] = $element;
									
									$debug_text = '';
									if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
										$debug_text = '<!-- Processed by GD Booster external file ' . $element . ' -->';
									}
									$out = str_replace( $element, $debug_text, $out );
								}
							} else { // Skipped file
								$debug_text = '';
								
								if (GEODIR_GD_BOOSTER_DEBUGGING_ENABLE) {
									$debug_text = '<!-- GD Booster skipped ' . $element . ' -->';
								}
								
								$out = str_replace( $element, $debug_text, $out );
							}
						}
					}
				}

				$js_rel_files = array_unique($js_rel_files);
				$js_abs_files = array_unique($js_abs_files);
				// Creating Booster markup
				$js_rel_files = implode(',',$js_rel_files);
				$js_abs_files = implode(',',$js_abs_files);
				$js_plain = preg_replace('/\/\*.*?\*\//ims','',$js_plain);
				$js_plain .= 'try {document.execCommand("BackgroundImageCache", false, true);} catch(err) {}';
				
				if (!empty($js_external_files)) {
					$booster_out .= "\r\n" . implode("\r\n", $js_external_files) . "\r\n";
				}
				
				$booster_out .= '<script type="text/javascript" src="'.
				get_option('siteurl').'/wp-content/plugins/'.$booster_folder.'/booster_js.php?dir='.
				htmlentities(str_replace('..','%3E',$js_rel_files)).
				'&amp;cachedir='.htmlentities(str_replace('..','%3E',$booster_cache_reldir),ENT_QUOTES).
				(($booster->debug) ? '&amp;debug=1' : '').
				((!$booster->js_minify) ? '&amp;js_minify=0' : '').
				(($booster->js_closure_compiler) ? '&amp;js_cc=1' : '').
				'&amp;nocache='.$booster->filestime.
				'?'.implode('&amp;',$js_parameters).'"></script>';
				if (!empty($js_exclude_files)) {
					$booster_out .= "\r\n" . implode("\r\n", $js_exclude_files) . "\r\n";
				}
				$booster_out .= '<script type="text/javascript">'.$js_plain.'</script>';
				$booster_out .= "\r\n";

                /*
                 * Filter the booster out js, allows you to add something before or after the JS output.
                 *
                 * @param string The JS script output contained in script tags.
                 * @since 1.0.9
                 */
                $booster_out = apply_filters('gd_booster_booster_out_js', $booster_out);
				#$booster_out .= "\r\n<!-- ".$js_abs_files." -->\r\n";
				
				// Injecting the result at the bottom
				//$out = str_replace('</head>',$booster_out.'</head>',$out);
				///*

                /*
                 * Filter the page output html before the JS code is added.
                 *
                 * @param string The entire page HTML before the new JS file is added.
                 * @since 1.0.9
                 */
                $out = apply_filters('gd_booster_out', $out);
				if ( strpos( $out, "</body>" ) !== false ) {
					$out = str_replace('</body>',$booster_out.'</body>',$out);
				} else {
					$out .= $booster_out;
				}
				//*/
			}
		}
		else $out = str_replace('<body','<div style="display: block; padding: 1em; background-color: #FFF9D0; color: #912C2C; border: 1px solid #912C2C; font-family: Calibri, \'Lucida Grande\', Arial, Verdana, sans-serif; white-space: pre;">You need to upgrade to PHP 5 or higher to have CSS-JS-Booster work. You currently are running on PHP '.phpversion().'</div><body',$out);
		
		// Recreate output buffer
		ob_end_clean();
		if (
		isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
		&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') 
		&& function_exists('ob_gzhandler') 
		&& (!ini_get('zlib.output_compression') || intval(ini_get('zlib.output_compression')) <= 0) 
		&& !function_exists('wp_cache_ob_callback')
		) @ob_start('ob_gzhandler');
		elseif(function_exists('wp_cache_ob_callback')) @ob_start('wp_cache_ob_callback');
		else @ob_start();
		
		// Output page
		echo $out;
	}
}

// wordpress SEO fix
add_filter( 'wpseo_json_ld_search_output', 'gd_booster_wordpress_seo_fix', 10, 1 ); 

function gd_booster_wordpress_seo_fix($code){
	if (strpos($code,'[') !== false) {
    	//they fixed it
	}else{
	//we fix it
	$code = str_replace('<script type="application/ld+json">', '<script type="application/ld+json">[', $code);
	$code = str_replace('</script>', ']</script>', $code);
	}
	return 	$code;
}

/**
 * Exclude javascript/css file from GD booster cache.
 *
 * @since 1.0.6
 *
 * @param sting $fileurl Retlative path of javascript/css file.
 * @param array $exclude_files Array of files to excludes from GD booster cache.
 * @return bool If true file excluded from GD booster cache.
 */
function gd_booster_exclude_file($fileurl, $exclude_files = array()){
	$return = false;
	
	if (!empty($exclude_files)) {
		foreach ($exclude_files as $exclude_file) {
			if ($exclude_file != '' && $fileurl != '' && strpos($fileurl, $exclude_file) !== false) {
				$return = true;
			}
		}
	}
	return $return;
}

/**
 * Exclude javascript from GD booster cache.
 *
 * @since 1.0.9
 *
 * @param bool $continue Whether to exclude script element or not.
 * @param sting $content Script element.
 * @return bool If true script element excluded.
 */
function geodir_booster_exclude_js( $continue, $content ) { 
	// Skip google ads js file
	if (strpos($content, '/pagead/js/adsbygoogle.js') !== false || strpos($content, '/pagead/show_ads.js') !== false) {
		$continue = true; 
	}
	
	// Skip google ads inline script
	if (strpos($content, 'window.adsbygoogle') !== false || (strpos($content, 'google_ad_client') !== false && strpos($content, 'google_ad_slot') !== false)) {
		$continue = true; 
	}

    // s2member
    if (strpos($content, 's2member_js') ) {
        global $gdb_s2member_active;
        $gdb_s2member_active = $content;
        add_action('gd_booster_booster_out_js','gd_booster_s2member_fix_js',10,1);
        add_action('gd_booster_out','gb_booster_s2member_fix_out',10,1);
        $continue = true;
    }

	return $continue; 
} 
add_filter( 'geodir_booster_script_continue', 'geodir_booster_exclude_js', 10, 2 );



$gdb_s2member_active = false;

function gd_booster_s2member_fix_js($booster_out){
    global $gdb_s2member_active;


    return $booster_out. $gdb_s2member_active;
}

function gb_booster_s2member_fix_out($out){
    global $gdb_s2member_active;

    $out = str_replace($gdb_s2member_active,"",$out);

    return $out;
}

