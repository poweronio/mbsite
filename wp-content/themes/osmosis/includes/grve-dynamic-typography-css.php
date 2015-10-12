<?php
/**
 *  Dynamic typography css style
 * 	@author		Greatives Team
 * 	@URI		http://greatives.eu
 */

$typo_css = "";

/**
 * Typography
 * ----------------------------------------------------------------------------
 */

/* Main */
$typo_css .= "

body {
	font-size: " . grve_option( 'body_font', '14px', 'font-size'  ) . ";
	font-family: " . grve_option( 'body_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'body_font', 'normal', 'font-weight'  ) . ";
	" . grve_css_option( 'body_font', '', 'letter-spacing'  ) . "
}

input[type='text'],
input[type='input'],
input[type='password'],
input[type='email'],
input[type='number'],
input[type='date'],
input[type='url'],
input[type='tel'],
input[type='search'],
textarea,
select {
	font-family: " . grve_option( 'body_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
}

";

/* Logo as text */
$typo_css .= "

#grve-header .grve-logo.grve-logo-text a {
	font-family: " . grve_option( 'logo_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'logo_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'logo_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'logo_font', '11px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'logo_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'logo_font', '', 'letter-spacing'  ) . "
}

";

/* Main Menu  */
$typo_css .= "

#grve-header #grve-main-menu ul li ul li a {
	font-family: " . grve_option( 'sub_menu_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'sub_menu_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'sub_menu_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'sub_menu_font', '11px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'sub_menu_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'sub_menu_font', '', 'letter-spacing'  ) . "
}

#grve-header #grve-main-menu > ul > li > a,
#grve-header .grve-responsive-menu-text {
	font-family: " . grve_option( 'main_menu_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'main_menu_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'main_menu_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'main_menu_font', '11px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'main_menu_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'main_menu_font', '', 'letter-spacing'  ) . "
}


";

/* Headings */
$typo_css .= "

h1 {
	font-family: " . grve_option( 'h1_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h1_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h1_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h1_font', '68px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h1_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h1_font', '', 'letter-spacing'  ) . "
}

h2 {
	font-family: " . grve_option( 'h2_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h2_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h2_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h2_font', '50px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h2_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h2_font', '', 'letter-spacing'  ) . "
}

h3 {
	font-family: " . grve_option( 'h3_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h3_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h3_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h3_font', '34px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h3_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h3_font', '', 'letter-spacing'  ) . "
}

h4,
.woocommerce h1 {
	font-family: " . grve_option( 'h4_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h4_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h4_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h4_font', '25px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h4_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h4_font', '', 'letter-spacing'  ) . "
}

h5,
#reply-title,
.grve-product-name,
.woocommerce h2,
.woocommerce-billing-fields h3,
#order_review_heading {
	font-family: " . grve_option( 'h5_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h5_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h5_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h5_font', '18px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h5_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h5_font', '', 'letter-spacing'  ) . "
}

h6,
.mfp-title,
.woocommerce table.shop_table th,
.woocommerce-page table.shop_table th,
.woocommerce div.product .woocommerce-tabs ul.tabs li a,
.woocommerce #content div.product .woocommerce-tabs ul.tabs li a,
.woocommerce-page div.product .woocommerce-tabs ul.tabs li a,
.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a {
	font-family: " . grve_option( 'h6_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'h6_font', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'h6_font', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'h6_font', '14px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'h6_font', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'h6_font', '', 'letter-spacing'  ) . "
}

";

/* Page Title */
$typo_css .= "

#grve-page-title .grve-title {
	font-family: " . grve_option( 'page_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'page_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'page_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'page_title', '60px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'page_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'page_title', '', 'letter-spacing'  ) . "
}

#grve-page-title .grve-description {
	font-family: " . grve_option( 'page_description', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'page_description', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'page_description', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'page_description', '24px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'page_description', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'page_description', '', 'letter-spacing'  ) . "
}

";

/* Portfolio Title */
$typo_css .= "

#grve-portfolio-title .grve-title {
	font-family: " . grve_option( 'portfolio_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'portfolio_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'portfolio_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'portfolio_title', '60px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'portfolio_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'portfolio_title', '', 'letter-spacing'  ) . "
}

