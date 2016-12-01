/**
 * Copyright (c) 2009, Hans De Bisschop, conversion to seperate (non ui-tabs
 * based) plugin
 */

(function($) {
	$.fn
			.extend({
				dynamicContentMenu : function(options) {
					// Settings list and the default values
					var defaults = {
						selectedItemId : ''
					};

					var settings = $.extend(defaults, options);
					var self = this;

					/**
					 * Handles the click on a dynamic content menu item
					 */
					function dynamicContentMenuItemClicked(e) {
						e.preventDefault();

						var id = $(this).attr('id');
						activateSelectedItem(id);
					}

					/**
					 * Actives the content panel of the selected item
					 */
					function activateSelectedItem(selectedItemId) {
						// Remove the selected class from the menu items
						$('li.dynamic_content_menu_item', self).removeClass(
								'selected');
						$('div.dynamic_content_menu_item_content', self)
								.removeClass('selected');

						$(
								'li#' + selectedItemId
										+ '.dynamic_content_menu_item', self)
								.addClass('selected');
						$(
								'div#' + selectedItemId
										+ '.dynamic_content_menu_item_content',
								self).addClass('selected');

						$(
								'div.dynamic_content_menu_item_content:not(.selected)',
								self).hide();
						$('div.dynamic_content_menu_item_content.selected',
								self).show();
					}

					/**
					 * Initializes the dynamic content menu
					 */
					function init() {
						// Initialize events
						$(self).on('click',
								'li.dynamic_content_menu_item:not(.selected)',
								dynamicContentMenuItemClicked);

						// Enable javascript based layout
						var contentContainer = $(
								'div.dynamic_content_menu_content_container',
								self);

						contentContainer.css('min-height',
								$('div.dynamic_content_menu_menu_container',
										self).height() + 50);
						contentContainer.css('margin-left', '200px');

						contentContainer.addClass('ui-dynamic-content');

						$('div.dynamic_content_menu_item_content_header',
								contentContainer).hide();

						var menuContainer = $(
								'div.dynamic_content_menu_menu_container', self);

						menuContainer.show();

						// Selected default item
						if (settings.selectedItemId != '') {
							activateSelectedItem(settings.selectedItemId);
						}
					}

					return this.each(init);
				}
			});
})(jQuery);
