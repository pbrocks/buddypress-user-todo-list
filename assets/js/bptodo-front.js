jQuery(document).ready(function(){
	webshims.setOptions('forms-ext', {types: 'date'});
	webshims.polyfill('forms forms-ext');

	//Export My Tasks
	jQuery(document).on('click', '#export_my_tasks', function(){
		jQuery( '#export_my_tasks' ).val( 'Exporting..' );
		jQuery.post(
			bptodo_ajax_object.ajax_url,
			{
				'action' : 'bptodo_export_my_tasks'
			},
			function( response ) {
				jQuery( '#export_my_tasks' ).val( 'Exported!' );
				tasks = response;
				JSONToCSVConvertor( tasks, "Buddypress - My Tasks List", true );
			},
			"JSON"
		);
	});

	//Add BP Todo Category Show Row
	jQuery(document).on('click', '.add-todo-category', function(){
		jQuery('.add-todo-cat-row').show();
	});

	//Add BP Todo Category Close Row
	jQuery(document).on('click', '#todo-cat-close', function(){
		jQuery('.add-todo-cat-row').hide();
	});

	//Add BP Todo Category
	jQuery(document).on('click', '#add-todo-cat', function(){
		var name = jQuery('#todo-category-name').val();
		jQuery( this ).val('Adding...');
		jQuery.post(
			bptodo_ajax_object.ajax_url,
			{
				'action' : 'bptodo_add_todo_category_front',
				'name' : name,
			},
			function( response ) {
				if( response == 'todo-category-added'){
					var html = '<option value="'+name+'">'+name+'</option>';
					jQuery('#bp_todo_categories').append(html);
					jQuery('.add-todo-cat-row').hide();
				}
			}
		);
	});
});

function JSONToCSVConvertor( JSONData, ReportTitle, ShowLabel ) {
	var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
	var CSV = '';
	CSV += ReportTitle + '\r\n\n';
	if (ShowLabel) {
		var row = "";
		for (var index in arrData[0]) {
			row += index + ',';
		}
		row = row.slice(0, -1);
		CSV += row + '\r\n';
	}

	//1st loop is to extract each row
	for (var i = 0; i < arrData.length; i++) {
		var row = "";
		for (var index in arrData[i]) {
			row += '"' + arrData[i][index] + '",';
		}
		row.slice(0, row.length - 1);
		//add a line break after each row
		CSV += row + '\r\n';
	}

	if (CSV == '') {
		alert("Invalid data");
		return;
	}

	var fileName = "MyTasks_";
	fileName += ReportTitle.replace(/ /g,"_");
	var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
	var link = document.createElement("a");
	link.href = uri;
	link.style = "visibility:hidden";
	link.download = fileName + ".csv";
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}