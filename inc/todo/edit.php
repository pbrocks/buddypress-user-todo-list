<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

$todo_id = $_GET['args'];

//Save todo items
if (isset($_POST['todo_update'])) {
	$cat = $_POST['todo_cat'];
	$title = $_POST['todo_title'];
	$summary = $_POST['todo_summary'];
	$due_date = $_POST['todo_due_date'];
	$taxonomy = 'todo_category';
	$args = array(
		'ID' => $_GET['args'],
		'post_type' => 'bp-todo',
		'post_status' => 'publish',
		'post_title' => $_POST['todo_title'],
		'post_content' => $_POST['todo_summary'],
		'post_author' => get_current_user_id(),
	);
	$post_id = wp_update_post($args);

	update_post_meta($post_id, 'todo_status', 'incomplete');
	update_post_meta($post_id, 'todo_due_date', $due_date);

	wp_set_object_terms($post_id, $cat, $taxonomy);
}

$todo_cats = get_terms('todo_category', 'orderby=name&hide_empty=0');
$todo = get_post($todo_id);
$todo_cat = wp_get_object_terms($todo_id, 'todo_category');
$todo_cat_id = 0;
if (!empty($todo_cat) && is_array($todo_cat))
	$todo_cat_id = $todo_cat[0]->term_id;
$todo_due_date = get_post_meta($todo_id, 'todo_due_date', true);
?>
<form action="" method="post">
	<table class="add-todo-block">
		<tr>
			<td width="20%">
				Todo Category
			</td>
			<td width="80%">
				<select name="todo_cat" id="bp_todo_categories" required>
					<option value="">--Select--</option>
					<?php if (isset($todo_cats)) {?>
						<?php foreach ($todo_cats as $todo_cat) {?>
							<option <?php if ($todo_cat_id == $todo_cat->term_id) echo 'selected="selected"'; ?> value="<?php echo $todo_cat->name; ?>">
								<?php echo $todo_cat->name; ?>
							</option>
						<?php }?>
					<?php }?>
				</select>
				<a href="javascript:void(0);" class="add-todo-category">
					<img src="<?php echo BPTODO_PLUGIN_URL.'assets/images/add.png'; ?>">
				</a>
			</td>
		</tr>

		<!-- Add Todo Category -->
		<tr class="add-todo-cat-row">
			<td>
				<input type="text" id="todo-category-name" placeholder="Todo category">
			</td>
			<td>
				<input type="button" id="add-todo-cat" value="Add">
				<input type="button" value="Close" id="todo-cat-close">
			</td>
		</tr>

		<tr>
			<td width="20%">
				Todo Title
			</td>
			<td width="80%">
				<input value="<?php echo $todo->post_title; ?>" type="text" placeholder="Todo Title" name="todo_title" required>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Todo Summary
			</td>
			<td width="80%">
				<textarea placeholder="Todo Summary" name="todo_summary"><?php echo $todo->post_content; ?></textarea>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Todo Due Date
			</td>
			<td width="80%">
				<input type="date" name="todo_due_date" value="<?php echo $todo_due_date; ?>" required>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="80%">
				<input type="submit" name="todo_update" value="Update Todo">
			</td>
		</tr>
	</table>
</form>