#grve-portfolio-title .grve-description {
	font-family: " . grve_option( 'portfolio_description', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'portfolio_description', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'portfolio_description', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'portfolio_description', '24px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'portfolio_description', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'portfolio_description', '', 'letter-spacing'  ) . "
}

";

/* Product Title */
$typo_css .= "

#grve-product-title .grve-title {
	font-family: " . grve_option( 'product_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'product_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'product_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'product_title', '60px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'product_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'product_title', '', 'letter-spacing'  ) . "
}

";

/* Post Title */
$typo_css .= "

#grve-post-title .grve-title,
#grve-main-content.grve-simple-style .grve-post-simple-title {
	font-family: " . grve_option( 'post_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'post_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'post_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'post_title', '60px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'post_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'post_title', '', 'letter-spacing'  ) . "
}

";

/* Feature Section */
$typo_css .= "

#grve-header[data-fullscreen='no'] #grve-feature-section .grve-title {
	font-family: " . grve_option( 'custom_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'custom_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'custom_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'custom_title', '60px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'custom_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'custom_title', '', 'letter-spacing'  ) . "
}

#grve-header[data-fullscreen='no'] #grve-feature-section .grve-description {
	font-family: " . grve_option( 'custom_description', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'custom_description', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'custom_description', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'custom_description', '24px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'custom_description', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'custom_description', '', 'letter-spacing'  ) . "
}

#grve-header[data-fullscreen='yes'] #grve-feature-section .grve-title {
	font-family: " . grve_option( 'fullscreen_custom_title', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'fullscreen_custom_title', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'fullscreen_custom_title', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'fullscreen_custom_title', '100px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'fullscreen_custom_title', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'fullscreen_custom_title', '', 'letter-spacing'  ) . "
}

#grve-header[data-fullscreen='yes'] #grve-feature-section .grve-description {
	font-family: " . grve_option( 'fullscreen_custom_description', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'fullscreen_custom_description', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'fullscreen_custom_description', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'fullscreen_custom_description', '30px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'fullscreen_custom_description', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'fullscreen_custom_description', '', 'letter-spacing'  ) . "
}

";

/* Special Text */
$typo_css .= "

.grve-leader-text p,
p.grve-leader-text {
	font-family: " . grve_option( 'leader_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'leader_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'leader_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'leader_text', '34px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'leader_text', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'leader_text', '', 'letter-spacing'  ) . "
}

.grve-subtitle p,
.grve-subtitle,
.grve-accordion .grve-title,
.grve-toggle .grve-title,
blockquote,
.woocommerce div.product span.price,
.woocommerce div.product p.price,
.woocommerce #content div.product span.price,
.woocommerce #content div.product p.price,
.woocommerce-page div.product span.price,
.woocommerce-page div.product p.price,
.woocommerce-page #content div.product span.price,
.woocommerce-page #content div.product p.price {
	font-family: " . grve_option( 'subtitle_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'subtitle_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'subtitle_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'subtitle_text', '18px', 'font-size'  ) . ";
	text-transform: " . grve_option( 'subtitle_text', 'none', 'text-transform'  ) . ";
	" . grve_css_option( 'subtitle_text', '', 'letter-spacing'  ) . "
}

