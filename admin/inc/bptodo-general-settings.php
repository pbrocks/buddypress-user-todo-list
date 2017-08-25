<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bptodo;
?>
<form action="" method="POST" id="bptodo-general-settings-form">
	<table class="form-table">
		<tbody>
			<!-- PROFILE MENU LABEL -->
			<tr>
				<th scope="row"><label for="bptodo-profile-menu-label"><?php _e( 'Profile Menu Label', BPTODO_TEXT_DOMAIN );?></label></th>
				<td>
					<input type="text" placeholder="<?php _e( 'Label', BPTODO_TEXT_DOMAIN );?>" name="bptodo_profile_menu_label" value="<?php echo $bptodo->profile_menu_label;?>" class="regular-text" required>
					<p class="description"><?php _e( 'This label will be seen in the profile menu(Please make sure you enter <strong>singular</strong> text).', BPTODO_TEXT_DOMAIN );?></p>
				</td>
			</tr>

			<!-- ALLOW USER TO ADD CATEGORY OF TODO -->
			<tr>
				<th scope="row"><label for="bptodo-allow-user-add-category"><?php _e( 'Allow User To Add Category', BPTODO_TEXT_DOMAIN );?></label></th>
				<td>
					<input type="checkbox" name="bptodo_allow_user_add_category" <?php echo ( $bptodo->allow_user_add_category == 'yes' ) ? 'checked': 'unchecked';?>>
					<p class="description"><?php _e( 'Check this option if you want to allow normal users of the site to have the ability to create the todo category.', BPTODO_TEXT_DOMAIN );?></p>
				</td>
			</tr>
			
			<!-- SEND NOTIFICATION AS DUE DATE REMINDER -->
			<tr>
				<th scope="row"><label for="bptodo-send-notification"><?php _e( 'Send Notification', BPTODO_TEXT_DOMAIN );?></label></th>
				<td>
					<input type="checkbox" name="bptodo_send_notification" <?php echo ( $bptodo->send_notification == 'yes' ) ? 'checked': 'unchecked';?>>
					<p class="description"><?php _e( 'Check this option if you want to send notification to the member as a reminder for his/her task due date.', BPTODO_TEXT_DOMAIN );?></p>
				</td>
			</tr>
			
			<!-- SEND EMAIL AS DUE DATE REMINDER -->
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
</form>