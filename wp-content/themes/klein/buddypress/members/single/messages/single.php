<div id="message-thread" role="main">

	<?php do_action( 'bp_before_message_thread_content' ); ?>

	<?php if ( bp_thread_has_messages() ) : ?>

		<h3 id="message-subject"><?php bp_the_thread_subject(); ?></h3>

		<p class="alert alert-info" id="message-recipients">
			<i class="fa fa-comments-o"></i>
			<span class="highlight">

				<?php if ( !bp_get_the_thread_recipients() ) : ?>

					<?php _e( 'You are alone in this conversation.', 'klein' ); ?>

				<?php else : ?>

					<?php printf( __( 'Conversation between %s and you.', 'klein' ), bp_get_the_thread_recipients() ); ?>

				<?php endif; ?>

			</span>

			<a class="btn btn-danger" href="<?php bp_the_thread_delete_link(); ?>" title="<?php esc_attr_e( "Delete Message", "klein" ); ?>"><?php _e( 'Delete', 'klein' ); ?></a> &nbsp;
		
		</p>

		<?php do_action( 'bp_before_message_thread_list' ); ?>

		<?php while ( bp_thread_messages() ) : bp_thread_the_message(); ?>

			<div class="message-box <?php bp_the_thread_message_css_class(); ?>">

				<div class="message-metadata">

					<?php do_action( 'bp_before_message_meta' ); ?>

					<?php bp_the_thread_message_sender_avatar( 'type=full&width=60&height=60' ); ?>

					

					<?php do_action( 'bp_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<?php do_action( 'bp_before_message_content' ); ?>

				<div class="message-content">
					
					<div class="message-content-sender">
						<?php if ( bp_get_the_thread_message_sender_link() ) : ?>
							<a href="<?php bp_the_thread_message_sender_link(); ?>" title="<?php bp_the_thread_message_sender_name(); ?>">
								<?php bp_the_thread_message_sender_name(); ?>
							</a>
						<?php else : ?>
							<?php bp_the_thread_message_sender_name(); ?>
						<?php endif; ?>
					</div>

					<div class="message-content-body">
						<?php bp_the_thread_message_content(); ?>
						<span class="activity"><?php bp_the_thread_message_time_since(); ?></span>
					</div>
					<div class="cleafix"></div>
				
				</div><!-- .message-content -->

				<?php do_action( 'bp_after_message_content' ); ?>

				<div class="clear"></div>

			</div><!-- .message-box -->

		<?php endwhile; ?>

		<?php do_action( 'bp_after_message_thread_list' ); ?>

		<?php do_action( 'bp_before_message_thread_reply' ); ?>

		<form id="send-reply" action="<?php bp_messages_form_action(); ?>" method="post" class="standard-form">

			<div class="message-box">

				<div class="message-metadata">

					<?php do_action( 'bp_before_message_meta' ); ?>

					<div class="avatar-box">
						<?php bp_loggedin_user_avatar( 'type=full&height=60&width=60' ); ?>
					</div>

					<?php do_action( 'bp_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<div class="message-content">

					<?php do_action( 'bp_before_message_reply_box' ); ?>
					
					<?php _e( 'Send a Reply', 'klein' ); ?>

					<textarea name="content" id="message_content" rows="2" cols="25"></textarea>

					<?php do_action( 'bp_after_message_reply_box' ); ?>

					<div class="submit">
						<input type="submit" name="send" value="<?php esc_attr_e( 'Send Reply', 'klein' ); ?>" id="send_reply_button"/>
					</div>

					<input type="hidden" id="thread_id" name="thread_id" value="<?php bp_the_thread_id(); ?>" />
					<input type="hidden" id="messages_order" name="messages_order" value="<?php bp_thread_messages_order(); ?>" />
					<?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ); ?>

				</div><!-- .message-content -->

			</div><!-- .message-box -->

		</form><!-- #send-reply -->

		<?php do_action( 'bp_after_message_thread_reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bp_after_message_thread_content' ); ?>

</div>