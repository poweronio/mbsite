<?php do_action( 'bp_before_member_settings_template' ); ?>

<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
	<p><?php _e( 'Send an email notice when:', 'klein' ); ?></p>

	<?php do_action( 'bp_notification_settings' ); ?>

	<?php do_action( 'bp_members_notification_settings_before_submit' ); ?>

	<div class="submit mg-top-20">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'klein' ); ?>" id="submit" class="auto" />
	</div>

	<?php do_action( 'bp_members_notification_settings_after_submit' ); ?>

	<?php wp_nonce_field('bp_settings_notifications' ); ?>

</form>

<?php do_action( 'bp_after_member_settings_template' ); ?>
