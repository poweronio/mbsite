<?php
/**
 * Contains location list page template.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $table_prefix WordPress Database Table prefix.
 */
global $wpdb, $table_prefix;

$table_prefix = $wpdb->prefix;
$location_search = ( isset( $_REQUEST['location_search_submit'] ) ) ? true : false;
?>
<div class="gd-content-heading">
<?php
if ( isset($_REQUEST['merge_field'] ) && $_REQUEST['merge_field'] == 'mergeid' && !$location_search ) {
	include_once( 'geodir_merge.php' );
} else if ( isset( $_REQUEST['city_hood'] ) && $_REQUEST['city_hood'] == 'hoodlist' && !$location_search ) {
	include_once( 'geodir_hood_list.php' );
} else {
	$location_items = geodir_manage_location_get_list();
?>
	<h3><?php echo GD_MANAGE_LOCATION; ?></h3>
	<input type="hidden" name="merge_field" value="mergeid" />
	<input type="hidden" name="action" value="merge_location_cities" />
	<?php if ( !empty( $location_items['pagination_top'] ) || !empty( $location_items['filter_box'] ) ) { ?>
	<br class="clear" />
	<div class="tablenav top">
		<div class="alignleft actions bulkactions"><?php if ( !empty( $location_items['filter_box'] ) ) { echo $location_items['filter_box']; } ?></div>
		<?php if ( !empty( $location_items['pagination_top'] ) ) { echo $location_items['pagination_top']; } ?>
	</div><br class="clear" />
	<?php } ?>
	<table style=" width:100%" cellpadding="5" class="widefat post fixed" id="geodir_location-form-merge">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" id="location_selectall" style="margin-left:0;"></th>
				<th width="102" align="left"><strong><?php echo GD_LOCATION; ?></strong></th>
				<th width="102" align="left"><strong><?php echo GD_CITY_HOOD; ?></strong></th>
				<!--<th width="102" align="left"><strong><?php echo GD_LOCATION_COUNTRY; ?></strong></th>-->
				<th width="130" align="left"><strong><?php echo GD_LOCATION_LATITUDE; ?></strong></th>
				<th width="130" align="left"><strong><?php echo GD_LOCATION_LONGITUDE; ?></strong></th>
				<th width="55" align="left"><strong><?php echo GD_LOCATION_DEFAULT_CITY; ?></strong></th>
				<th width="50" align="left"><strong><?php echo GD_LOCATION_ACTION; ?></strong></th>
			</tr>
<?php
	if ( $location_items && isset( $location_items['items'] ) && !empty( $location_items['items'] ) ) {
		$locationinfo = $location_items['items'];
		foreach ( $locationinfo as $locationinfoObj ) {
			$list_location_id = $locationinfoObj->location_id;
			
			$hood_count = geodir_count_hood_by_location( $list_location_id );
			
			$nonce = wp_create_nonce( 'location_action_' . $list_location_id );
			?>
			<tr>
				<td><input type="checkbox" class="merge_case" name="mergeid[]" id="mergevalue" value="<?php echo $list_location_id; ?>"></td>
				<td><?php echo $locationinfoObj->city;?>, <?php echo $locationinfoObj->region;?>, <?php echo __( $locationinfoObj->country, GEODIRECTORY_TEXTDOMAIN );?></td>
				<td><label style="float:left;">(<?php echo $hood_count; ?>)</label> <a style="margin-top:2px; margin-left:5px; float:left;" href="<?php echo admin_url().'admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&city_hood=hoodlist&id='.$list_location_id;?>"><img src="<?php echo plugins_url('',__FILE__); ?>/images/plus-white-icon.png" alt="<?php echo GD_CITY_ADD_HOOD; ?>" title="<?php echo GD_CITY_ADD_HOOD; ?>"/></a></td>
				<!--<td><?php echo $locationinfoObj->country;?></td>-->
				<td><?php echo $locationinfoObj->city_latitude;?></td>
				<td><?php echo $locationinfoObj->city_longitude;?></td>
				<td><input type="radio" value="<?php echo $list_location_id; ?>" name="default_city" id="gd_loc_default" <?php if($locationinfoObj->is_default == '1'){ echo 'checked="checked"';} ?> onclick="geodir_set_location_default(this.value, '<?php echo $nonce;?>')" /></td>
				<td>
					<a href="<?php echo admin_url().'admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_addedit&id='.$list_location_id;?>"><img src="<?php echo plugins_url('',__FILE__); ?>/images/edit.png" alt="<?php echo GD_LOCATION_EDIT; ?>" title="<?php echo GD_LOCATION_EDIT; ?>"/></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php if ( !$locationinfoObj->is_default == '1' ) { ?><a href="<?php echo admin_url().'admin-ajax.php?action=geodir_locationajax_action&location_ajax_action=delete&id='.$list_location_id.'&_wpnonce='.$nonce; ?>" onClick="return confirm('<?php _e( 'Are you sure want to delete this location?', GEODIRLOCATION_TEXTDOMAIN );?>');"><img src="<?php echo plugins_url('',__FILE__); ?>/images/delete.png" alt="<?php echo GD_LOCATION_DELETE; ?>" title="<?php echo GD_LOCATION_DELETE; ?>"/></a><?php } ?>
				</td>
			</tr>
<?php
		}
	}
?>
		</thead>
	</table>
	<?php if ( !empty( $location_items['pagination'] ) ) { ?>
	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions"></div>
		<?php echo $location_items['pagination']; ?>
		<br class="clear" />
	</div>
	<?php } ?>
	<span style="padding:10px; display:block;"><b><?php echo GD_LOCATION_NOTE; ?></b> <?php echo MSG_LOCATION_SELECT_CITY; ?></span>
	<input type="submit" value="<?php echo GD_LOCATION_BTN_MERGE; ?>" class="button-primary" onclick="return geodir_location_merge_ids()" style=" margin:0 10px 10px;" />&nbsp;&nbsp;<input type="button" value="<?php _e( 'Delete Selected', GEODIRLOCATION_TEXTDOMAIN ); ?>" class="button-primary" onclick="return geodir_location_bulk_delete()" style=" margin:0 10px 10px;" />
<?php } ?>
</div>