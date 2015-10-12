<?php
/**
 * Single Event Meta (Organizer) Template
 *
 */

$phone = tribe_get_organizer_phone();
$email = tribe_get_organizer_email();
$website = tribe_get_organizer_website_link();
?>

<div class="grve-tribe-events-meta-group grve-tribe-events-meta-group-organizer">
	<h5 class="grve-title"> <?php _e( tribe_get_organizer_label_singular(), GRVE_THEME_TRANSLATE ) ?> </h5>
	<ul>
		<?php do_action( 'tribe_events_single_meta_organizer_section_start' ) ?>

		<li class="fn org"> <?php echo tribe_get_organizer() ?> </li>

		<?php if ( ! empty( $phone ) ): ?>
			<li>
				<span> <?php _e( 'Phone:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="tel"> <?php echo $phone ?> </div>
			</li>
		<?php endif ?>

		<?php if ( ! empty( $email ) ): ?>
			<li>
				<span> <?php _e( 'Email:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="email"> <?php echo $email ?> </div>
			</li>
		<?php endif ?>

		<?php if ( ! empty( $website ) ): ?>
			<li>
				<span> <?php _e( 'Website:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="url"> <?php echo $website ?> </div>
			</li>
		<?php endif ?>

		<?php do_action( 'tribe_events_single_meta_organizer_section_end' ) ?>
	</ul>
</div>