<?php
/**
 * WooCommerce Content Sidebar
 *
 * @package Klein
 */
?>

<div id="primary" class="content-area col-sm-9 col-md-9">
	<div id="content" class="site-content" role="main">
		<?php
		if ( is_singular( 'product' ) ) {
		 	woocommerce_content();
		}else{
			//For ANY product archive.
				//Product taxonomy, product search or /shop landing
		 	woocommerce_get_template( 'archive-product.php' );
		}
		?>
	</div><!-- #content -->
</div><!-- #primary -->

<div id="secondary" class="col-sm-3 col-md-3">
	<?php get_sidebar( 'woocommerce-sidebar-right'); ?>
</div>
