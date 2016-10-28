/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme, doAjaxPost, setMemory */

$(function() {

	function saveOldQuestionValue(event, userInterface) {
		oldQuestionValue = $('#question').val();
	}

	function synchronizeTitle(event, userInterface) {
		var questionValue = $('#question').val();
		var titleValue = $('#title').val();

		if (!titleValue || titleValue == oldQuestionValue) {
			$('#title').val(questionValue);
			$("#title").trigger('change');
		}
	}

	$(document).ready(function() {
		$(document).on('focusin', '#question', saveOldQuestionValue);
		$(document).on('focusout', '#question', synchronizeTitle);
	});

});