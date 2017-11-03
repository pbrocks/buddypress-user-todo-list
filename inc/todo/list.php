<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly
global $bptodo;
$profile_menu_slug = $bptodo->profile_menu_slug;
$profile_menu_label = $bptodo->profile_menu_label;

//Save todo items
if ( isset( $_POST[ 'todo_create' ] ) && wp_verify_nonce( $_POST[ 'save_new_todo_data_nonce' ], 'wp-bp-todo' ) ) {

	$cat		 = sanitize_text_field( $_POST[ 'todo_cat' ] );
	$title		 = sanitize_text_field( $_POST[ 'todo_title' ] );
	$due_date	 = sanitize_text_field( $_POST[ 'todo_due_date' ] );
	$summary	 = sanitize_text_field( $_POST[ 'todo_summary' ] );

	$taxonomy	 = 'todo_category';
	$args		 = array(
		'post_type'		 => 'bp-todo',
		'post_status'	 => 'publish',
		'post_title'	 => $title,
		'post_content'	 => $summary,
		'post_author'	 => get_current_user_id(),
	);
	$post_id	 = wp_insert_post( $args );

	update_post_meta( $post_id, 'todo_status', 'incomplete' );
	update_post_meta( $post_id, 'todo_due_date', $due_date );
	update_post_meta( $post_id, 'todo_last_day_mail_sent', 'no' );
	update_post_meta( $post_id, 'todo_last_day_notification_sent', 'no' );

	wp_set_object_terms( $post_id, $cat, $taxonomy );
}
//Update todo items
if ( isset( $_POST[ 'todo_update' ] ) && wp_verify_nonce( $_POST[ 'save_update_todo_data_nonce' ], 'wp-bp-todo' ) ) {
	$cat = sanitize_text_field( $_POST[ 'todo_cat' ] );

	$title		= 	sanitize_text_field( $_POST[ 'todo_title' ] );
	$summary	= 	sanitize_text_field( $_POST[ 'todo_summary' ] );
	$due_date	= 	sanitize_text_field( $_POST[ 'todo_due_date' ] );
	$todo_id 	=	sanitize_text_field( $_POST['hidden_todo_id'] );

	$taxonomy	 = 'todo_category';
	$args		 = array(
		'ID'			 => $todo_id,
		'post_type'		 => 'bp-todo',
		'post_status'	 => 'publish',
		'post_title'	 => $title,
		'post_content'	 => $summary,
		'post_author'	 => get_current_user_id(),
	);
	$post_id	 = wp_update_post( $args );

	update_post_meta( $post_id, 'todo_status', 'incomplete' );
	update_post_meta( $post_id, 'todo_due_date', $due_date );

	wp_set_object_terms( $post_id, $cat, $taxonomy );
}
$class = "todo-completed";

//List of Todo Items
$args	 = array(
	'post_type'		 => 'bp-todo',
	'post_status'	 => 'publish',
	'author'		 => get_current_user_id(),
	'posts_per_page' => -1,
);
$todos	 = get_posts( $args );

$todo_list = array();
foreach ( $todos as $todo ) {
	$curr_date	 = date_create( date( 'Y-m-d' ) );
	$due_date	 = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
	$diff		 = date_diff( $curr_date, $due_date );
	$diff_days	 = $diff->format( "%R%a" );

	if ( $diff_days < 0 ) {
		$todo_list[ 'past' ][] = $todo->ID;
	} elseif ( $diff_days == 0 ) {
		$todo_list[ 'today' ][] = $todo->ID;
	} elseif ( $diff_days == 1 ) {
		$todo_list[ 'tomorrow' ][] = $todo->ID;
	} else {
		$todo_list[ 'future' ][] = $todo->ID;
	}
}

