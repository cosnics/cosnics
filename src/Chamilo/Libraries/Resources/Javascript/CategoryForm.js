$(function() {

	function add_category(evt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();
			var add_elem = jQuery(document.createElement('input'));
			add_elem.attr('type', 'hidden');
			add_elem.attr('name', 'add[]');
			add_elem.val('1');
			$('#category_form').prepend(add_elem);
			$('#category_form').submit();
			return false;
		}
	}

	$(document).ready(function() {
		$(document).on('keypress', '#category_form', add_category);
	});

});