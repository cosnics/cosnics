$(function() {
    $(document).ready(function() {
        $(document).on('click', '.specific_rights_selector', function() {
            $('.specific_rights_selector_box').show();
        });
        $(document).on('click', '.inherit_rights_selector', function() {
            $('.specific_rights_selector_box').hide();
        });

        $(document).on('click', '.other_option_selected', function() {
            var parent = $(this).parents('.right');
            $('.entity_selector_box', parent).hide();
        });

        $(document).on('click', '.entity_option_selected', function() {
            var parent = $(this).parents('.right');
            $('.entity_selector_box', parent).show();
        });

        $('.specific_rights_selector').each(function() {
            if ($(this).attr('checked')) {
            $('.specific_rights_selector_box').show();
            }
        });

        // if ($('.entity_option_selected').attr('checked')) {
        // In the rights for a weblcms, the form is shown multiple times. Using
        // the above selector then causes a javascript error (because it matches
        // more than 1 element)
        // The below selector fixes that, but shows the entity_selector_box for
        // all instances of the form if 1 or more elements match.
        // That's better than not working at all, but should be reworked to use
        // the element id's.

        $('.entity_option_selected').each(function() {

            var entitySelectorBox = $(this).closest('.right').find('.entity_selector_box');

            if ($(this).attr('checked')) {
            entitySelectorBox.show();
            } else {
            entitySelectorBox.hide();
            }
        });
    });

});