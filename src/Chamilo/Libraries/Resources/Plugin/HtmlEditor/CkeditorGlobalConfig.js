(function () {
    CKEDITOR.plugins.addExternal(
        'chamilo', web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/chamilo/');
    CKEDITOR.plugins.addExternal(
        'chamilofakeobjects',
        web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/chamilofakeobjects/'
    );
    CKEDITOR.plugins.addExternal(
        'resource', web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/resource/');
    CKEDITOR.plugins.addExternal(
        'resourceupload',
        web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/resourceupload/'
    );
    CKEDITOR.plugins.addExternal(
        'resourcestylecontextmenu',
        web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/resourcestylecontextmenu/'
    );
    CKEDITOR.plugins.addExternal(
        'quickquestion',
        web_path + 'Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorExtra/Plugin/quickquestion/'
    );

    CKEDITOR.stylesSet.add('default', [
        // Adding space after the style name is an intended workaround. For now, there
        // is no option to create two styles with the same name for different widget types. See #16664.
        {name: 'small ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-240p'}, cm_order: 1}, //groups give issues with the stylescombo plugin
        {name: 'medium ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-360p'}, cm_order: 2},
        {name: 'large ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-720p'}, cm_order: 3},

        {name: 'small', type: 'widget', widget: 'resource', attributes: {'class': 'embed-240p'}, cm_order: 1},
        {name: 'medium', type: 'widget', widget: 'resource', attributes: {'class': 'embed-360p'}, cm_order: 2},
        {name: 'large', type: 'widget', widget: 'resource', attributes: {'class': 'embed-720p'}, cm_order: 3},
        {name: 'Marker', element: 'span', attributes: {'class': 'marker'}},

        {name: 'Big', element: 'big'},
        {name: 'Small', element: 'small'},
        {name: 'Typewriter', element: 'tt'},

        {name: 'Computer Code', element: 'code'},
        {name: 'Keyboard Phrase', element: 'kbd'},
        {name: 'Sample Text', element: 'samp'},
        {name: 'Variable', element: 'var'},

        {name: 'Deleted Text', element: 'del'},
        {name: 'Inserted Text', element: 'ins'},

        {name: 'Cited Work', element: 'cite'},
        {name: 'Inline Quotation', element: 'q'}
    ]);

    // allow i tags to be empty (for font awesome)
    CKEDITOR.dtd.$removeEmpty['i'] = false;

    //fix for bootstrap skin
    CKEDITOR.skin.chameleon = function () {
        return '';
    };

})();

