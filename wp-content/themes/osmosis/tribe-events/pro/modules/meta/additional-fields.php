<?php
/**
 * Single Event Meta (Additional Fields) Template
 *
 */

if ( ! isset( $fields ) || empty( $fields ) || ! is_array( $fields ) ) {
	return;
}
?>

<div class="grve-tribe-events-meta-group grve-tribe-events-meta-group-other">
	<h5 class="grve-title"> <?php _e( 'Other', GRVE_THEME_TRANSLATE ) ?> </h5>
	<ul>
		<?php foreach ( $fields as $name => $value ): ?>
			<li>
				<span><?php echo $name; ?> </span>
				<?php echo $value; ?>
			</li>
		<?php endforeach ?>
	</ul>
</div>