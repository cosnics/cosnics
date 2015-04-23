/*global $, document, jQuery, window */

$(function() {

	function setPackageSelection(e, ui) {
		e.preventDefault();

		var packageElement = $(this);
		var packageElementCheckbox = $('input', packageElement);
		var packageElementSelection = $('input:checked', packageElement);

		if (packageElementSelection.length == 1) {
			packageElement.removeClass('package-list-item-selected');
			packageElementCheckbox.prop('checked', false);
		} else {
			packageElement.addClass('package-list-item-selected');
			packageElementCheckbox.prop('checked', true);
		}

	}

	function deselectAllPackages(e, ui) {
		var packageTypeImage = $(this);
		var packageTypeContainer = packageTypeImage.parent().parent();

		$('div.package-list-item', packageTypeContainer).removeClass(
				'package-list-item-selected');
		$('div.package-list-item input', packageTypeContainer).prop('checked',
				false);
	}

	function selectAllPackages(e, ui) {
		var packageTypeImage = $(this);
		var packageTypeContainer = packageTypeImage.parent().parent();

		$('div.package-list-item', packageTypeContainer).addClass(
				'package-list-item-selected');
		$('div.package-list-item input', packageTypeContainer).prop('checked',
				true);
	}

	function showSelectionOptions(e, ui) {
		var packageTypeHeader = $(this);

		$("h3 img.package-list-select-all", packageTypeHeader).show();
		$("h3 img.package-list-select-none", packageTypeHeader).show();
	}

	function hideSelectionOptions(e, ui) {
		var packageTypeHeader = $(this);

		$("h3 img.package-list-select-all", packageTypeHeader).hide();
		$("h3 img.package-list-select-none", packageTypeHeader).hide();
	}

	$(document).ready(
			function() {
				$(document).on('click',
						"div.package-selection div.package-list-item",
						setPackageSelection);
				$(document).on('click',
						"div.package-selection img.package-list-select-none",
						deselectAllPackages);
				$(document).on('click',
						"div.package-selection img.package-list-select-all",
						selectAllPackages);

				$("div.package-list img.package-list-select-all").hide();
				$("div.package-list img.package-list-select-none").hide();

				$(document).on('mouseover', "div.package-list",
						showSelectionOptions);
				$(document).on('mouseout', "div.package-list",
						hideSelectionOptions);
			});

});