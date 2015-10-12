<?php
	// Populate variables to be used in the form from the result obtained from handle_submission()
	$form_errors 		= (isset($result['errors'])) ? $result['errors'] : "";
	$submission_status 	= (isset($result['success'])) ? $result['success'] : "";
	$final_post_id 		= (isset($current['post_id'])) ? $current['post_id'] : "-1";
?>

<form class="wpfepp-form" method="POST" style="<?php echo ( isset($this->settings['width']) && !empty($this->settings['width']) ) ? ('max-width:'.$this->settings['width'].';') : ''; ?>">

	<?php //Display a general error or success message. ?>
	<div class="wpfepp-message <?php echo ($submission_status)?"success":"error"; ?> <?php echo isset($form_errors['form'])?'display':''; ?>">
		<?php echo (isset($form_errors['form']))?$form_errors['form']:""; ?>
	</div>

	<div class="wpfepp-form-fields">
		<?php //Start traversing through the fields of this form. ?>
		<?php foreach($this->get_fields() as $field_key => $field): ?>

			<?php
				//Put errors for this particular field in $field_errors and current value in $field_current
				$field_errors 	= isset($form_errors[$field_key]) ? $form_errors[$field_key] : "";
				$field_current 	= isset($current[$field_key]) ? ($current[$field_key]) : "";
				$unique_key 	= 'form-'.$this->id.'-'.$field_key;
			?>

			<?php //Start outputting the field HTML if the field is enabled ?>
			<?php if(wpfepp_is_field_supported($field['type'], $this->post_type)):?>
				<?php if($field['enabled']): ?>

				<?php if(isset($field['prefix_text']) && $field['prefix_text']): ?>
					<div class="wpfepp-prefix-text">
						<?php echo $field['prefix_text']; ?>
					</div>
				<?php endif; ?>

				<div class="wpfepp-<?php echo $field_key; ?>-field-container wpfepp-form-field-container" style="<?php echo ( isset($field['width']) && !empty($field['width']) ) ? ('width:'.$field['width'].';') : ''; ?>">
					<label for="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-form-field-label"><?php echo $field['label']; ?></label>
					<div class="wpfepp-form-field-errors"><?php echo $field_errors; ?></div>

					<?php if($field['type'] == 'title'): ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" type="text" value="<?php echo esc_attr($field_current); ?>" <?php echo $this->print_restrictions($field); ?> />
					<?php endif; ?>

					<?php if($field['type'] == 'content'): ?>
						<?php if($field['element'] == 'richtext'): ?>
							<?php $media_buttons = ( isset($field['media_button']) ) ? (boolean)$field['media_button'] : true; ?>
							<?php wp_editor( $field_current, "wpfepp-$unique_key-field", array('wpautop'=>true, 'media_buttons'=> $media_buttons, 'textarea_name'=>$field_key, 'textarea_rows'=>10, 'editor_class'=>"wpfepp-$field_key-field wpfepp-form-field") ); ?>
						<?php else: ?>
							<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-plain-field wpfepp-form-field" name="<?php echo $field_key; ?>"><?php echo esc_textarea($field_current); ?></textarea>
						<?php endif; ?>
						<?php if( !wpfepp_current_user_has($this->settings['no_restrictions']) ): ?>
							<script>
								function wpfepp_set_content_restrictions($){
									<?php if($field['required']): ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('required', 'true');<?php endif; ?>
									<?php if($field["min_words"] && is_numeric($field["min_words"])): ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('minwords', '<?php echo $field["min_words"]; ?>');<?php endif; ?>
									<?php if($field["max_words"] && is_numeric($field["max_words"])): ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('maxwords', '<?php echo $field["max_words"]; ?>');<?php endif; ?>
									<?php if($field["max_links"] && is_numeric($field["max_links"])): ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('maxlinks', '<?php echo $field["max_links"]; ?>');<?php endif; ?>
								}
							</script>
						<?php endif; ?>
					<?php endif; ?>

					<?php if($field['type'] == 'excerpt'): ?>
						<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $this->print_restrictions($field); ?> ><?php echo esc_textarea($field_current); ?></textarea>
					<?php endif; ?>

					<?php if($field['type'] == 'thumbnail'): ?>
						<div class="wpfepp-<?php echo $field_key; ?>-field">
							<div class="wpfepp-<?php echo $field_key; ?>-container"><?php $this->output_thumbnail($field_current); ?></div>
							<a class="wpfepp-<?php echo $field_key; ?>-link" href="#"><?php _e('Select Featured Image', 'wpfepp-plugin'); ?></a>
							<a class="wpfepp-<?php echo $field_key; ?>-close" href="#"><i class="wpfepp-icon-close"></i></a>
							<input type="hidden" value="<?php echo ($field_current)?esc_attr($field_current):"-1"; ?>" name="<?php echo $field_key; ?>" class="wpfepp-<?php echo $field_key; ?>-id wpfepp-form-field" <?php echo $this->print_restrictions($field); ?> />
						</div>
					<?php endif; ?>

					<?php if($field['type'] == 'hierarchical_taxonomy'): ?>
						<?php
							$exclude_terms 	= (isset($field['exclude']) && !empty($field['exclude'])) ? $field['exclude'] : '';
							$include_terms 	= (isset($field['include']) && !empty($field['include'])) ? $field['include'] : '';
							$hide_empty 	= (isset($field['hide_empty'])) ? $field['hide_empty'] : 0;
							$tax_args 		= array( 'hide_empty' => $hide_empty, 'exclude' => $exclude_terms, 'include' => $include_terms, 'parent' => 0);
						?>
						<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-hierarchical-taxonomy-field wpfepp-form-field" name="<?php echo $field_key; ?>[]" <?php echo $this->print_restrictions($field); ?> >
							<?php if(!$field['multiple']): ?><option value=""><?php _e('Select', 'wpfepp-plugin'); ?> ...</option><?php endif; ?>
							<?php $this->hierarchical_taxonomy_options($field_key, $tax_args, $field_current); ?>
						</select>
					<?php endif; ?>

					<?php if($field['type'] == 'non_hierarchical_taxonomy'): ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" type="text" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-non-hierarchical-taxonomy-field wpfepp-form-field" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $this->print_restrictions($field); ?> />
					<?php endif; ?>

					<?php if($field['type'] == 'post_format'): ?>
						<?php $formats = get_theme_support( 'post-formats' ); ?>
						<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>">
							<option value="standard"><?php _e('Standard', 'wpfepp-plugin'); ?></option>
							<?php foreach ($formats[0] as $key => $format): ?>
								<option value="<?php echo $format; ?>" <?php selected($field_current, $format); ?>><?php echo ucfirst($format); ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>

					<?php if($field['type'] == 'custom_field'): ?>
						<?php if($field['element'] == 'input'  || $field['element'] == 'email' || $field['element'] == 'url'): ?>
							<?php $cf_input_type = ($field['element'] == 'input') ? 'text' : $field['element']; ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="<?php echo $cf_input_type; ?>" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $this->print_restrictions($field); ?> />
						<?php elseif($field['element'] == 'textarea'): ?>
							<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $this->print_restrictions($field); ?> ><?php echo esc_textarea($field_current); ?></textarea>
						<?php elseif($field['element'] == 'checkbox'): ?>
							<input type="hidden" name="<?php echo $field_key; ?>" value="0" />
							<input type="checkbox" id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $this->print_restrictions($field); ?> value="1" <?php checked($field_current); ?> />
						<?php elseif($field['element'] == 'select'): ?>
							<?php $field['choices'] = wpfepp_choices($field['choices']); ?>
							<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $this->print_restrictions($field); ?> >
								<option value=""><?php _e('Select', 'wpfepp-plugin'); ?> ...</option>
								<?php foreach ($field['choices'] as $choice): ?>
									<option value="<?php echo esc_attr($choice['key']); ?>"><?php echo $choice['val']; ?></option>
								<?php endforeach; ?>
							</select>
						<?php elseif($field['element'] == 'radio'): ?>
							<?php $field['choices'] = wpfepp_choices($field['choices']); ?>
							<?php foreach ($field['choices'] as $choice): ?>
								<input type="radio" value="<?php echo esc_attr($choice['key']); ?>" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $this->print_restrictions($field); ?> <?php checked($field_current); ?> /> <?php echo $choice['val']; ?><br/>
							<?php endforeach; ?>
						<?php elseif($field['element'] == 'image_url'): ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="url" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $this->print_restrictions($field); ?> />
							<button type="button" class="wpfepp-button wpfepp-image-url-button"><?php _e('Upload/Select', 'wpfepp-plugin'); ?></button>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php else: ?>
					<?php if(isset($field['fallback_value']) && $field['fallback_value']): ?>
						<textarea style="display:none;" name="<?php echo $field_key; ?>"><?php echo $field['fallback_value']; ?></textarea>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

		<?php endforeach; ?>

		<?php
			if($this->settings['captcha_enabled'] && $this->post_status($final_post_id) == 'new'){
				$this->captcha->render();
			}
		?>

		<?php $this->user_defined_fields($current); ?>

		<?php //Now that all the visible fields have been generated, create the hidden fields ?>
		<input class="wpfepp-form-id-field" type="hidden" name="form_id" value="<?php echo $this->id; ?>" />
		<input class="wpfepp-post-id-field" type="hidden" name="post_id" value="<?php echo $final_post_id; ?>" />
		<?php wp_nonce_field( 'wpfepp-form-'.$this->id.'-nonce', '_wpnonce', false, true ); ?>
		<input type="hidden" name="action" value="wpfepp_handle_submission_ajax" />

		<?php //Finally, the submit button ?>
		<button type="submit" class="wpfepp-button wpfepp-submit-button <?php echo (isset($this->settings['button_color'])) ? $this->settings['button_color'] : 'blue'; ?>" name="wpfepp-form-<?php echo $this->id; ?>-submit"><i></i> <?php _e('Submit', 'wpfepp-plugin'); ?></button>
		<?php if( $this->settings['enable_drafts'] && ($this->post_status($final_post_id) == 'new' || $this->post_status($final_post_id) == 'draft') ): ?>
			<button type="submit" class="wpfepp-button wpfepp-save-button cancel" name="wpfepp-form-<?php echo $this->id; ?>-save"><i></i> <?php _e('Save Draft', 'wpfepp-plugin'); ?></button>
		<?php endif; ?>
	</div> <!-- /wpfepp-form-fields -->
</form>