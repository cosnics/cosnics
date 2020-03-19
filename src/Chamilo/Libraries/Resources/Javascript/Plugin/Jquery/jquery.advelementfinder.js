/**
 * Copyright (c) 2011, Sven Vanpoucke
 */

(function($) {
	$.fn
			.extend({
				advelementfinder : function(options) {
					// Settings list and the default values
					var defaults = {
						name : '',
						elementTypes : null,
						defaultValues : null,
						maxItems : 100,
						maxSelectableItems : null
					};

					var settings = $.extend(defaults, options);
					var ajaxUri = getPath('WEB_PATH') + 'index.php';

					// Declare some global variables
					var self = this;

					// Declare the two boxes as global so they can be reused
					var inactiveBox;
					var activeBox;

					// Keeps track of the last selected elements for both boxes
					// (to
					// enable multiple select)
					var lastSelectedElements = new Array();

					// Keeps track of the activated elements so they can be
					// greyed out
					// from the search results
					var activatedElements = new Array();

					/**
					 * Keeps track of the selected filter box
					 */
					var selectedFilter;

					/**
					 * Temporary filter box. This is needed because we only add
					 * the filter box to the list if there are filters
					 * available.
					 */
					var tempFilterBox;

					/**
					 * Keeps track of the current offset of the elements. When
					 * there are more then 500 elements we select the elements
					 * in smaller groups for performance upgrades
					 */
					var offset = 0;

					/**
					 * Removes an element from the activated elements array with
					 * given index
					 */
					function removeActivatedElementFromArray(id) {
						var index = $.inArray(id, activatedElements);
						if (index != -1) {
							activatedElements.splice(index, 1);
						}
					}

					/**
					 * Json encodes the array and moves it to the form
					 */
					function addActivatedElementsToForm() {
						var encoded = '[';

						var size = activatedElements.length;
						for ( var i = 0; i < size; i++) {
							var element = activatedElements[i];
							encoded += '"' + element + '"';

							if (i < size - 1) {
								encoded += ',';
							}

						}

						encoded += ']';

						$('#hidden_active_elements', self).val(encoded);
					}

					/**
					 * Sets the given element to enabled (both class / image)
					 */
					function enableElement(theElement) {
						if (typeof theElement.css('background-image') !== 'undefined') {
							theElement.removeClass('disabled');
							theElement.css('background-image', theElement.css(
									'background-image').replace('Na.png',
									'.png'));

							var filterBoxes = $('.filter_box', self);
							$('#' + theElement.attr('id'), filterBoxes).attr(
									'disabled', '');
						}
					}

					/**
					 * Sets the given element to disabled (both class / image)
					 * so it can not be selected again
					 */
					function disableElement(theElement) {
						if (theElement.css('background-image')) {
							if (!theElement.hasClass('disabled')) {
								theElement.addClass('disabled');
								theElement.css('background-image', theElement
										.css('background-image').replace(
												'.png', 'Na.png'));

								var filterBoxes = $('.filter_box', self);
								$('#' + theElement.attr('id'), filterBoxes)
										.attr('disabled', 'disabled');
							}
						}
					}

					/**
					 * Activates the selected elements from the inactive box
					 */
					function activateElements(e) {
						e.preventDefault();

						// Loop through all the selected elements in the
						// inactivebox
						var selectedElements = $('.selected', inactiveBox);
						$
								.each(
										selectedElements,
										function() {
											// Remove the selected /
											// lastselected classes from
											// the new

											if (settings.maxSelectableItems != null
													&& (selectedElements.length
															+ activatedElements.length > settings.maxSelectableItems)) {
												alert(getTranslation(
														'TooManyItemsSelected',
														{
															'MAX_ITEMS' : settings.maxSelectableItems
														}, 'Chamilo\\Libraries'));

												return false;
											}

											// created item
											$(this).removeClass('selected');
											$(this).removeClass('lastSelected');

											// Retrieve the selected elements
											// html
											var selectedElementLi = $(this)
													.closest('li');
											var elementHtml = selectedElementLi
													.html();

											// Copy the selected elements html
											// to a new li and
											// add them
											// to the active box
											var newElementLi = $('<li />', {
												html : elementHtml
											});
											addElementTo(newElementLi,
													activeBox);

											activatedElements.push($(this)
													.attr('id'));

											// Disables this element in the
											// inactivebox
											disableElement($(this));

										});

						activationPostProcess(inactiveBox);

						return false;
					}

					/**
					 * Deactivates the selected elements from the active box
					 */
					function deactivateElements(e) {
						e.preventDefault();

						// Loop through all the selected elements in the
						// inactivebox
						var selectedElements = $('.selected', activeBox);
						$.each(selectedElements, function() {
							var selectedId = $(this).attr('id');

							// Retrieve the selected elements li and remove it
							var selectedElementLi = $(this).closest('li');
							selectedElementLi.remove();

							// Remove the element from the activated elements
							// list
							removeActivatedElementFromArray(selectedId);

							// Enable the element in the inactiveBox
							var disabledElement = $('a#' + selectedId,
									inactiveBox);
							enableElement(disabledElement);
						});

						activationPostProcess(activeBox);

						return false;
					}

					/**
					 * Post process a activate or deactivate event
					 */
					function activationPostProcess(box) {
						// Clear the last selected element if the last selected
						// element
						// has been disabled
						var lastSelectedElement = $('.lastSelected', box);
						if (lastSelectedElement.length == 0) {
							lastSelectedElements[box.attr('id')] = null;
						}

						// Change the state of the activate elements button
						setActivateElementsButtonState();

						// Reprocess the tree's images
						processTree();

						// Add activated elements to the form array
						addActivatedElementsToForm();
					}

					/**
					 * Sets the activate_elements button state depeninding on
					 * the max selectable items
					 */
					function setActivateElementsButtonState() {
						if (settings.maxSelectableItems != null
								&& activatedElements.length >= settings.maxSelectableItems) {
							$('input.activate_elements', self).addClass(
									'disabled');
							$('input.activate_elements', self).attr('disabled',
									'disabled');
						} else {
							$('input.activate_elements', self).removeClass(
									'disabled');
							$('input.activate_elements', self).removeAttr(
									'disabled');
						}
					}

					/**
					 * Selects one or multiple elements
					 */
					function selectElement(e) {
						e.preventDefault();

						// Retrieve the selected object's list root
						var selectedUl = $(this).closest('ul');

						// Retrieve the last selected element and his list root
						var selectedBoxId = selectedUl.closest('div')
								.attr('id');
						var lastSelectedElement = lastSelectedElements[selectedBoxId];

						if (lastSelectedElement) {
							var lastSelectedUl = lastSelectedElement
									.closest('ul');
						}

						/**
						 * When shift is pressed and the parents of the last
						 * selected element and the curent selected element are
						 * equal then we start the multiple select process
						 */
						if (e.shiftKey && lastSelectedUl
								&& selectedUl.get(0) == lastSelectedUl.get(0)) {
							// Clear all the selected elements in the box
							$('.selected', $(this).parents('ul')).removeClass(
									'selected');

							// Retrieve the indexes of the current and last
							// selected
							// element
							var elementsLi = selectedUl.children('li');

							var selectedElementLi = $(this).closest('li');
							var lastSelectedElementLi = lastSelectedElement
									.closest('li');

							var currentLiIndex = elementsLi
									.index(selectedElementLi);
							var lastSelectedLiIndex = elementsLi
									.index(lastSelectedElementLi);

							// Determine the lowest and the highest selected
							// index
							var start = Math.min(lastSelectedLiIndex,
									currentLiIndex);
							var end = Math.max(lastSelectedLiIndex,
									currentLiIndex);

							// Add the selected class to all the items between
							// the
							// current and last selected element
							for ( var i = start; i <= end; i++) {
								var elementLi = elementsLi.eq(i);
								var element = $('a', elementLi);

								if (!element.hasClass('disabled')) {
									element.addClass('selected');
								}
							}
						} else {
							/**
							 * If the ctrl key is pressed then the selected
							 * element it's class will be toggled Otherwise all
							 * the selected elements from the box will be
							 * deselected and only the current element will be
							 * selected. (check for ctrl and meta key for mac
							 * users)
							 */
							if (e.ctrlKey || e.metaKey) {
								$(this).toggleClass('selected');
							} else {
								$('.selected', $(this).parents('ul'))
										.removeClass('selected');
								$(this).addClass('selected');
							}

							$('.lastSelected', $(this).parents('ul'))
									.removeClass('lastSelected');
							$(this).addClass('lastSelected');

							// Add the last selected element to the cache for
							// this box
							lastSelectedElements[selectedBoxId] = $(this);
						}

						return false;
					}

					function elementDoubleClicked(e) {
						var filterBoxSelect = $('.filter_box:last > select',
								self);
						if (filterBoxSelect.length == 0) {
							return false;
						}

						var id = $(this).attr('id');
						var option = $('#' + id, filterBoxSelect);
						if (option.length > 0) {
							filterBoxSelect.val(id);
							filterBoxSelect.change();
						}

						return false;
					}

					/**
					 * Fixes classnames so the tree menu is correctly renderered
					 */
					function processTree() {
						$('div', self).removeClass('last');
						$('ul li:last-child > div', self).addClass('last');
						$('ul li:last-child > ul', self).css(
								'background-image', 'none');
					}

					/**
					 * Shows the element finder
					 */
					function showElementFinder(e) {
						e.preventDefault();
						$(this).hide();
						$('#' + settings.name + '_collapse_button').show();
						self.show();
					}

					/**
					 * Hides the element finder
					 */
					function hideElementFinder(e) {
						e.preventDefault();
						$(this).hide();
						$('#' + settings.name + '_expand_button').show();
						self.hide();
					}

					/**
					 * Handles the selection of an element type
					 */
					function elementTypeSelected(e) {
						// Remove all the filter boxes
						$('.filter_box', self).remove();
						delete (selectedFilter);

						// Clear search field
						$('#search_field', self).val('');

						offset = 0;
						updateElements();
					}

					/**
					 * Handles the selection of a filter box
					 */
					function filterBoxSelected(e) {
						// Remove the filter boxes beneath this filter_box
						var parentFilterBox = $(this).closest('.filter_box');
						parentFilterBox.nextAll().remove();

						// Clear search field
						$('#search_field', self).val('');

						var value = $(this).val();
						if (value == -1) {
							var previousBox = parentFilterBox.prev();
							if (previousBox) {
								value = $('select', previousBox).val();
							} else {
								value = 0;
							}

							parentFilterBox.remove();
						}

						selectedFilter = value;

						offset = 0;
						updateElements();
					}

					/**
					 * Handles the keypress event in the search field Only
					 * reacts on keycode 13 (enter key)
					 */
					function searchFieldChanged(e) {
						if (e.keyCode == 13) {
							var query = $(this).val();
							if (query.length == 0) {
								offset = 0;
								updateElements(true);
							} else {
								var replacedQuery = str_replace('*', '', query);

								if (replacedQuery.length == 0) {
									inactiveBox.html(getTranslation(
											'QueryCanNotBeStarsOnly', null,
											'Chamilo\\Libraries'))
								} else {
									if (replacedQuery.length < 3) {
										inactiveBox.html(getTranslation(
												'QueryMinimum3Characters',
												null, 'Chamilo\\Libraries'));
									} else {
										offset = 0;
										updateElements(true);
									}
								}
							}

							return false;
						}

						return true;
					}

					/**
					 * Handles the click on the previous (-1) or next (1)
					 * elements box
					 */
					function selectOtherElements(multiplier) {
						offset = offset + (multiplier * settings.maxItems);
						updateElements();
					}

					/**
					 * Updates the elements in the list with the selected type
					 */
					function updateElements(is_search) {
						inactiveBox
								.html('<div class="element_finder_loading"><span class="fas fa-spinner fa-pulse fa-3x"></span></div>');

						var selectedTypeId = $('#element_types_selector', self)
								.val();
						if (selectedTypeId == -1) {
							inactiveBox.html(getTranslation(
									'SelectElementType', null,
									'Chamilo\\Libraries'));
							return;
						}

						var elementType = getElementTypeById(selectedTypeId);

						var query = $('#search_field', self).val();
						var result = loadElements(elementType, query,
								selectedFilter);

						if (result.properties.elements
								&& result.properties.elements.length > 0) {
							var totalElements = result.properties.total_elements;

							var hasNextElements = (offset + settings.maxItems <= totalElements);
							var hasPreviousElements = (offset > 0);

							inactiveBox.html('');
							lastSelectedElements['inactive_box'] = null;

							if (hasPreviousElements) {
								var previousDiv = $('<div />', {
									'class' : 'previous_elements'
								});

								inactiveBox.append(previousDiv);
							}

							var elementsDiv = $('<div />');
							inactiveBox.append(elementsDiv);

							tempFilterBox = createFilterBox();

							buildInactiveElements(result.properties.elements,
									elementsDiv);

							if (!is_search
									&& $('select', tempFilterBox).children().length > 0) {
								// Add a dummy option
								var option = createFilterOption(-1,
										getTranslation('SelectFilter', null,
												'Chamilo\\Libraries'));
								$('select', tempFilterBox).prepend(option);
								$('select', tempFilterBox).prop(
										'selectedIndex', 0);

								$('.element_finder_types', self).append(
										tempFilterBox);
								tempFilterBox = null;
							}

							tempFilterBox = null;

							if (hasNextElements) {
								var nextDiv = $('<div />', {
									'class' : 'next_elements'
								});

								inactiveBox.append(nextDiv);
							}
							processTree();
						} else {
							inactiveBox.html(getTranslation('NoSearchResults',
									null, 'Chamilo\\Libraries'));
						}
					}

					/**
					 * Retrieves the element type by id from the element types
					 * array
					 */
					function getElementTypeById(id) {
						for ( var i = 0; i < settings.elementTypes.length; i++) {
							if (settings.elementTypes[i].id == id) {
								return settings.elementTypes[i];
							}
						}

						return null;
					}

					/**
					 * Loads the elements as a json result
					 */
					function loadElements(elementType, query, filter) {
						var parameters = {
							'application' : elementType.application,
							'go' : elementType.go,
							'query' : query,
							'filter' : filter,
							'offset' : offset
						};

						if (elementType.parameters) {
							$.extend(parameters, elementType.parameters);
						}
						
						var result = doAjaxPost(ajaxUri, parameters);
						result = eval('(' + result + ')');

						return result;
					}

					/**
					 * Builds an element list based on an array in the
					 * inactiveBox
					 */
					function buildInactiveElements(elements, parent) {
						$.each(elements, function(i, element) {
							var selected = ($.inArray(element.id,
									activatedElements) != -1);

							// If the element is a filter
							if (element.type > 1) {
								var option = createFilterOption(element.id,
										element.title);

								if (selected) {
									option.attr('disabled', 'disabled');
								}

								$('select', tempFilterBox).append(option);
							}

							var elementLi = buildElement(element, parent);

							var elementLink = $('a', elementLi);

							if (selected) {
								disableElement(elementLink);
							}

							if (element.type == 3) {
								elementLink.addClass('filter');
							}

							buildInactiveElements(element.children, elementLi);
						});
					}

					/**
					 * Builds an element list based on an array in the activeBox
					 */
					function buildActiveElements(elements, parent) {
						$.each(elements, function(i, element) {
							var elementLi = buildElement(element, parent);

							activatedElements.push(element.id);

							buildActiveElements(element.children, elementLi);
						});
					}

					/**
					 * Builds the layout of an element
					 */
					function buildElement(element, parent) {
						var elementLi = createElement(element.id,
								element.classes, element.title,
								element.description);

						addElementTo(elementLi, parent);

						return elementLi;
					}

					/**
					 * Creates a new element for the elements list
					 * 
					 * @param id
					 * @param classes
					 * @param title
					 * @param description
					 */
					function createElement(id, classes, title, description) {
						var li = $('<li />');
						var div = $('<div />');
						var item = $('<a />', {
							id : id,
							'class' : classes,
							href : '#',
							title : description,
							text : title
						})

						div.append(item);
						li.append(div);

						return li;
					}

					/**
					 * Adds an element to a parent element (or to the root)
					 */
					function addElementTo(li, parent_element) {
						var ul = parent_element.children('ul');

						if (!ul.is('ul')) {
							ul = $('<ul />');
							if (parent_element.is('div')) {
								ul.addClass('tree-menu');
							}
						}

						ul.append(li);
						parent_element.append(ul);
					}

					/**
					 * Creates a filterbox
					 */
					function createFilterBox() {
						var div = $('<div>', {
							'class' : 'filter_box'
						});

						var filterbox = $('<select>');

						$(div).append(filterbox);

						return div;
					}

					/**
					 * Creates a filter option for an elemtn
					 */
					function createFilterOption(id, title) {
						var option = $('<option>', {
							id : id,
							value : id,
							text : title
						});

						return option;
					}

					/**
					 * Initializes the elements
					 */
					function initElements() {
						// (Re)Initialize some default values
						activeBox.html('');
						activatedElements = [];
						lastSelectedElements = [];
						delete (selectedFilter);

						if (settings.elementTypes.length == 1) {
							$('#element_types_selector', self).prop(
									'selectedIndex', 1);
							$('#element_types_selector', self).toggle();
						} else {
							$('#element_types_selector', self).prop(
									'selectedIndex', 0);
						}

						$('.filter_box', self).remove();

						// Builds the list with default selected values
						if (settings.defaultValues) {
							buildActiveElements(settings.defaultValues,
									activeBox);
							addActivatedElementsToForm();

							processTree();

							// Change the state of the activate elements button
							setActivateElementsButtonState();
						}

						// Loads the selectable elements
						updateElements();
					}

					/**
					 * Initializes the advanced element finder
					 */
					function init() {
						// Initalise global variables
						inactiveBox = $('#inactive_elements', self);
						activeBox = $('#active_elements', self);

						initElements();

						// Declare the events for the element type changer
						// combobox
						$(self).on('change', '#element_types_selector',
								elementTypeSelected);

						// Declare the events for the filter boxes
						$(self).on('change', '.filter_box > select',
								filterBoxSelected);

						// Declare the events for the elements
						$(self).on('click',
								'a:not(.disabled, .category, .filter)',
								selectElement);
						$(inactiveBox).on('dblclick',
								'a:not(.disabled, .category)',
								elementDoubleClicked);
						$(self).on('click', 'a.disabled, a.category, a.filter',
								function() {
									return false;
								});

						// Declare the events for the search field
						$('.element_query').keypress(searchFieldChanged);

						// Declare the events for the pager
						$(self).on('click', '.previous_elements', function() {
							selectOtherElements(-1);
						});
						$(self).on('click', '.next_elements', function() {
							selectOtherElements(1);
						});

						// Declare the events for the buttons
						$(self).on('click', '#activate_button',
								activateElements);
						$(self).on('click', '#deactivate_button',
								deactivateElements);

						$('#' + settings.name + '_expand_button').click(
								showElementFinder);
						$('#' + settings.name + '_collapse_button').click(
								hideElementFinder);

						// Declare the reset events
						$(document).on('click', ':reset', initElements);
					}

					return this.each(init);
				}
			});
})(jQuery);
