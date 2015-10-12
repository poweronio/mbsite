/**
 * Klein Javascript
 *
 * This file contains Klein javascript functions
 * and definitions, method, or any javacript function
 * that corresponds to the theme
 *
 * @package Klein
 */
jQuery(document).ready(function($) {

    "use strict";

		/**
		 * Updates/Notifcation Menu
		 **/

		$("#klein-top-updates-btn").click(function(e) {
			e.preventDefault();
			$("#klein-top-updates-nav").toggle();
		});
		
		/**
		 * Main Menu Navigation
		 */
		$('.menu.mobile ul.sub-menu').prev('a').addClass('desktop-menu-data-dropdown').append("&nbsp; <span class='sub-menu-toggle'><i class='nav-icon fa fa-angle-down'></i></span>");
		$('.menu.mobile ul.children').prev('a').addClass('desktop-menu-data-dropdown').append("&nbsp; <span class='sub-menu-toggle'><i class='nav-icon fa fa-angle-down'></i></span>");
		
		
		$('.nav-btn').click(function() {
			var dropdown = $(this).attr('data-dropdown');
			$(dropdown).fadeToggle();
		});
		
	
		
		// nav menu widgets
		if ($('.widget.widget_nav_menu').length == 1) {
			$(".widget.widget_nav_menu ul.sub-menu").prev('a').addClass('desktop-menu-data-dropdown').append("&nbsp; <i class='nav-menu-icon nav-icon fa fa-angle-down'></i>");
			$('.widget.widget_nav_menu li a i').click(function(e) {
				e.preventDefault();
				if ($(this).hasClass('fa fa-angle-down')) {
					$(this).removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
				} else {
					$(this).removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
				}
				$(this).parent().next('ul.sub-menu').slideToggle();
			});
		}
		
		/**
		* Responsive Menu Toggle
		*/
		$('.sub-menu-toggle').on('click', function(e) {
			e.preventDefault();
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$('i', $(this)).removeClass('fa fa-angle-up').addClass('fa fa-angle-down');
			} else {
				$(this).addClass('open');
				$('i', $(this)).removeClass('fa fa-angle-down').addClass('fa fa-angle-up');
			}
			$('menu.mobile ul.sub-menu').css('display', 'none');
			$(this).parent().next('ul').toggle();
		});
		
		/**
		* Tooltips
		*/
		$('.tip').tooltip({
			delay: {
				show: 0,
				hide: 0
			},
			animation: true
		});
		
		/**
		* Popover
		*/
		$('.pophover').popover({
			trigger: 'hover'
		});
		
		/**
		* Carousel (Standard)
		*/
		if ($('.gears-carousel-standard').length >= 1) {
			var $klein_carousel_standard = $('.gears-carousel-standard');
			$.each($klein_carousel_standard, function() {
				
				var __this = $(this);
				var max_slides = (__this.attr('data-max-slides') !== undefined && __this.attr('data-max-slides').length >= 1) ? __this.attr('data-max-slides') : 7;
				var min_slides = (__this.attr('data-min-slides') !== undefined && __this.attr('data-min-slides').length >= 1) ? __this.attr('data-min-slides') : 1;
				var slide_width = (__this.attr('data-item-width') !== undefined && __this.attr('data-item-width').length >= 1) ? __this.attr('data-item-width') : 85;
				
				var prop = {
					minSlides: parseInt(min_slides),
					maxSlides: parseInt(max_slides),
					slideWidth: parseInt(slide_width),
					nextText: '<span class="fa fa-angle-right"></span>',
					prevText: '<span class="fa fa-angle-left"></span>',
					pager: false,
					moveSlides: 3,
					slideMargin: 20
				};
				
				__this.bxSlider(prop);
				
			});
		}
	
		/**
		* Magnific Popup
		*/
		var $magnific_popup_config = {
			type: 'image',
			gallery: {
				enable: true
			}
		};

		// initialize
		$('.klein-zoom').magnificPopup($magnific_popup_config);

		// additional class to support popup
		$('.klein-popup').magnificPopup({
			type: 'image'
		});
		
		// hide BuddyPress subnav if empty
		if ($('.item-list-tabs#subnav ul').length >= 1) {
			if ("" === $.trim($('.item-list-tabs#subnav ul').html())) {
				$('.item-list-tabs#subnav').remove();
			}
		}
		
		// hover effect on profile bubble
		var $user_nav = $( '.item-list-tabs li' );
			if( $user_nav.length >=1 ){
				$( $user_nav ).mouseenter( function(e){
					var $bubble = $( 'a span', $(this) );
						if( $bubble.length >=1 ){
							$bubble.animate({
								top: '-19px'
							});
						}
				}).mouseout( function(e){
					var $bubble = $( 'a span', $(this) );
						if( $bubble.length >=1 ){
							$bubble.animate({
								top: '-12px'
							});
						}
				});
			}

		// organize rtmedia-list
		function doMasonryRtMediaList() {
			var $listIsotopeMasonry = $('.activity .rtmedia-list');

			if ($listIsotopeMasonry.length >= 1) 
			{
				imagesLoaded($listIsotopeMasonry, function(){
					try {
						$listIsotopeMasonry.isotope({
							layoutMode: 'masonry'
						});
					} catch(e) {}
				
				});
			}
			return;
		}	
		(function recurseRtMediaTiles() {
		    doMasonryRtMediaList();
		    setTimeout(recurseRtMediaTiles, 500);
		    return;
		})();	
		
		// organize buddypress members list 
		// and groups list using jQuery
		// isotopes
		
		var kleinTileBpItemList = function() {
			var $bpItemList = $('ul.bp-objects-loop');

			if ($bpItemList.length >= 1) 
			{
				// check if all avatars are loaded
				if (imagesLoaded) {
					imagesLoaded($bpItemList, function(){
						try{
							$bpItemList.isotope({
								itemSelector: 'li',
								layoutMode: 'fitRows'
							});
						} catch(e) {
							console.log('warning isotope is not found');
						}
					});
				}
			}

			return true;
		}

		kleinTileBpItemList();

		// jQuery sticky menu
		
		if ('on' === kleinConfig.isStickyMenu) 
		{

			var calculateViewPortStickyHeading = function()
			{
				var topBarSpace = 0;
				var viewportWidth = $(window).width();
				var wpBreakPointWidth = 768;

				// disable on mobile
				if (viewportWidth > wpBreakPointWidth) { 
					if ($('#wpadminbar').length >= 1) {
						topBarSpace = $('#wpadminbar').height();
					}

					// reset the topBarSpace to 0 
					// when inside visual composer's edit 
					// page or screen
					if ($('#wpadminbar').css('display') == 'none') {
						topBarSpace = 0;
					}

					$('#bp-klein-top-bar').unstick();
					$("#bp-klein-top-bar").sticky({topSpacing: topBarSpace});
				}
				return;
			}

			$(window).resize(function(){
				calculateViewPortStickyHeading();
			});
			
			calculateViewPortStickyHeading();
			
		}
		

		// Scroll Top
		if ('on' === kleinConfig.hasBackToTop) 
		{
			if ($('#kleinScrollToTop').length >=1 ) {
				$("#kleinScrollToTop").click(function(e){
					e.preventDefault();
					var body = $("html, body");
					body.animate({scrollTop:0}, '500', 'swing');	
				});
			
				$(window).scroll(function() {
		  			var top = $(window).scrollTop();
		  				if (top >= 850) {
		  					$("#kleinScrollToTop").fadeIn();
		  				} else {
		  					$("#kleinScrollToTop").fadeOut();
		  				}
				});
			}
		}

		/**
		 * Search Menu
		 */
		$('#klein-search-btn').click(function(e){
			e.preventDefault();
			$('#klein-search-container').toggleClass('active');
		});

		/**
		 * Article Sharing
		 */
		$('.article-sharer').click(function(e){
			e.preventDefault();
			var url = $(this).attr('href');
			var sharerWindow = window.open(url,'','height=220,width=550');
		});

		/**
		 * Members & Groups Parallax Effect
		 */
		if ($('img#cover-photo').length >=1) {

			$(document).on('scroll', function() {
			 	
			 	var speed = 5;
			 	var blurrSpeed = 10;	
				var offsetTop = $(window).scrollTop() / speed;
				var blurr = offsetTop / blurrSpeed;
				var opacity = ((100 - offsetTop)) / 100;

				if (offsetTop >= 100) {
					return;
				}

				$('img#cover-photo').css({
					'position': 'relative',
					'transform': 'translateY('+offsetTop+'px)',
					'opacity': opacity,
				});
			
			});
		 
		}

});