<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly
global $bptodo;
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="bptodo-shortcode"><?php echo '[bptodo_by_category]';?></label></th>
			<td>
				<p><?php _e( 'This shortcode will list all the '.$bptodo->profile_menu_label_plural.' category wise.', BPTODO_TEXT_DOMAIN );?></p>
				<p class="description"><?php _e( 'Arguments accepted:', BPTODO_TEXT_DOMAIN );?></p>
				<ol type="1">
					<li>category : <?php _e( 'you need to provide the category id of which the '.$bptodo->profile_menu_label_plural.' you want to show.', BPTODO_TEXT_DOMAIN );?> </li>
				</ol>
			</td>
		</tr>
	</tbody>
</table>