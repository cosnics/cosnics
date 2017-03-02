/*
 Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.plugins.addExternal( 'chamilo', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/chamilo/');
CKEDITOR.plugins.addExternal( 'chamilofakeobjects', web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/chamilofakeobjects/');

CKEDITOR.editorConfig = function(config)
{
    config.uiColor = '#F5F5F5';
    config.plugins = 'a11yhelp,about,basicstyles,bidi,blockquote,button,clipboard,colorbutton,colordialog,contextmenu,dialog,dialogadvtab,dialogui,div,enterkey,entities,fakeobjects,find,flash,floatingspace,floatpanel,font,format,forms,horizontalrule,htmlwriter,iframe,image,indent,indentblock,indentlist,justify,link,list,listblock,liststyle,magicline,maximize,menu,menubutton,newpage,pagebreak,panel,panelbutton,pastefromword,pastetext,popup,preview,print,removeformat,resize,richcombo,save,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,table,tabletools,templates,toolbar,undo,wsc,wysiwygarea,eqneditor,oembed,widget,pastecode';
    config.extraPlugins = "chamilo,chamilofakeobjects";

    config.menu_groups = 'clipboard,' + 'form,' + 'tablecell,tablecellproperties,tablerow,tablecolumn,table,'
            + 'anchor,link,image,flash,'
            + 'checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea,div,' + 'chamilo';

    config.toolbarCanCollapse = true;

    config.toolbar_Full = [
            [ 'Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates' ],
            [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'PasteCode', '-', 'Print', 'SpellChecker', 'Scayt' ],
            [ 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat' ],
            [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ],
            '/',
            [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript' ],
            [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote' ],
            [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
            [ 'Link', 'Unlink', 'Anchor' ],
            [ 'Chamilo', 'Image', 'oembed', 'EqnEditor', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar',
                    'PageBreak' ], '/', [ 'Styles', 'Format', 'Font', 'FontSize' ], [ 'TextColor', 'BGColor' ],
            [ 'Maximize', 'ShowBlocks', '-', 'About' ] ];

    config.toolbar_Basic = [
            [ 'Maximize', '-', 'Styles', 'Format', 'Font', 'FontSize', '-', 'Bold', 'Italic', 'Underline' ],
            '/',
            [ 'Preview', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'PasteCode', '-',
                    'NumberedList', 'BulletedList', 'HorizontalRule', '-', 'JustifyLeft', 'JustifyCenter',
                    'JustifyRight', 'JustifyBlock', 'Outdent', 'Indent' ],
            '/',
            [ 'Link', 'Unlink', '-', 'TextColor', 'BGColor', '-', 'Table', 'Chamilo', 'Image', 'oembed', 'EqnEditor',
                    'Smiley', '-', 'Templates', 'SpecialChar', '-', 'Source' ] ];

    config.filebrowserChamiloBrowseUrl = web_path
            + 'index.php?application=Chamilo\\Core\\Repository&go=HtmlEditorFile&plugin=chamilo';
    config.filebrowserChamiloHandbookLinkBrowseUrl = web_path
            + 'index.php?application=Chamilo\\Application\Handbook&go=handbook_topic_picker';
    config.latexDialogUrl = web_path
            + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/plugins/latex/dialogs/latex.html?a=b';

    config.contentsCss = [
        web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/contents.css',
        web_path + 'index.php?application=Chamilo\\Libraries\\Ajax&go=CkeditorCss&theme=' + getTheme(),
        web_path + 'index.php?application=Chamilo%5CLibraries%5CAjax&go=resource&type=css&theme=' + getTheme()
    ];

    config.disableNativeSpellChecker = false;
    config.allowedContent = true;
    config.resize_dir = 'both';

    // allow i tags to be empty (for font awesome)
    CKEDITOR.dtd.$removeEmpty['i'] = false;

    //fix for bootstrap skin
    CKEDITOR.skin.chameleon = function(){
        return '';
    };
};
