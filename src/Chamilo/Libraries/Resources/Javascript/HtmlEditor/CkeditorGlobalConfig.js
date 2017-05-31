(function() {
    CKEDITOR.plugins.addExternal( 'chamilo', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/chamilo/');
    CKEDITOR.plugins.addExternal( 'chamilofakeobjects', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/chamilofakeobjects/');
    CKEDITOR.plugins.addExternal('resource', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/resource/');
    CKEDITOR.plugins.addExternal('resourceupload', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/resourceupload/');
    CKEDITOR.plugins.addExternal('resourcestylecontextmenu', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/resourcestylecontextmenu/');
    CKEDITOR.plugins.addExternal('quickquestion', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/quickquestion/');
console.log(CKEDITOR);
    CKEDITOR.lang.nl.embedbase.button = 'Voeg externe media in';
    CKEDITOR.lang.nl.eqneditor.edit = 'Wijzig vergelijking';
    CKEDITOR.lang.nl.eqneditor.menu = 'Wiskunde';
    CKEDITOR.lang.nl.eqneditor.title = 'CodeCogs vergelijkings-editor';
    CKEDITOR.lang.nl.eqneditor.toolbar = 'Voeg nieuwe vergelijking';

    CKEDITOR.stylesSet.add( 'default', [
        // Adding space after the style name is an intended workaround. For now, there
        // is no option to create two styles with the same name for different widget types. See #16664.
        { name: 'small ', type: 'widget', widget: 'embed', attributes: { 'class': 'embed-240p' }, cm_order: 1 }, //groups give issues with the stylescombo plugin
        { name: 'medium ', type: 'widget', widget: 'embed', attributes: { 'class': 'embed-360p' }, cm_order: 2},
        { name: 'large ', type: 'widget', widget: 'embed', attributes: { 'class': 'embed-720p' }, cm_order: 3},

        { name: 'small', type: 'widget', widget: 'resource', attributes: { 'class': 'embed-240p' }, cm_order: 1},
        { name: 'medium', type: 'widget', widget: 'resource', attributes: { 'class': 'embed-360p' }, cm_order: 2 },
        { name: 'large', type: 'widget', widget: 'resource', attributes: { 'class': 'embed-720p' }, cm_order: 3 }
    ]);

    // allow i tags to be empty (for font awesome)
    CKEDITOR.dtd.$removeEmpty['i'] = false;

    //fix for bootstrap skin
    CKEDITOR.skin.chameleon = function(){
        return '';
    };

})();

