/*global $, document, jQuery, window */

$(function () {

	$(document).ready(function ()
	{
//		$('.iphonecheckbox').iphoneStyle({ checkedLabel: 'On', uncheckedLabel: 'Off'});
		$("#form_tabs ul").css('display', 'block');
		$("#form_tabs h2").hide();
		$("#form_tabs").tabs();
		var tabs = $('#form_tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
		tabs.tabs('select', tabnumber);
	});

});