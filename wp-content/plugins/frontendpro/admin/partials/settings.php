<?php 
	$form 			= $this->db->get($_GET['form']);
	$form_post_type = $form['post_type'];
	$form_fields 	= $form['fields'];
	$form_settings 	= $form['settings'];
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
	<h2><?php echo stripslashes($form['name']); ?> <?php _e('Settings', 'wpfepp-plugin'); ?> <img id="wpfepp-loading" src="<?php echo plugins_url('static/img/loading.gif', dirname(dirname(__FILE__))); ?>" /></h2>
	<p><code>[wpfepp_submission_form form="<?php echo $_GET['form']; ?>"]</code></p>
	<p><code>[wpfepp_post_table form="<?php echo $_GET['form']; ?>"]</code></p>

	<?php $this->tabs->display(); ?>
</div>