if ( empty( $todos ) ) {
	?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, no ' . $profile_menu_slug . ' found.', BPTODO_TEXT_DOMAIN );?></p>
	</div>
<?php } else { ?>

	<!-- Show the successful message when todo is added -->
	<?php if( isset( $_POST['todo_create'] ) ) {?>
		<div id="message" class="info">
			<p><?php _e( $profile_menu_label.' added successfully !', BPTODO_TEXT_DOMAIN );?></p>
		</div>
	<?php }?>

	<!-- Show the successful message when todo is updated -->
	<?php if( isset( $_POST['todo_update'] ) ) {?>
		<div id="message" class="info">
			<p><?php _e( $profile_menu_label.' updated successfully !', BPTODO_TEXT_DOMAIN );?></p>
		</div>
	<?php }?>

	<div class="bptodo-adming-setting">
		<div class="bptodo-admin-settings-block">
			<div id="bptodo-settings-tbl">

				<!-- PAST TASKS -->
				<?php if ( !empty( $todo_list[ 'past' ] ) ) { ?>
					<div class="bptodo-admin-row">
						<div>
							<button class="bptodo-item"><?php _e( 'PAST', BPTODO_TEXT_DOMAIN );?></button>
							<div class="panel">
								<div class="todo-detail">
									<table class="bp-todo-reminder">
										<thead>
											<tr>
												<th><?php _e( 'Sr. No.', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Task', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Due Date', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Actions', BPTODO_TEXT_DOMAIN );?></th>
											</tr>
										</thead>
										<tbody>
											<?php $count = 1;?>
											<?php foreach ( $todo_list[ 'past' ] as $tid ) { ?>
												<?php
												$todo			 = get_post( $tid );
												$todo_title		 = $todo->post_title;
												$todo_edit_url	 = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

												$todo_status		 = get_post_meta( $todo->ID, 'todo_status', true );
												$due_date_str		 = $due_date_td_class	 = '';
												$curr_date			 = date_create( date( 'Y-m-d' ) );
												$due_date			 = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
												$diff				 = date_diff( $curr_date, $due_date );
												$diff_days			 = $diff->format( "%R%a" );
												if ( $diff_days < 0 ) {
													$due_date_str		 = 'Expired ' . abs( $diff_days ) . ' days ago!';
													$due_date_td_class	 = 'bptodo-expired';
												} else if ( $diff_days == 0 ) {
													$due_date_str		 = 'Today is the last day to complete. Hurry Up!';
													$due_date_td_class	 = 'bptodo-expires-today';
												} else {
													$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
												}
												if ( $todo_status == 'complete' ) {
													$due_date_str		 = 'Completed!';
													$due_date_td_class	 = '';
												}
												?>
												<tr id="bptodo-row-<?php echo $tid;?>">
													<td class="bptodo-sr-no <?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $count++;?></td>
													<td class="<?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $todo_title;?></td>
													<td class="<?php
													echo $due_date_td_class;
													if ( $todo_status == 'complete' )
														echo $class;
													?>"><?php echo $due_date_str;?></td>
													<td class="bp-to-do-actions">
														<ul>
															<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-times"></i></a></li>
															<?php if ( $todo_status !== 'complete' ) { ?>
																<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-edit"></i></a></li>
																<li id="bptodo-complete-li-<?php echo $tid;?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-check"></i></a></li>
															<?php } else { ?>
																<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Undo Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-undo"></i></a></li>
															<?php } ?>
														</ul>	
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>

							</div>
						</div>
					</div>
				<?php } ?>

				<!-- TASKS FOR TODAY -->
				<?php if ( !empty( $todo_list[ 'today' ] ) ) { ?>
					<div class="bptodo-admin-row">
						<div>
							<button class="bptodo-item"><?php _e( 'TODAY', BPTODO_TEXT_DOMAIN );?></button>
							<div class="panel">
								<div class="todo-detail">
									<table class="bp-todo-reminder">
										<thead>
											<tr>
												<th><?php _e( 'Sr. No.', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Task', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Due Date', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Actions', BPTODO_TEXT_DOMAIN );?></th>
											</tr>
										</thead>
										<tbody>
											<?php $count = 1;?>
											<?php foreach ( $todo_list[ 'today' ] as $tid ) { ?>
												<?php
												$todo			 = get_post( $tid );
												$todo_title		 = $todo->post_title;
												$todo_edit_url	 = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

												$todo_status		 = get_post_meta( $todo->ID, 'todo_status', true );
												$due_date_str		 = $due_date_td_class	 = '';
												$curr_date			 = date_create( date( 'Y-m-d' ) );
												$due_date			 = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
												$diff				 = date_diff( $curr_date, $due_date );
												$diff_days			 = $diff->format( "%R%a" );
												if ( $diff_days < 0 ) {
													$due_date_str		 = 'Expired ' . abs( $diff_days ) . ' days ago!';
													$due_date_td_class	 = 'bptodo-expired';
												} else if ( $diff_days == 0 ) {
													$due_date_str		 = 'Today is the last day to complete. Hurry Up!';
													$due_date_td_class	 = 'bptodo-expires-today';
												} else {
													$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
												}
												if ( $todo_status == 'complete' ) {
													$due_date_str		 = 'Completed!';
													$due_date_td_class	 = '';
												}
												?>
												<tr id="bptodo-row-<?php echo $tid;?>">
													<td class="bptodo-sr-no <?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $count++;?></td>
													<td class="<?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $todo_title;?></td>
													<td class="<?php
													echo $due_date_td_class;
													if ( $todo_status == 'complete' )
														echo $class;
													?>"><?php echo $due_date_str;?></td>
													<td class="bp-to-do-actions">
														<ul>
															<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-times"></i></a></li>
															<?php if ( $todo_status !== 'complete' ) { ?>
																<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-edit"></i></a></li>
																<li id="bptodo-complete-li-<?php echo $tid;?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-check"></i></a></li>
															<?php } else { ?>
																<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Undo Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-undo"></i></a></li>
															<?php } ?>
														</ul>	
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- TASKS FOR TOMORROW -->
				<?php if ( !empty( $todo_list[ 'tomorrow' ] ) ) { ?>
					<div class="bptodo-admin-row">
						<div>
							<button class="bptodo-item"><?php _e( 'TOMORROW', BPTODO_TEXT_DOMAIN );?></button>
							<div class="panel">
								<div class="todo-detail">
									<table class="bp-todo-reminder">
										<thead>
											<tr>
												<th><?php _e( 'Sr. No.', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Task', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Due Date', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Actions', BPTODO_TEXT_DOMAIN );?></th>
											</tr>
										</thead>
										<tbody>
											<?php $count = 1;?>
											<?php foreach ( $todo_list[ 'tomorrow' ] as $tid ) { ?>
												<?php
												$todo			 = get_post( $tid );
												$todo_title		 = $todo->post_title;
												$todo_edit_url	 = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;
												
												$todo_status		 = get_post_meta( $todo->ID, 'todo_status', true );
												$due_date_str		 = $due_date_td_class	 = '';
												$curr_date			 = date_create( date( 'Y-m-d' ) );
												$due_date			 = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
												$diff				 = date_diff( $curr_date, $due_date );
												$diff_days			 = $diff->format( "%R%a" );
												if ( $diff_days < 0 ) {
													$due_date_str		 = 'Expired ' . abs( $diff_days ) . ' days ago!';
													$due_date_td_class	 = 'bptodo-expired';
												} else if ( $diff_days == 0 ) {
													$due_date_str		 = 'Today is the last day to complete. Hurry Up!';
													$due_date_td_class	 = 'bptodo-expires-today';
												} else {
													$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
												}
												if ( $todo_status == 'complete' ) {
													$due_date_str		 = 'Completed!';
													$due_date_td_class	 = '';
												}
												?>
												<tr id="bptodo-row-<?php echo $tid;?>">
													<td class="bptodo-sr-no <?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $count++;?></td>
													<td class="<?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $todo_title;?></td>
													<td class="<?php
													echo $due_date_td_class;
													if ( $todo_status == 'complete' )
														echo $class;
													?>"><?php echo $due_date_str;?></td>
													<td class="bp-to-do-actions">
														<ul>
															<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-times"></i></a></li>
															<?php if ( $todo_status !== 'complete' ) { ?>
																<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-edit"></i></a></li>
																<li id="bptodo-complete-li-<?php echo $tid;?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-check"></i></a></li>
															<?php } else { ?>
																<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Undo Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-undo"></i></a></li>
															<?php } ?>
														</ul>	
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- TASKS FOR SOMEDAY -->
				<?php if ( !empty( $todo_list[ 'future' ] ) ) { ?>
					<div class="bptodo-admin-row">
						<div>
							<button class="bptodo-item"><?php _e( 'SOMEDAY', BPTODO_TEXT_DOMAIN );?></button>
							<div class="panel">
								<div class="todo-detail">
									<table class="bp-todo-reminder">
										<thead>
											<tr>
												<th><?php _e( 'Sr. No.', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Task', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Due Date', BPTODO_TEXT_DOMAIN );?></th>
												<th><?php _e( 'Actions', BPTODO_TEXT_DOMAIN );?></th>
											</tr>
										</thead>
										<tbody>
											<?php $count = 1;?>
											<?php foreach ( $todo_list[ 'future' ] as $tid ) { ?>
												<?php
												$todo			 = get_post( $tid );
												$todo_title		 = $todo->post_title;
												$todo_edit_url	 = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

												$todo_status		 = get_post_meta( $todo->ID, 'todo_status', true );
												$due_date_str		 = $due_date_td_class	 = '';
												$curr_date			 = date_create( date( 'Y-m-d' ) );
												$due_date			 = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
												$diff				 = date_diff( $curr_date, $due_date );
												$diff_days			 = $diff->format( "%R%a" );
												if ( $diff_days < 0 ) {
													$due_date_str		 = 'Expired ' . abs( $diff_days ) . ' days ago!';
													$due_date_td_class	 = 'bptodo-expired';
												} else if ( $diff_days == 0 ) {
													$due_date_str		 = 'Today is the last day to complete. Hurry Up!';
													$due_date_td_class	 = 'bptodo-expires-today';
												} else {
													$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
												}
												if ( $todo_status == 'complete' ) {
													$due_date_str		 = 'Completed!';
													$due_date_td_class	 = '';
												}
												?>
												<tr id="bptodo-row-<?php echo $tid;?>">
													<td class="bptodo-sr-no <?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $count++;?></td>
													<td class="<?php if ( $todo_status == 'complete' ) echo $class;?>"><?php echo $todo_title;?></td>
													<td class="<?php
													echo $due_date_td_class;
													if ( $todo_status == 'complete' )
														echo $class;
													?>"><?php echo $due_date_str;?></td>
													<td class="bp-to-do-actions">
														<ul>
															<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-times"></i></a></li>
															<?php if ( $todo_status !== 'complete' ) { ?>
																<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-edit"></i></a></li>
																<li id="bptodo-complete-li-<?php echo $tid;?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-check"></i></a></li>
															<?php } else { ?>
																<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Undo Complete: ' . $todo_title, BPTODO_TEXT_DOMAIN );?>"><i class="fa fa-undo"></i></a></li>
																	<?php } ?>
														</ul>	
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}?>