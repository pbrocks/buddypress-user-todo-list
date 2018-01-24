<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bptodo;
$profile_menu_label	 = $bptodo->profile_menu_label;
$profile_menu_slug	 = $bptodo->profile_menu_slug;
$class = "todo-completed";

if ( !empty( $atts[ 'category' ] ) ) {
	$term = get_term_by( 'id', $atts[ 'category' ], 'todo_category' );
	if ( !empty( $term ) ) {
		$args	 = array(
			'post_type'		 => 'bp-todo',
			'numberposts'	 => -1,
			'tax_query'		 => array(
				array(
					'taxonomy'			 => 'todo_category',
					'field'				 => 'id',
					'terms'				 => $atts[ 'category' ],
					'include_children'	 => true
				)
			)
		);
		$todos	 = get_posts( $args );
		if ( !empty( $todos ) ) {
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
			?>
			<div class="bptodo-adming-setting">
				<div class="bptodo-admin-settings-block">
					<div id="bptodo-settings-tbl">

						<!-- PAST TASKS -->
						<?php if ( !empty( $todo_list[ 'past' ] ) ) { ?>
							<div class="bptodo-admin-row">
								<div>
									<button class="bptodo-item"><?php _e( 'PAST', 'wb-todo' ); ?></button>
									<div class="panel">
										<div class="todo-detail">
											<table class="bp-todo-reminder">
												<thead>
													<tr>
														<th></th>
														<th><?php _e( 'Task', 'wb-todo' );?></th>
														<th><?php _e( 'Task Description', 'wb-todo' );?></th>
														<th><?php _e( 'Due Date', 'wb-todo' );?></th>
														<th><?php _e( 'Mark Complete', 'wb-todo' );?></th>
													</tr>
												</thead>
												<tbody>
													<?php $count = 1; ?>
													<?php foreach ( $todo_list[ 'past' ] as $tid ) { ?>
														<?php
														$todo			 = get_post( $tid );
														$todo_title		 = $todo->post_title;

														$todo_edit_url	 = bp_core_get_userlink( get_current_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

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
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $count++;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo_title;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo->post_content;?></td>
															<td class="<?php
															echo $due_date_td_class;
															if ( $todo_status == 'complete' )
																echo $class;
															?>"><?php echo $due_date_str; ?></td>
															<td class="bp-to-do-actions">
																<ul>
																	<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-times"></i></a></li>
																	<?php if ( $todo_status !== 'complete' ) { ?>
																		<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-edit"></i></a></li>
																		<li id="bptodo-complete-li-<?php echo $tid;?>"><a href="javacript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-check"></i></a></li>
																	<?php } else { ?>
																		<li><a href="javacript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Undo Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-undo"></i></a></li>
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
									<button class="bptodo-item"><?php _e( 'TODAY', 'wb-todo' );?></button>
									<div class="panel">
										<div class="todo-detail">
											<table class="bp-todo-reminder">
												<thead>
													<tr>
														<th></th>
														<th><?php _e( 'Task', 'wb-todo' );?></th>
														<th><?php _e( 'Task Description', 'wb-todo' );?></th>
														<th><?php _e( 'Due Date', 'wb-todo' );?></th>
														<th><?php _e( 'Mark Complete', 'wb-todo' );?></th>
													</tr>
												</thead>
												<tbody>
													<?php $count = 1; ?>
													<?php foreach ( $todo_list[ 'today' ] as $tid ) { ?>
														<?php
														$todo			 = get_post( $tid );
														$todo_title		 = $todo->post_title;

														$todo_edit_url	 = bp_core_get_userlink( get_current_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

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
														<tr id="bptodo-row-<?php echo $tid; ?>">
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $count++;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo_title;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo->post_content;?></td>
															<td class="<?php
															echo $due_date_td_class;
															if ( $todo_status == 'complete' )
																echo $class;
															?>"><?php echo $due_date_str; ?></td>
															<td class="bp-to-do-actions">
																<ul>
																	<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-times"></i></a></li>
																	<?php if ( $todo_status !== 'complete' ) { ?>
																		<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-edit"></i></a></li>
																		<li id="bptodo-complete-li-<?php echo $tid; ?>"><a href="javacript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-check"></i></a></li>
																	<?php } else { ?>
																		<li><a href="javacript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Undo Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-undo"></i></a></li>
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
									<button class="bptodo-item"><?php _e( 'TOMORROW', 'wb-todo' ); ?></button>
									<div class="panel">
										<div class="todo-detail">
											<table class="bp-todo-reminder">
												<thead>
													<tr>
														<th></th>
														<th><?php _e( 'Task', 'wb-todo' ); ?></th>
														<th><?php _e( 'Task Description', 'wb-todo' ); ?></th>
														<th><?php _e( 'Due Date', 'wb-todo' ); ?></th>
														<th><?php _e( 'Mark Complete', 'wb-todo' ); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $count = 1; ?>
													<?php foreach ( $todo_list[ 'tomorrow' ] as $tid ) { ?>
														<?php
														$todo			 = get_post( $tid );
														$todo_title		 = $todo->post_title;

														$todo_edit_url	 = bp_core_get_userlink( get_current_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

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
														<tr id="bptodo-row-<?php echo $tid; ?>">
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $count++;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo_title;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo->post_content;?></td>
															<td class="<?php
															echo $due_date_td_class;
															if ( $todo_status == 'complete' )
																echo $class;
															?>"><?php echo $due_date_str; ?></td>
															<td class="bp-to-do-actions">
																<ul>
																	<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-times"></i></a></li>
																	<?php if ( $todo_status !== 'complete' ) { ?>
																		<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-edit"></i></a></li>
																		<li id="bptodo-complete-li-<?php echo $tid; ?>"><a href="javacript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-check"></i></a></li>
																	<?php } else { ?>
																		<li><a href="javacript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Undo Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-undo"></i></a></li>
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
									<button class="bptodo-item"><?php _e( 'SOMEDAY', 'wb-todo' ); ?></button>
									<div class="panel">
										<div class="todo-detail">
											<table class="bp-todo-reminder">
												<thead>
													<tr>
														<th><?php _e( 'Sr. No.', 'wb-todo' );?></th>
														<th><?php _e( 'Task', 'wb-todo' ); ?></th>
														<th><?php _e( 'Task Description', 'wb-todo' ); ?></th>
														<th><?php _e( 'Due Date', 'wb-todo' ); ?></th>
														<th><?php _e( 'Mark Complete', 'wb-todo' ); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $count = 1; ?>
													<?php foreach ( $todo_list[ 'future' ] as $tid ) { ?>
														<?php
														$todo			 = get_post( $tid );
														$todo_title		 = $todo->post_title;

														$todo_edit_url	 = bp_core_get_userlink( get_current_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

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
														<tr id="bptodo-row-<?php echo $tid; ?>">
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $count++;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo_title;?></td>
															<td class="<?php if ( $todo_status == 'complete' ) echo $class; ?>"><?php echo $todo->post_content;?></td>
															<td class="<?php
															echo $due_date_td_class;
															if ( $todo_status == 'complete' )
																echo $class;
															?>"><?php echo $due_date_str; ?></td>
															<td class="bp-to-do-actions">
																<ul>
																	<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo $tid;?>" title="<?php _e( 'Remove: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-times"></i></a></li>
																	<?php if ( $todo_status !== 'complete' ) { ?>
																		<li><a href="<?php echo $todo_edit_url;?>" title="<?php _e( 'Edit: ' . $todo_title, 'wb-todo' );?>"><i class="fa fa-edit"></i></a></li>
																		<li id="bptodo-complete-li-<?php echo $tid; ?>"><a href="javacript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-check"></i></a></li>
																	<?php } else { ?>
																		<li><a href="javacript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo $tid; ?>" title="<?php _e( 'Undo Complete: ' . $todo_title, 'wb-todo' ); ?>"><i class="fa fa-undo"></i></a></li>
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
		} else {
			?>
			<div id="message" class="info">
				<p><?php _e( 'There are no todos in this category.', 'wb-todo' ); ?></p>
			</div>
			<?php
		}
	} else {
		?>
		<div id="message" class="info">
			<p><?php _e( 'Please provide a valid category ID.', 'wb-todo' ); ?></p>
		</div>
		<?php
	}
} else {
	?>
	<div id="message" class="info">
		<p><?php _e( 'Please provide any category ID.', 'wb-todo' ); ?></p>
	</div>
	<?php
}
?>