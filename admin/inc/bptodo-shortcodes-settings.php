<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly
global $bptodo;
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="bptodo-shortcode-1">[bptodo_by_category category="<i>CATEGORY_ID</i>"]</label></th>
			<td>
				<p><?php _e( 'This shortcode will list all the ' . $bptodo->profile_menu_label_plural . ' category wise.', 'wb-todo' ); ?></p>
				<p class="description"><?php _e( 'Arguments accepted:', 'wb-todo' ); ?></p>
				<ol type="1">
					<li>category : <?php _e( 'you need to provide the category id of which the ' . $bptodo->profile_menu_label_plural . ' you want to show.', 'wb-todo' ); ?> </li>
				</ol>
			</td>
		</tr>
	</tbody>
</table>
