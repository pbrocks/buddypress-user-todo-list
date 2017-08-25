<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="bptodo-loggedout-user-panel">
	<p><?php _e( 'You need to be loggedin to view the todo items.' );?></p>
	<?php wp_login_form();?>
</div>