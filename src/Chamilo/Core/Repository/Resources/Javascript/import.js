(function($) {
	var buttonName;
	var buttonClass;
	var timer;

	function disableImportButton(e, ui) {

		if ($(this).hasClass(buttonClass)) {

			$(this).attr('readonly');
			$(this).removeClass('positive');
			$(this).addClass('loading');
			$(this)
					.html(
							getTranslation('Uploading', null,
									'common\\libraries'));
		}
	}

	function checkCategoryName(e, ui) {
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
		var categoryName = $('input[name="new_category"]').attr("value");
		var parentId = $('select[name="parent_id"]').attr("value");

		if (categoryName.length == 0) {
			$('input[name="new_category"]').removeClass('input_valid');
			$('input[name="new_category"]').removeClass('input_conflict');
			$('button[name="import_button"]').attr('class', '');
			$('button[name="import_button"]').html(buttonName);
			$('button[name="import_button"]').unbind('click');
			$('button[name="import_button"]').addClass(buttonClass);
		} else {
			var parameters = {
				'application' : 'Chamilo\\Core\\Repository\\Ajax',
				'go' : 'check_category_name',
				'name' : categoryName,
				'parent_id' : parentId
			};

			var response = $.ajax({
				type : "POST",
				url : ajaxUri,
				data : parameters,
				async : false
			}).success(
					function(json) {
						if (json.result_code == 409) {
							$('input[name="new_category"]').removeClass(
									'input_valid');
							$('input[name="new_category"]').addClass(
									'input_conflict');
							$('button[name="import_button"]').attr('class', '');
							$('button[name="import_button"]').addClass(
									'negative error');
							$('button[name="import_button"]').html(
									getTranslation('InvalidConflict', null,
											'common\\libraries'));
							$('button[name="import_button"]').bind('click',
									disableButton);
						} else {
							$('input[name="new_category"]').removeClass(
									'input_conflict');
							$('input[name="new_category"]').addClass(
									'input_valid');
							$('button[name="import_button"]').attr('class', '');
							$('button[name="import_button"]').addClass(buttonClass);
							$('button[name="import_button"]').html(buttonName);
							$('button[name="import_button"]').unbind('click');
						}
					});
		}
	}

	function showNewCategory(e, ui) {
		e.preventDefault();
		$("div#new_category").show();
		$("input#add_category").hide();
	}

	function disableButton(e, ui) {
		e.preventDefault();
	}

	$(document).ready(function() {
		$("div#new_category").hide();
		$("input#add_category").show();
		$(document).on('click', "input#add_category", showNewCategory);

		$("input[name='new_category']").keypress(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				checkCategoryName();
			} else {
				clearTimeout(timer);
				timer = setTimeout(checkCategoryName, 750);
			}
		});

		$("input[name='new_category']").change(function() {
			clearTimeout(timer);
			timer = setTimeout(checkCategoryName, 750);
		});

		$("select[name='parent_id']").change(function() {
			checkCategoryName();
		});
		buttonName = $('button[name="import_button"]').val();
		buttonClass = $('button[name="import_button"]').attr('class');
		$(document).on('click', '#import_button', disableImportButton);
	});

})(jQuery);