.grve-small-text,
small,
#grve-meta-bar .grve-meta-elements a,
#grve-meta-bar .grve-meta-elements li.grve-field-date,
#grve-anchor-menu a,
.grve-tags,
.grve-categories,
#grve-post-title #grve-social-share ul li .grve-like-counter,
#grve-portfolio-bar #grve-social-share ul li .grve-like-counter,
.grve-blog .grve-like-counter,
.grve-blog .grve-post-author,
.grve-blog .grve-post-date,
.grve-pagination ul li,
#grve-header-options ul.grve-options a span,
.grve-pagination .grve-icon-nav-right,
.grve-pagination .grve-icon-nav-left,
#grve-comments .grve-comment-item .grve-comment-date,
#grve-comments .comment-reply-link,
#grve-comments .comment-edit-link,
.grve-newsletter label,
#grve-footer-bar .grve-social li,
#grve-footer-bar .grve-copyright,
#grve-footer-bar #grve-second-menu,
#grve-share-modal .grve-social li a,
#grve-language-modal .grve-language li a,
.grve-bar-title,
.grve-percentage,
.grve-tabs-title li,
.grve-pricing-table ul li,
.logged-in-as,
.widget.widget_recent_entries li span.post-date,
cite,
label,
.grve-testimonial-name,
.grve-hr .grve-divider-backtotop,
.grve-slider-item .grve-slider-content span.grve-title,
.grve-gallery figure figcaption .grve-caption,
.widget.widget_calendar caption,
.widget .rss-date,
.widget.widget_tag_cloud a,
.grve-widget.grve-latest-news .grve-latest-news-date,
.grve-widget.grve-comments .grve-comment-date,
.wpcf7-form p,
.wpcf7-form .grve-one-third,
.wpcf7-form .grve-one-half,
.mfp-counter,
.grve-related-post .grve-caption,
.grve-comment-nav ul li a,
.grve-portfolio .grve-like-counter span,
.grve-portfolio .grve-portfolio-btns,
.grve-portfolio .grve-filter,
.grve-blog .grve-filter,
.grve-image-hover .grve-caption,
.grve-portfolio .grve-hover-style-2 figcaption .grve-caption,
ul.grve-fields li,
.grve-team-social li a,
.grve-carousel-wrapper .grve-post-item .grve-caption,
.grve-blog .grve-like-counter span,
.grve-add-cart,
.grve-map-infotext p,
a.grve-infotext-link,
#grve-meta-responsive,
.woocommerce span.onsale,
.woocommerce nav.woocommerce-pagination ul li,
.woocommerce #content nav.woocommerce-pagination ul li,
.woocommerce-page nav.woocommerce-pagination ul li,
.woocommerce-page #content nav.woocommerce-pagination ul li,
.woocommerce .woocommerce-result-count,
.woocommerce-page .woocommerce-result-count,
.woocommerce-review-link,
.product_meta,
table.shop_table td.product-name,
.woocommerce .related h2,
.woocommerce .upsells.products h2,
.woocommerce-page .related h2,
.woocommerce-page .upsells.products h2,
.woocommerce .cross-sells h2,
.woocommerce .cart_totals h2,
.woocommerce .shipping-calculator-button,
.woocommerce-page .shipping-calculator-button,
.woocommerce-info,
#grve-shop-modal .cart_list.product_list_widget li a,
.woocommerce .widget_price_filter .price_slider_amount,
.woocommerce-page .widget_price_filter .price_slider_amount,
.woocommerce ul.cart_list li a,
.woocommerce ul.product_list_widget li a,
.woocommerce-page ul.cart_list li a,
.woocommerce-page ul.product_list_widget li a,
.woocommerce.widget_product_tag_cloud .tagcloud a {
	font-family: " . grve_option( 'small_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'small_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'small_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'small_text', '10px', 'font-size'  ) . " !important;
	text-transform: " . grve_option( 'small_text', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'small_text', '', 'letter-spacing'  ) . "
}


.grve-author-info .grve-read-more,
.more-link,
.grve-read-more,
.grve-blog.grve-isotope[data-type='pint-blog'] .grve-isotope-item .grve-media-content .grve-read-more span,
.grve-newsletter input[type='submit'],
.grve-search button[type='submit'],
#grve-above-footer .grve-social li,
.grve-btn,
input[type='submit'],
input[type='reset'],
button,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button,
.woocommerce #respond input#submit,
.woocommerce #content input.button,
.woocommerce-page a.button,
.woocommerce-page button.button,
.woocommerce-page input.button,
.woocommerce-page #respond input#submit,
.woocommerce-page #content input.button,
#grve-shop-modal a.button {
	font-family: " . grve_option( 'link_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'link_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'link_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'link_text', '11px', 'font-size'  ) . " !important;
	text-transform: " . grve_option( 'link_text', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'link_text', '', 'letter-spacing'  ) . "
}

";


/* Trim css for speed */
$typo_css_trim =  preg_replace( '/\s+/', ' ', $typo_css );

/* Add stylesheet Tag */
$typo_css_out = "<!-- Dynamic css -->\n<style type=\"text/css\">\n" . $typo_css_trim . "\n</style>";

echo $typo_css_out;

?>
