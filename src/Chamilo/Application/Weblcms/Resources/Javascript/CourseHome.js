(function ($) {
    var handle_visible_clicked = function (ev, ui) {
        var parent = $(this).parent().parent();
        var old_parent = parent.parent().parent().parent();
        var tool = parent.attr('id');
        tool = tool.substring(5, tool.length);

        // Determine visibility icon
        var img = $(this).attr("src");
        var imgtag = $(this);
        var pos = $(this).hasClass('invisible-tool');

        // Determine tool icon
        var tool_img = $(".tool_image", parent);
        var src = tool_img.attr('src');

        // Determine tool text class
        var tool_text = $("#tool_text", parent);

        var isInvisible = tool_text.hasClass('invisible-tool');

        if (isInvisible) {
            var new_visible = 1;
        }
        else {
            var new_visible = 0;
        }

        // Determine course id
        var course_code = $("#coursecode").html();

        $.post(
            "./index.php?application=Chamilo\\Application\\Weblcms\\Ajax&go=ChangeCourseModuleVisibility", {
                tool: tool,
                visible: new_visible,
                course: course_code
            }, function (data) {
                if (data.result_code == 200) {
                    // If succeeded : change the icons and classes
                    // Changes icons and classes
                    var is_visible = imgtag.attr('src');
                    // imgtag.attr('src', new_img);

                    var new_parent = $('div.removed-tools');

                    if (!isInvisible) {
                        tool_text.addClass('invisible-tool');
                        var new_src = src.replace('New', '');
                        new_src = new_src.replace('.png', 'Na.png');

                        imgtag.attr('src', is_visible.replace('Visible.png', 'Invisible.png'));
                    }
                    else {
                        tool_text.removeClass('invisible-tool');
                        var new_src = src.replace('Na.png', '.png');
                        imgtag.attr('src', is_visible.replace('Invisible.png', 'Visible.png'));
                    }


                    // If we use disabled section we should move the
                    // tool to the correct section
                    if (new_parent) {
                        var clear_div = new_parent.children(".clear")[0];
                        if (clear_div) {
                            new_parent.children(".clear")[0].remove;
                        }

                        var message = $('div.normal-message', new_parent);
                        if (message) {
                            message.remove();
                        }

                        new_parent.append(parent.parent());
                        new_parent.append(clear_div);

                        if (old_parent.find('.tool').size() == 0) {
                            old_parent.prepend(
                                '<div class="alert alert-info">' + getTranslation('NoToolsAvailable', {}, 'Chamilo\\Application\\Weblcms') +
                                '</div>'
                            );
                        }
                    }

                    tool_img.attr('src', new_src);
                }
            }
        );

        return false;
    }

    function toolsSortableStart(e, ui) {
        ui.helper.css("border", "4px solid #c0c0c0");
    }

    function toolSortableBeforeStop(e, ui) {
        ui.helper.css("border", "0px solid #c0c0c0");
    }

    function toolsSortableUpdate(e, ui) {
        var section = $(this).attr("id");
        var order = $(this).sortable("serialize");

        $.post(
            "./application/weblcms/php/ajax/block_sort.php", {
                section: section,
                order: order
            }, function (data) {

            }
        );

    }

    function toolsSortable() {
        $(".toolblock .block .description").sortable("destroy");
        $(".toolblock .block .description").sortable(
            {
                cancel: 'a',
                opacity: 0.8,
                forceHelperSize: true,
                forcePlaceholderSize: true,
                cursor: 'move',
                helper: 'original',
                placeholder: 'toolSortHelper',
                revert: true,
                scroll: false,
                start: toolsSortableStart,
                beforeStop: toolSortableBeforeStop,
                update: toolsSortableUpdate
            }
        );
    }

    $(document).ready(
        function () {
            toolsSortable();

            $(".tool_visible").bind('click', handle_visible_clicked);

            $(".tooldrag").css('display', 'inline');

        }
    );

})(jQuery);