/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost */

$(function() {

	function initializeDragAndDrop() {
		$("#options, #choices").children().draggable({ 
			revert: true 
			}).disableSelection();
		$("#choices, #options").droppable({ 
					activeClass: "ui-state-highlight",
					drop: handleDrop
			}).disableSelection();

	}
	
	function handleDrop(event, ui){
		
		
		var order_limit = parseInt($('#order_limit').val(), 10),
		option_count = parseInt($('#option_count').val(), 10) , id;
		
		id = ui.draggable.attr('id');
//		alert(id);
		$('#options > #'+id).remove();
		ui.draggable.attr('class', 'ui-draggable');
		$("#choices").append(ui.draggable);
		
		
		
//		if(! (item_count <= order_limit)){
//			$( "#sortable2" ).sortable( "cancel" );
//			$( "#sortable2" ).sortable( "refresh" );
//			alert('event zou gestopt moeten zijn');
//		}
		
//		alert('item_count: '+option_count+' orderlimit: '+order_limit);
	}
	

	$(document).ready(function() {

		initializeDragAndDrop();
//		$( "#sortable2" ).on( "sortstop", checkOrderLimit);

	});

});