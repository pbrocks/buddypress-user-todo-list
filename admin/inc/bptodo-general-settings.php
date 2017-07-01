<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bptodo;
// echo '<pre>'; print_r( $bptodo ); die("lkoo");
//Save general settings
if( isset( $_POST['bptodo-save-settings'] ) && wp_verify_nonce( $_POST['bptodo-general-settings-nonce'], 'bptodo' ) ) {

	//Profile menu label
	$settings['profile_menu_label'] = sanitize_text_field( $_POST['bptodo_profile_menu_label'] );
	update_option('user_todo_list_settings', $settings);
	echo '<div class="notice notice-success"><p>Settings Saved.</p></div>';
}
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="bptodo-profile-menu-label"><?php _e( 'Profile Menu Label', BPTODO_TEXT_DOMAIN );?></label></th>
			<td>
				<input type="text" placeholder="<?php _e( 'Label', BPTODO_TEXT_DOMAIN );?>" name="bptodo_profile_menu_label" value="<?php echo $bptodo['profile_menu_label'];?>" class="regular-text" required>
				<p class="description"><?php _e( 'This label will be seen in the profile menu.', BPTODO_TEXT_DOMAIN );?></p>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit">
	<?php wp_nonce_field( 'bptodo', 'bptodo-general-settings-nonce');?>
	<input type="submit" name="bptodo-save-settings" class="button button-primary" value="Save Changes">
</p>