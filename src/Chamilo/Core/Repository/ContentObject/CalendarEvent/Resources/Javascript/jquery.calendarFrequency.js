(function ($) {
    $.fn
        .extend({
            calendarFrequency: function (options) {
                // Settings list and the default values
                var defaults = {
                    defaultValues: null,
                };

                /**
                 * Initializes the advanced element finder
                 */
                function init() {
                    // $('.frequency-options').hide();


                    // // Initalise global variables
                    // inactiveBox = $('#inactive_elements', self);
                    // activeBox = $('#active_elements', self);
                    //
                    // initElements();
                    //
                    // // Declare the events for the element type changer
                    // // combobox
                    // $(self).on('change', '#element_types_selector',
                    //     elementTypeSelected
                    // );
                    //
                    // // Declare the events for the filter boxes
                    // $(self).on('change', '.filter_box > select',
                    //     filterBoxSelected
                    // );
                    //
                    // // Declare the events for the elements
                    // $(self).on(
                    //     'click',
                    //     'a:not(.disabled, .category, .filter)',
                    //     selectElement
                    // );
                    // $(inactiveBox).on(
                    //     'dblclick',
                    //     'a:not(.disabled, .category)',
                    //     elementDoubleClicked
                    // );
                    // $(self).on('click', 'a.disabled, a.category, a.filter',
                    //     function () {
                    //         return false;
                    //     }
                    // );
                    //
                    // // Declare the events for the search field
                    // $('.element_query').keypress(searchFieldChanged);
                    //
                    // // Declare the events for the pager
                    // $(self).on('click', '.previous_elements', function () {
                    //     selectOtherElements(-1);
                    // });
                    // $(self).on('click', '.next_elements', function () {
                    //     selectOtherElements(1);
                    // });
                    //
                    // // Declare the events for the buttons
                    // $(self).on('click', '#activate_button',
                    //     activateElements
                    // );
                    // $(self).on('click', '#deactivate_button',
                    //     deactivateElements
                    // );
                    //
                    // $('#' + settings.name + '_expand_button').click(
                    //     showElementFinder);
                    // $('#' + settings.name + '_collapse_button').click(
                    //     hideElementFinder);
                    //
                    // // Declare the reset events
                    // $(document).on('click', ':reset', initElements);
                }

                return this.each(init);
            }
        });
})(jQuery);
