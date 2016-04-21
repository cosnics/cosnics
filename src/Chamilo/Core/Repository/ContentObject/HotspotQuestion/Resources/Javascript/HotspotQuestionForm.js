/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost, serialize, unserialize */

$(function() {
	// var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62',
	// '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384',
	// '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932',
	// '#ff9429', '#f6d7c5', '#7a2893'],
	var colours = [ '#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff',
			'#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff' ], offset, currentPolygon = null, positions = [], skippedOptions = 0;

	/***************************************************************************
	 * Functionality to draw hotspots
	 **************************************************************************/

	function redrawPolygon() {
		$('.polygon_fill_' + currentPolygon, $('#selected_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#selected_image')).remove();

		$('#selected_image').fillPolygon(positions[currentPolygon].X,
				positions[currentPolygon].Y, {
					clss : 'polygon_fill_' + currentPolygon,
					color : colours[currentPolygon],
					alpha : 0.5
				});
		$('#selected_image').drawPolygon(positions[currentPolygon].X,
				positions[currentPolygon].Y, {
					clss : 'polygon_line_' + currentPolygon,
					color : colours[currentPolygon],
					stroke : 1,
					alpha : 0.9
				});
	}

	function setCoordinates() {
		var coordinatesField = $('input[name="coordinates[' + currentPolygon
				+ ']"]'), coordinatesData = [], currentCoordinates = positions[currentPolygon];

		$.each(currentCoordinates.X, function(index, item) {
			coordinatesData.push([ item, currentCoordinates.Y[index] ]);
		});

		coordinatesField.val((serialize(coordinatesData)));
	}

	function getCoordinates(ev, ui) {
		if (currentPolygon !== null) {
			var pX, pY;

			offset = $('#selected_image').offset();

			pX = ev.pageX - offset.left;
			pY = ev.pageY - offset.top;
			pX = pX.toFixed(0);
			pY = pY.toFixed(0);
			positions[currentPolygon].X.push(parseInt(pX, 10));
			positions[currentPolygon].Y.push(parseInt(pY, 10));

			redrawPolygon();
			setCoordinates();
		}
	}

	function resetPolygonObject(id) {
		currentPolygon = id;

		positions[currentPolygon] = {};
		positions[currentPolygon].X = [];
		positions[currentPolygon].Y = [];

		$('.polygon_fill_' + currentPolygon, $('#selected_image')).remove();
		$('.polygon_line_' + currentPolygon, $('#selected_image')).remove();
	}

	function resetPolygon(ev, ui) {
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('reset_', '');
		$('#hotspot_marking .colour_box').css('background-color', colours[id]);
		resetPolygonObject(id);
	}

	function editPolygon(ev, ui) {
		ev.preventDefault();
		var id = $(this).attr(('id')).replace('edit_', '');
		$('#hotspot_marking .colour_box').css('background-color', colours[id]);
		resetPolygonObject(id);
	}

	function initializePolygons() {
		$('input[name*="coordinates"]').each(
				function(i) {
					var fieldName = $(this).attr('name'), id = fieldName
							.substr(12, fieldName.length - 13), fieldValue = $(
							this).val();

					if (fieldValue !== '') {
						fieldValue = unserialize(fieldValue);

						currentPolygon = id;
						resetPolygonObject(id);

						$.each(fieldValue, function(index, item) {
							positions[id].X.push(item[0]);
							positions[id].Y.push(item[1]);
						});

						redrawPolygon();
					}
				});
	}

	/***************************************************************************
	 * Functionality to add / remove options
	 **************************************************************************/

	function getEditIcon() {
		return $('.table-data > tbody > tr:first > td:last .edit_option').attr(
				'src');
	}

	function getResetIcon() {
		return $('.table-data > tbody > tr:first > td:last .reset_option')
				.attr('src');
	}

	function getDeleteIcon() {
		return $('.table-data > tbody > tr:first > td:last .remove_option')
				.attr('src').replace('_na.png', '.png');
	}

	function processOptions() {
		var deleteImage, deleteField, rows;

		deleteImage = '<img class="remove_option" src="'
				+ getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[$option_number]" />';
		rows = $('.table-data > tbody > tr');

		if (rows.size() <= 1) {
			deleteField = deleteImage;
		}

		rows.each(function() {
			var weightField, weightFieldName, id, appendField;

			weightField = $('input[name*="option_weight"]', this);
			weightFieldName = weightField.attr('name');
			id = weightFieldName.substr(14, weightFieldName.length - 15);
			appendField = deleteField.replace(/\$option_number/g, id);

			$('.remove_option', this).remove();
			$('td:last', this).append(appendField);
		});
	}

	function removeOption(ev, ui) {
		ev.preventDefault();

		var tableBody = $(this).parent().parent().parent(), id = $(this).attr(
				'id'), row = 0, rows;

		id = id.replace('remove_', '');
		destroyHtmlEditor('answer[' + id + ']');
		destroyHtmlEditor('comment[' + id + ']');
		$('tr#option_' + id, tableBody).remove();
		$('input[name="coordinates[' + id + ']"]').remove();

		rows = $('tr', tableBody);

		doAjaxPost("./libraries/ajax/mc_question.php", {
			action : 'skip_option',
			value : id
		});

		rows.each(function() {
			var row_class = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', row_class);
			row += 1;
		});

		skippedOptions += 1;
		processOptions();

		// Delete the hotspots visually on the image
		$('.polygon_fill_' + id, $('#selected_image')).remove();
		$('.polygon_line_' + id, $('#selected_image')).remove();
	}

	function addOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions = $('#mc_number_of_options').val(), newNumber = (parseInt(
				numberOfOptions, 10) + 1), rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even'
				: 'row_odd', name = 'correct[' + numberOfOptions + ']', id = name, fieldColour, fieldCoordinates, fieldAnswer, fieldComment, fieldScore, fieldEdit, fieldReset, fieldDelete, string, parameters, editorNameAnswer, editorNameComment;

		setMemory('mc_number_of_options', newNumber);

		$('#mc_number_of_options').val(newNumber);

		parameters = {
			"width" : "100%",
			"height" : "65",
			"toolbar" : "RepositoryQuestion",
			"collapse_toolbar" : true
		};
		editorNameAnswer = 'answer[' + numberOfOptions + ']';
		editorNameComment = 'comment[' + numberOfOptions + ']';

		fieldColour = '<div class="colour_box" style="background-color: '
				+ colours[numberOfOptions] + ';"></div>';
		fieldCoordinates = '<input name="coordinates[' + numberOfOptions
				+ ']" type="hidden" value="" />';
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldComment = renderHtmlEditor(editorNameComment, parameters);
		fieldScore = '<input class="input_numeric" type="text" value="1" name="option_weight['
				+ numberOfOptions + ']" size="2" />';
		fieldEdit = '<input id="edit_' + numberOfOptions
				+ '" class="edit_option" type="image" src="' + getEditIcon()
				+ '" name="edit[' + numberOfOptions + ']" />&nbsp;&nbsp;';
		fieldReset = '<input id="reset_' + numberOfOptions
				+ '" class="reset_option" type="image" src="' + getResetIcon()
				+ '" name="reset[' + numberOfOptions + ']" />&nbsp;&nbsp;';
		fieldDelete = '<input id="remove_' + numberOfOptions
				+ '" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[' + numberOfOptions
				+ ']" />';

		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
				+ '"><td>' + fieldColour + fieldCoordinates + '</td><td>'
				+ fieldAnswer + '</td><td>' + fieldComment + '</td><td>'
				+ fieldScore + '</td><td>' + fieldEdit + fieldReset
				+ fieldDelete + '</td></tr>';

		$('.table-data > tbody').append(string);

		processOptions();

		// Prepare the positions array and hotspots image
		$('#hotspot_marking .colour_box').css('background-color',
				colours[numberOfOptions]);
		resetPolygonObject(numberOfOptions);
	}

	function setHotspotImage(ev, ui) {
		$('div.label', $('#selected_image').closest('div.row')).remove();
		$('div.formw', $('#selected_image').closest('div.row')).css('float',
				'left').css('width', '100%');

		$('#hotspot_options').show();
		$('#hotspot_marking').show();

		// Select the first option by default
		$('#hotspot_marking .colour_box').css('background-color', colours[0]);
		resetPolygonObject(0);
	}

	function lockWeight(ev, ui) {
		var checked = $(this).prop('checked');
		if (checked) {
			$('input[name="weight"]').prop('disabled', true);
		} else {
			$('input[name="weight"]').prop('disabled', false);
		}
	}

	$(document).ready(
			function() {
				// We've got JavaScript so we hide the warning message
				$('#hotspot_javascript').hide();
				// $('#image_select').show();

				// Initialize possible existing polygons
				initializePolygons();

				// Bind clicks on the edit and reset buttons
				$(document).on('click', 'input[name*="edit"]', editPolygon);
				$(document).on('click', 'input[name*="reset"]', resetPolygon);

				// Bind clicks on the image
				$(document).on('click', '#selected_image', getCoordinates);

				// Bind actions to option management buttons
				$(document).on('click', '.remove_option', removeOption);
				$(document).on('click', '.add_option', addOption);

				var value = $('input[name="image"]').val();
				if (value != '') {
					$('div.label', $('#selected_image').closest('div.row'))
							.remove();
					$('div.formw', $('#selected_image').closest('div.row'))
							.css('float', 'left').css('width', '100%');
				}

				$('input[name="image"]').change(function() {
					setHotspotImage();
				});

				// Process image selection
				$(document).on('click',
						'.element_finder_inactive a:not(.disabled, .category)',
						setHotspotImage);

				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

});
