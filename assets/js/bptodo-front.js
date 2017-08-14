jQuery( document ).ready( function () {

    var acc = document.getElementsByClassName( "bptodo-item" );
    var i;
    for ( i = 0; i < acc.length; i++ ) {
        if ( i == 0 ) {
            acc[i].classList.toggle( "active" );
            var panel = acc[i].nextElementSibling;
            if ( panel.style.maxHeight ) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        }
        acc[i].onclick = function () {
            this.classList.toggle( "active" );
            var panel = this.nextElementSibling;
            if ( panel.style.maxHeight ) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        }
    }

    jQuery( document ).on( 'click', '.bptodo-accordion', function () {
        return false;
    } );

    //Datepicker
    jQuery( '.todo_due_date' ).datepicker( { dateFormat: 'yy-mm-dd', minDate: 0 } );

    //Export My Tasks
    jQuery( document ).on( 'click', '#export_my_tasks', function () {
        jQuery( '#export_my_tasks' ).html( '<i class="fa fa-refresh fa-spin"></i>' );
        jQuery.post(
            ajaxurl,
            {
                'action': 'bptodo_export_my_tasks'
            },
            function ( response ) {
                jQuery( '#export_my_tasks' ).html( '<i class="fa fa-download" aria-hidden="true"></i>' );
                tasks = response;
                JSONToCSVConvertor( tasks, "Buddypress - My Tasks List", true );
            },
            "JSON"
            );
    } );

    //Add BP Todo Category Show Row
    jQuery( document ).on( 'click', '.add-todo-category', function () {
        jQuery( '.add-todo-cat-row' ).slideToggle( 'slow' );
    } );

    //Add BP Todo Category
    jQuery( document ).on( 'click', '#add-todo-cat', function () {
        var name = jQuery( '#todo-category-name' ).val();
        if ( name == '' ) {
            jQuery( '#todo-category-name' ).addClass( 'bptodo-add-cat-empty' ).attr( 'placeholder', 'Category name is required.' );
        } else {
            jQuery( this ).val( 'Adding...' );
            jQuery.post(
                ajaxurl,
                {
                    'action': 'bptodo_add_todo_category_front',
                    'name': name,
                },
                function ( response ) {
                    if ( response == 'todo-category-added' ) {
                        var html = '<option value="' + name + '">' + name + '</option>';
                        jQuery( '#bp_todo_categories' ).append( html );
                        jQuery( '.add-todo-cat-row' ).hide();
                    }
                }
            );
        }
    } );

    //Remove a todo
    jQuery( document ).on( 'click', '.bptodo-remove-todo', function () {
        if ( confirm( 'Are you sure?' ) ) {
            var tid = jQuery( this ).data( 'tid' );
            jQuery( this ).html( '<i class="fa fa-refresh fa-spin"></i>' );

            jQuery.post(
                ajaxurl,
                {
                    'action': 'bptodo_remove_todo',
                    'tid': tid,
                },
                function ( response ) {
                    if ( response == 'todo-removed' ) {
                        jQuery( '#bptodo-row-' + tid ).remove();
                    }
                }
            );
        }
    } );

    //Complete a todo
    jQuery( document ).on( 'click', '.bptodo-complete-todo', function () {
        var clicked_tid = jQuery( this );
        var tid = jQuery( this ).data( 'tid' );
        jQuery( this ).html( '<i class="fa fa-refresh fa-spin"></i>' );

        jQuery.post(
            ajaxurl,
            {
                'action': 'bptodo_complete_todo',
                'tid': tid,
            },
            function ( response ) {
                if ( response == 'todo-completed' ) {
                    clicked_tid.closest( 'tr' ).find( "td" ).addClass( 'todo-completed' );
                    clicked_tid.closest( 'td' ).prev( 'td' ).text( 'Completed!' );
                    clicked_tid.closest( 'td' ).html( '<ul><li><a href="javacript:void(0);" class="bptodo-remove-todo" data-tid="' + tid + '" title="Remove"><i class="fa fa-times"></i></a></li><li><a href="javacript:void(0);" class="bptodo-undo-complete-todo" data-tid="' + tid + '" title="Undo Complete"><i class="fa fa-undo"></i></a></li></ul>' )
                }
            }
        );
    } );

    //Undo complete a todo
    jQuery( document ).on( 'click', '.bptodo-undo-complete-todo', function () {
        var tid = jQuery( this ).data( 'tid' );
        jQuery( this ).html( '<i class="fa fa-refresh fa-spin"></i>' );

        jQuery.post(
            ajaxurl,
            {
                'action': 'bptodo_undo_complete_todo',
                'tid': tid,
            },
            function ( response ) {
                if ( response == 'todo-undo-completed' ) {
                    window.location.reload();
                }
            }
        );
    } );

    jQuery( document ).on( 'click', '#bp-add-new-todo', function () {
        jQuery( '#myModal' ).show();
    } );
    var span = document.getElementsByClassName( "close" )[0];
    var modal = document.getElementById( 'myModal' );
    // When the user clicks on <span> (x), close the modal
    if ( span ) {
        span.onclick = function () {
            modal.style.display = "none";
        }
    }
} );

function JSONToCSVConvertor( JSONData, ReportTitle, ShowLabel ) {
    var arrData = typeof JSONData != 'object' ? JSON.parse( JSONData ) : JSONData;
    var CSV = '';
    CSV += ReportTitle + '\r\n\n';
    if ( ShowLabel ) {
        var row = "";
        for ( var index in arrData[0] ) {
            row += index + ',';
        }
        row = row.slice( 0, -1 );
        CSV += row + '\r\n';
    }

    //1st loop is to extract each row
    for ( var i = 0; i < arrData.length; i++ ) {
        var row = "";
        for ( var index in arrData[i] ) {
            row += '"' + arrData[i][index] + '",';
        }
        row.slice( 0, row.length - 1 );
        //add a line break after each row
        CSV += row + '\r\n';
    }

    if ( CSV == '' ) {
        alert( "Invalid data" );
        return;
    }

    var fileName = "MyTasks_";
    fileName += ReportTitle.replace( / /g, "_" );
    var uri = 'data:text/csv;charset=utf-8,' + escape( CSV );
    var link = document.createElement( "a" );
    link.href = uri;
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";
    document.body.appendChild( link );
    link.click();
    document.body.removeChild( link );
}