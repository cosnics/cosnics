/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost, serialize, unserialize */

$(function() {
	// var colours = ['#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62',
	// '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384',
	// '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932',
	// '#ff9429', '#f6d7c5', '#7a2893'],
	var colours = [ '#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff',
			'#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff' ], offset, currentPolygon = null, currentHotspot = null, positions = [];

	/***************************************************************************
	 * Functionality to draw hotspots
	 **************************************************************************/

	function calculateSelectionCoordinates(posX, posY) {
		var coordinates = {
			X : [],
			Y : []
		};

		coordinates.X.push(posX);
		coordinates.X.push(posX + 7);
		coordinates.X.push(posX);
		coordinates.X.push(posX - 7);

		coordinates.Y.push(posY - 7);
		coordinates.Y.push(posY);
		coordinates.Y.push(posY + 7);
		coordinates.Y.push(posY);

		return coordinates;
	}

	function redrawPolygon() {
		$('.polygon_fill_' + currentHotspot + '_' + currentPolygon,
				$('#hotspot_image_' + currentHotspot)).remove();
		$('.polygon_line_' + currentHotspot + '_' + currentPolygon,
				$('#hotspot_image_' + currentHotspot)).remove();

		var selectionCoordinates = calculateSelectionCoordinates(
				positions[currentHotspot][currentPolygon].X,
				positions[currentHotspot][currentPolygon].Y);

		$('#hotspot_image_' + currentHotspot).fillPolygon(
				selectionCoordinates.X,
				selectionCoordinates.Y,
				{
					clss : 'polygon_fill_' + currentHotspot + '_'
							+ currentPolygon,
					color : colours[currentPolygon],
					alpha : 0.5
				});
		$('#hotspot_image_' + currentHotspot).drawPolygon(
				selectionCoordinates.X,
				selectionCoordinates.Y,
				{
					clss : 'polygon_line_' + currentHotspot + '_'
							+ currentPolygon,
					color : colours[currentPolygon],
					stroke : 1,
					alpha : 1
				});
	}

	function setCoordinates() {
		var coordinatesField = $('input[name="' + currentHotspot + '_'
				+ currentPolygon + '"]'), coordinatesData, currentCoordinates = positions[currentHotspot][currentPolygon];

		coordinatesData = [ currentCoordinates.X, currentCoordinates.Y ];
		coordinatesField.val((serialize(coordinatesData)));
	}

	function resetPolygonObject(question, option) {
		currentHotspot = question;
		currentPolygon = option;

		if (typeof positions[currentHotspot] === 'undefined') {
			positions[currentHotspot] = {};
		}

		positions[currentHotspot][currentPolygon] = {};
		positions[currentHotspot][currentPolygon].X = [];
		positions[currentHotspot][currentPolygon].Y = [];

		$('.polygon_fill_' + currentHotspot + '_' + currentPolygon,
				$('#hotspot_image_' + question)).remove();
		$('.polygon_line_' + currentHotspot + '_' + currentPolygon,
				$('#hotspot_image_' + question)).remove();
	}

	function getCoordinates(ev, ui) {
		if (currentPolygon !== null && currentHotspot !== null) {
			var pX, pY;

			resetPolygonObject(currentHotspot, currentPolygon);
			offset = $('#hotspot_image_' + currentHotspot).offset();

			pX = ev.pageX - offset.left;
			pY = ev.pageY - offset.top;
			pX = pX.toFixed(0);
			pY = pY.toFixed(0);
			positions[currentHotspot][currentPolygon].X = parseInt(pX, 10);
			positions[currentHotspot][currentPolygon].Y = parseInt(pY, 10);

			redrawPolygon();
			setCoordinates();
		}
	}

	function resetPolygon(ev, ui) {
		ev.preventDefault();
		var ids = $(this).attr('id').replace('reset_', '').split('_'), question_id = ids[0], option_id = ids[1];

		$('#hotspot_marking_' + question_id + ' .colour_box').css(
				'background-color', 'transparent');
		resetPolygonObject(question_id, option_id);

		$(
				'tr#' + currentHotspot + '_' + currentPolygon
						+ ' img.hotspot_configured').hide();
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.edit_option')
				.show();

		currentHotspot = null;
		currentPolygon = null;
	}

	function editPolygon(ev, ui) {
		ev.preventDefault();
		var ids = $(this).attr('id').replace('colour_', '').split('_'), question_id = ids[0], option_id = ids[1];

		$('#hotspot_marking_' + question_id + ' .colour_box').css(
				'background-color', colours[option_id]);
		resetPolygonObject(question_id, option_id);
	}

	function addCheck() {
		$('tr#' + currentHotspot + '_' + currentPolygon + ' img.edit_option')
				.hide();
		$(
				'tr#' + currentHotspot + '_' + currentPolygon
						+ ' img.hotspot_configured').show();
	}

	function initializePolygons() {
		var hotspotQuestions = $('input.hotspot_coordinates');

		hotspotQuestions
				.each(function(i) {
					var fieldIds = $(this).attr('name').split('_'), fieldValue = $(
							this).val(), questionId = fieldIds[0], answerId = fieldIds[1];

					if (fieldValue !== '') {
						fieldValue = unserialize(fieldValue);

						currentHotspot = questionId;
						currentPolygon = answerId;

						resetPolygonObject(questionId, answerId);

						positions[currentHotspot][currentPolygon].X = fieldValue[0];
						positions[currentHotspot][currentPolygon].Y = fieldValue[1];

						redrawPolygon();
					}

				});
	}

	$(document).ready(function() {
		// Initialize possible existing polygons
		// Possible when going back to a previous page or the likes.
		initializePolygons();

		// Bind clicks on the edit and reset buttons
		$(document).on('click', '.colour_box', editPolygon);
		$(document).on('click', '.reset_option', resetPolygon);

		$('.hotspot_question_options .colour_box:first').trigger('click');

		// Bind clicks on the image
		$('.hotspot_image').click(getCoordinates);
	});

});