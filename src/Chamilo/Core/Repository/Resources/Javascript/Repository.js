(function($) {

	var maxBlockHeight = 0, maxComplexBlockHeight = 0, checkboxes;
	var isSearchAutoCompleteLoaded = false;

	function checkCompareCheckboxes(e, ui) {
		checkboxCount = $(
				"table.table-data > tbody > tr > td > input.repository_version_browser_table_id:checkbox:checked")
				.size();

		if (checkboxCount >= 2) {
			$(
					"table.table-data > tbody > tr > td > input.repository_version_browser_table_id:checkbox:not(:checked)")
					.attr('disabled', true);
		} else {
			$(
					"table.table-data > tbody > tr > td > input.repository_version_browser_table_id:checkbox")
					.removeAttr('disabled');
		}
	}

	$(document)
			.ready(
					function() {

						$("div.create_block").each(function(i) {
							if ($(this).height() > maxBlockHeight) {
								maxBlockHeight = $(this).height();
							}
						});

						$("div.create_block").height(maxBlockHeight);
						$("#other_content_object_types").hide();

						$("div.thumbnail_actions").hide();
						$(document).on('mouseenter',
								"table.gallery_table > tbody > tr > td",
								function(i) {
									$("div.thumbnail_actions", this).show();
								});
						$(document).on('mouseleave',
								"table.gallery_table > tbody > tr > td",
								function(i) {
									$("div.thumbnail_actions", this).hide();
								});
						$(document)
								.on(
										'click',
										"table.table-data > tbody > tr > td > input.repository_version_browser_table_id:checkbox",
										checkCompareCheckboxes);
					});

	$(".search_query").autocomplete({
		source : getPath('WEB_PATH') + 'Repository/Ajax/SearchComplete.php',
		minLength : 2,
		select : function(event, ui) {
			console.log(ui.item.value);
		}
	});

})(jQuery);