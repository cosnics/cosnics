
$(function () {
    $(window).resize(function () {
        var h = Math.max($(window).height() - 0, 420);
        $('#container, #data, #tree, #data .content').height(h).filter('.default').css('lineHeight', h + 'px');
    }).resize();

    $('#tree')
        .jstree({
            'core' : {
                'data' : {
                    'url' : '?operation=get_node',
                    'data' : function (node) {
                        return { 'id' : node.id };
                    }
                },
                'force_text' : true,
                'check_callback' : true,
                'themes' : {
                    'responsive' : false
                }
            },
            'plugins' : ['state','dnd','contextmenu','wholerow']
        })
        .on('delete_node.jstree', function (e, data) {
            $.get('?operation=delete_node', { 'id' : data.node.id })
                .fail(function () {
                    data.instance.refresh();
                });
        })
        .on('create_node.jstree', function (e, data) {
            $.get('?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                .done(function (d) {
                    data.instance.set_id(data.node, d.id);
                })
                .fail(function () {
                    data.instance.refresh();
                });
        })
        .on('rename_node.jstree', function (e, data) {
            $.get('?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
                .fail(function () {
                    data.instance.refresh();
                });
        })
        .on('move_node.jstree', function (e, data) {
            $.get('?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
                .fail(function () {
                    data.instance.refresh();
                });
        })
        .on('copy_node.jstree', function (e, data) {
            $.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
                .always(function () {
                    data.instance.refresh();
                });
        })
        .on('changed.jstree', function (e, data) {
            if(data && data.selected && data.selected.length) {
                $.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
                    $('#data .default').text(d.content).show();
                });
            }
            else {
                $('#data .content').hide();
                $('#data .default').text('Select a file from the tree.').show();
            }
        });
});
