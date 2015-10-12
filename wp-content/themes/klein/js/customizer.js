/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );

	// Top Bar Background
	wp.customize('klein_top_bar_background', function(value){
		value.bind(function (newColor){
			$('#klein-top-links').css({
				background: newColor
			});
		});
	});

	// Top Bar Color
	wp.customize('klein_top_bar_color', function(value){
		value.bind(function (newColor){
			$('#klein-top-links ul li a').css({
				color: newColor
			});
		});
	});

	//Navigation Background
	wp.customize( 'klein_navigation_background', function( value ) {
        value.bind( function( to ) {
            $( '#bp-klein-top-bar' ).css( 'background', to );
            $( '.desktop-menu ul.sub-menu li a' ).css( 'background', to );
        } );
    });
	// Navigation Foreground
	wp.customize( 'klein_navigation_color', function( value ) {
        value.bind( function( to ) {
            $( '.desktop-menu ul li a' ).css( 'color', to );
        } );
    });
	
	// Footer Widgets
	wp.customize('klein_footer_widget_background_settings', function(element){
		element.bind(function(newValue){
			$('#footer-widgets').css('background', newValue);
		});
	});
	
	// Footer background
	wp.customize( 'klein_footer_background', function( value ){
		value.bind( function( to ){
			$( '#footer' ).css( 'background', to );
		});
	});
	
	// Footer foreground
	wp.customize( 'klein_footer_color', function( value ){
		value.bind( function( to ){
			$( '#footer' ).css( 'color', to );
		});
	});
	
	
} )( jQuery );
