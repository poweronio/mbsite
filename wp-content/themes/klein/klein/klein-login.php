<?php
/**
 * Klein Login
 *
 * Overwrites default WordPress login style
 *
 * @package klein
 * @since 1.0.2
 */
?>
<?php
if( !function_exists('klein_login_logo_url') ){
	function klein_login_logo_url() {
		return home_url();
	}
}
add_filter( 'login_headerurl', 'klein_login_logo_url' );
?>
<?php if( !function_exists('klein_login_logo') ){ ?>
<?php function klein_login_logo() { ?>
	<?php
		// get the logo
		$default_logo = get_template_directory_uri() . '/logo.png';
		$logo = ot_get_option( 'logo', $default_logo );
	?>
    <style type="text/css">
	
	 @import url('http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,600,300italic');
       	
       	body.login form .forgetmenot label,
		body.login label {
			color: #34495E;
			font-size: 16px;
		}

		body.login form .forgetmenot label
		{
			line-height: 1;
			padding-top: 9px;
			font-style: italic;
			font-weight: 400;
			display: block;
		}

		body.login #login,
		body.login div#login form {width: 375px; margin: 0 auto;}

		body.login { background: #BDC3C7 ;}
		body.login div#login h1 a {
			background-image: url(<?php echo $logo ?>);
            padding-bottom: 30px;
			background-position: center center;
			background-size: auto;
			width: 350px;
			height: 67px;
		}
		body.login div#login form {
			background: #ECF0F1;
			-webkit-box-shadow: none;
			box-shadow: none;
			border-radius: 0;
			-webkit-border-radius: 0;
			-moz-border-radius: 0;
			-webkit-border-radius: 0;
			font-family: "Source Sans Pro", sans-serif;
			border-radius: 4px;
			-moz-border-radius: 4px;
			-webkit-border-radius: 4px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			padding: 35px 40px 35px 40px;
			
		}
		
		body.login form .input, 
		.login input[type="text"] {
			-webkit-box-shadow: none;
			box-shadow: none;
			border-radius: 0;
			font-family: 'Source Sans Pro', sans-serif;
		}

		body.login input[type="text"],
		body.login input[type="password"] {
			padding: 8px 12px;
			display: inline-block;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			border: 1px solid #bdc3c7;
			outline: 0;
			margin-bottom: 20px;
			height: 42px;
			display: block;
			width: 100%;
			max-width: 100%;
			-webkit-transition: border .25s linear,color .25s linear,background-color .25s linear;
			transition: border .25s linear,color .25s linear,background-color .25s linear;
			background: #ECF0F1;
			-webkit-box-shadow: inset 0px 0px 14px -6px #7F8C8D;
			-moz-box-shadow: inset 0px 0px 14px -6px #7F8C8D;
			box-shadow: inset 0px 0px 14px -6px #7F8C8D;
		}


		body.login a{
				color: #ECF0F1;
		}
		body.login .message,
		body.login #login_error{

			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;

			border: 1px solid #E74C3C;
			color: #C0392B;
			
			background: transparent;
			padding: 15px 35px;
			margin-bottom: 35px;
			box-shadow: none;
		}

		body.login .message a,
		body.login #login_error a{
			color: #E74C3C;
		}

		body.login .button,
		body.login div#login form#loginform p.submit input#wp-submit {
			background: none;
			background-color: transparent;
			box-shadow: none;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			border: 0;

			/*start*/

			color: #fff;
			-moz-border-radius: 20px;
			-webkit-border-radius: 20px;
			border-radius: 20px;

			background: #2ECC71;
			display: block;
			
			padding: 10px 20px;
			line-height: 1;
			height: auto;
			font-size: 15px;
		}
		
		body.login .button:active,
		body.login div#login form#loginform p.submit input#wp-submit:active
		{
			-webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
			box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
			background-image: none;
			background-color: #3071a9;
			border-color: #2d6ca2;
		}
		
		body.login div#login p#backtoblog,
		body.login div#login p#nav {
			text-shadow: none;
			font-style: italic;
			margin-top: 20px;
			padding-top: 0;
			line-height: 1;
		}
		 
		body.login div#login p#backtoblog {
			text-shadow: none;
		}
		body.login div#login p#nav,
		body.login div#login p#nav a,
		body.login div#login p#backtoblog a {
			color: #2C3E50!important;
			font-size: 16px;
			text-decoration: none;
			font-family: 'Source Sans Pro', sans-serif;
		}
		
		 body.login #ce-facebook-connect-link a {
			background: #3B5A9B;
			padding: 10px 0px;
			display: block;
			margin-bottom: 20px;
			text-align: center;
			color: #ECF0F1;
			font-size: 16px;
			font-weight: bold;
			text-decoration: none;
			border-radius: 3px;
			text-transform: uppercase;
        }
		
		body.login #ce-facebook-connect-link a:active {
			position: relative;
			background: #426ABE;
		}
    </style>
<?php } 
} // end func!klein_login_logo
add_action( 'login_enqueue_scripts', 'klein_login_logo' );
?>