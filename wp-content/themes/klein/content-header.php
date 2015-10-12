<?php
/**
 * Contains header for for contents
 * 
 * @package Klein
 */
?>

<div class="content-heading ">
	<div class="row">
		<div class="col-md-12" id="content-header">
			<div class="row">
				<div class="col-md-12">
					<?php $bread_mg_class = ''; ?>
					<?php if(!function_exists('bcn_display')) { ?>
						<?php $bread_mg_class = 'no-mg-bottom'; ?>
					<?php } ?>
					<h1 class="entry-title <?php echo $bread_mg_class;?>" id="bp-klein-page-title">
						<?php the_title(); ?>
					</h1>
					<!--breadcrumbs-->
					<?php if(function_exists('bcn_display')){ ?>
						<div class="klein-breadcrumbs">
							<?php bcn_display(); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>