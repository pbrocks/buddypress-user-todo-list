<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bptodo;
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="bptodo-profile-menu-label"><?php _e( 'Profile Menu Label', BPTODO_TEXT_DOMAIN );?></label></th>
			<td>
				<input type="text" placeholder="<?php _e( 'Label', BPTODO_TEXT_DOMAIN );?>" name="bptodo_profile_menu_label" value="<?php echo $bptodo->profile_menu_label;?>" class="regular-text" required>
				<p class="description"><?php _e( 'This label will be seen in the profile menu.', BPTODO_TEXT_DOMAIN );?></p>
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="bptodo-send-notification"><?php _e( 'Send Notification', BPTODO_TEXT_DOMAIN );?></label></th>
			<td>
				<input type="checkbox" name="bptodo_send_notification" <?php echo ( $bptodo->send_notification == 'yes' ) ? 'checked': 'unchecked';?>>
				<p class="description"><?php _e( 'Check this option if you want to send notification to the member as a reminder for his/her task due date.', BPTODO_TEXT_DOMAIN );?></p>
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="bptodo-send-mail"><?php _e( 'Send Mail', BPTODO_TEXT_DOMAIN );?></label></th>
			<td>
				<input type="checkbox" name="bptodo_send_mail" <?php echo ( $bptodo->send_mail == 'yes' ) ? 'checked': 'unchecked';?>>
				<p class="description"><?php _e( 'Check this option if you want to send mail to the member as a reminder for his/her task due date.', BPTODO_TEXT_DOMAIN );?></p>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit">
	<?php wp_nonce_field( 'bptodo', 'bptodo-general-settings-nonce');?>
	<input type="submit" name="bptodo-save-settings" class="button button-primary" value="Save Changes">
</p>