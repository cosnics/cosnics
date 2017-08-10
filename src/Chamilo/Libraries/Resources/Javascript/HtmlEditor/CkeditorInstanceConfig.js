/*
 Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function(config)
{
    config.uiColor = '#F5F5F5';
    config.plugins = 'uploadwidget,a11yhelp,about,basicstyles,bidi,blockquote,button,clipboard,colorbutton,colordialog,contextmenu,dialog,dialogadvtab,dialogui,div,enterkey,entities,fakeobjects,find,flash,floatingspace,floatpanel,font,format,forms,horizontalrule,htmlwriter,iframe,image2,indent,indentblock,indentlist,justify,link,list,listblock,liststyle,magicline,maximize,menu,menubutton,newpage,pagebreak,panel,panelbutton,pastefromword,pastetext,popup,preview,print,removeformat,resize,richcombo,save,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,table,tabletools,templates,toolbar,undo,wsc,wysiwygarea,mathjax,widget,embed,filebrowser,autoembed';
    config.extraPlugins = "resource,resourceupload,chamilo,chamilofakeobjects,autosave,quickquestion,resourcestylecontextmenu";

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
            [ 'Chamilo', 'Image', 'Embed', 'Mathjax', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar',
                    'PageBreak' ], '/', [ 'Styles', 'Format', 'Font', 'FontSize' ], [ 'TextColor', 'BGColor' ],
            [ 'Maximize', 'ShowBlocks', '-', 'About' ] ];

    config.toolbar_Basic = [
            [ 'Maximize', '-', 'Styles', 'Format', 'Font', 'FontSize', '-', 'Bold', 'Italic', 'Underline' ],
            '/',
            [ 'Preview', 'Print', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'PasteCode', '-',
                    'NumberedList', 'BulletedList', 'HorizontalRule', '-', 'JustifyLeft', 'JustifyCenter',
                    'JustifyRight', 'JustifyBlock', 'Outdent', 'Indent' ],
            '/',
            [ 'Link', 'Unlink', 'Anchor', '-', 'TextColor', 'BGColor', '-', 'Table', 'Resource', 'Embed', 'Mathjax',
                    'Smiley', 'Quickquestion', '-', 'Templates', 'SpecialChar', '-', 'Source' ] ];

    /*config.filebrowserBrowseUrl = web_path
        + 'index.php?application=Chamilo\\Core\\Repository&go=HtmlEditorFile&plugin=chamilo';*/
    config.uploadUrl = web_path
        + 'index.php?application=Chamilo\\Core\\Repository\\Ajax&go=HtmlEditorFileUpload';

    /*config.filebrowserImageBrowseUrl  = web_path
        + 'index.php?application=Chamilo\\Core\\Repository&go=HtmlEditorFile&plugin=chamilo&tab=Browser';
    config.filebrowserImageUploadUrl= web_path
        + 'index.php?application=Chamilo\\Core\\Repository&go=HtmlEditorFile&plugin=chamilo';*/
    config.filebrowserChamiloBrowseUrl = web_path
            + 'index.php?application=Chamilo\\Core\\Repository&go=HtmlEditorFile&plugin=chamilo';
    config.filebrowserChamiloHandbookLinkBrowseUrl = web_path
            + 'index.php?application=Chamilo\\Application\Handbook&go=handbook_topic_picker';
    config.latexDialogUrl = web_path
            + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/plugins/latex/dialogs/latex.html?a=b';

    config.image2_captionedClass = 'image-captioned';
    config.image2_alignClasses = [ 'align-left', 'align-center', 'align-right' ];

    config.contentsCss = [
        web_path + 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/contents.css?v=1',
        web_path + 'index.php?application=Chamilo\\Libraries\\Ajax&go=CkeditorCss&theme=' + getTheme() + '?v=1',
        web_path + 'index.php?application=Chamilo%5CLibraries%5CAjax&go=resource&type=css&theme=' + getTheme() + '?v=1'
    ];

    config.embed_provider = '//noembed.com/embed?url={url}&callback={callback}'; //free. Default is iframely.

    config.disableNativeSpellChecker = false;
    config.allowedContent = true;
    config.resize_dir = 'both';
    //config.enterMode = CKEDITOR.ENTER_DIV;

    config.startupFocus = true;

    //config.mathJaxLib = '//cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?config=TeX-AMS_HTML';

    config.autosave = {
        SaveKey: null, // fix to force unique savekey (even for page with multiple instances)
        // Save Content on Destroy - Setting to Save content on editor destroy (Default is false) ...
        saveOnDestroy : true,

        // Setting to set the Save button to inform the plugin when the content is saved by the user and doesn't need to be stored temporary ...
        saveDetectionSelectors : 'button[type="submit"]',

        // Notification Type - Setting to set the if you want to show the "Auto Saved" message, and if yes you can show as Notification or as Message in the Status bar (Default is "notification")
        messageType : "no",

        // Delay
        delay : 15 //@todo: check performance with large pages
    };
};