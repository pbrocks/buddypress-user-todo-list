<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-user-todo-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $bptodo;
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="bptodo-shortcode-1">[bptodo_by_category category="<i>CATEGORY_ID</i>"]</label></th>
			<td>
				<p>
					<?php
						esc_html_e( 'This shortcode will list all the ', 'wb-todo' );
						echo esc_html( $bptodo->profile_menu_label_plural, 'wb-todo' );
						esc_html_e( ' category wise.', 'wb-todo' );
					?>
				</p>
				<p class="description"><?php esc_html_e( 'Arguments accepted:', 'wb-todo' ); ?></p>
				<ol type="1">
					<li>
						<?php
							esc_html_e( 'category : ', 'wb-todo' );
							esc_html_e( 'you need to provide the category id of which the ', 'wb-todo' );
							echo esc_html( $bptodo->profile_menu_label_plural, 'wb-todo' );
							esc_html_e( ' you want to show.', 'wb-todo' );
						?>
					</li>
				</ol>
			</td>
		</tr>
	</tbody>
</table>
