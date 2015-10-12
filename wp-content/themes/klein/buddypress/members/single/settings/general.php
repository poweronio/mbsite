<?php do_action( 'bp_before_member_settings_template' ); ?>

<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

	<?php if ( !is_super_admin() ) : ?>

		<label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'klein' ); ?></label>
		<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" /> &nbsp;<a href="<?php echo wp_lostpassword_url(); ?>" title="<?php esc_attr_e( 'Password Lost and Found', 'klein' ); ?>"><?php _e( 'Lost your password?', 'klein' ); ?></a>

	<?php endif; ?>

	<label for="email"><?php _e( 'Account Email', 'klein' ); ?></label>
	<input type="text" name="email" id="email" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" />

	<label for="pass1"><?php _e( 'Change Password <span>(leave blank for no change)</span>', 'klein' ); ?></label>
	<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small password-entry" /> &nbsp;<?php _e( 'New Password', 'klein' ); ?><br />
	<div id="pass-strength-result"></div>
	<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small password-entry-confirm" /> &nbsp;<?php _e( 'Repeat New Password', 'klein' ); ?>

	<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

	<div class="submit mg-top-20">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'klein' ); ?>" id="submit" class="auto" />
	</div>

	<?php do_action( 'bp_core_general_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'bp_settings_general' ); ?>

</form>

<?php do_action( 'bp_after_member_settings_template' ); ?>
