/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
	// // Define changes to default configuration here. For example:
	config.uiColor = '#F5F5F5';
	config.plugins = 'a11yhelp,about,basicstyles,bidi,blockquote,button,clipboard,colorbutton,colordialog,contextmenu,dialog,dialogadvtab,dialogui,div,enterkey,entities,fakeobjects,find,flash,floatingspace,floatpanel,font,format,forms,horizontalrule,htmlwriter,iframe,image,indent,indentblock,indentlist,justify,link,list,listblock,liststyle,magicline,maximize,menu,menubutton,newpage,pagebreak,panel,panelbutton,pastefromword,pastetext,popup,preview,print,removeformat,resize,richcombo,save,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,table,tabletools,templates,toolbar,undo,wsc,wysiwygarea,chamilo,chamilofakeobjects,eqneditor';
	// config.removePlugins = 'elementspath,resize';

	config.menu_groups = 'clipboard,'
			+ 'form,'
			+ 'tablecell,tablecellproperties,tablerow,tablecolumn,table,'
			+ 'anchor,link,image,flash,'
			+ 'checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea,div,'
			+ 'chamilo';

	config.toolbarCanCollapse = true;

	config.toolbar_Full = [
			[ 'Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates' ],
			[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-',
					'Print', 'SpellChecker', 'Scayt' ],
			[ 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll',
					'RemoveFormat' ],
			[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select',
					'Button', 'ImageButton', 'HiddenField' ],
			'/',
			[ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript',
					'Superscript' ],
			[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
					'Blockquote' ],
			[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
			[ 'Link', 'Unlink', 'Anchor' ],
			[ 'Chamilo', 'Image', 'EqnEditor', 'Table', 'HorizontalRule', 'Smiley',
					'SpecialChar', 'PageBreak' ], '/',
			[ 'Styles', 'Format', 'Font', 'FontSize' ],
			[ 'TextColor', 'BGColor' ],
			[ 'Maximize', 'ShowBlocks', '-', 'About' ] ];

	// config.toolbar_Webpage =
	// [
	// ['Source','-','Save','NewPage','Preview','-','Templates'],
	// ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print',
	// 'SpellChecker', 'Scayt'],
	// ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	// ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select',
	// 'Button', 'ImageButton', 'HiddenField'],
	// '/',
	// ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	// ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	// ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	// ['Link','Unlink','Anchor'],
	// ['Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
	// '/',
	// ['Styles','Format','Font','FontSize'],
	// ['TextColor','BGColor'],
	// ['Maximize', 'ShowBlocks','-','About']
	// ];

	// config.toolbar_Html =
	// [
	// ['Maximize','-','Font','FontSize','Format','Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','Cut','Copy','Paste','PasteText','PasteFromWord'],
	// '/',
	// ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','-','Outdent','Indent','Blockquote','-','TextColor','BGColor','-','HorizontalRule','Link','Unlink','-','Image','Table','-','Source']
	// ];

	config.toolbar_Basic = [
			[ 'Maximize', '-', 'Styles', 'Format', 'Font', 'FontSize', '-',
					'Bold', 'Italic', 'Underline' ],
			'/',
			[ 'Preview', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste',
					'PasteText', 'PasteFromWord', '-', 'NumberedList',
					'BulletedList', 'HorizontalRule', '-', 'JustifyLeft',
					'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Outdent',
					'Indent' ],
			'/',
			[ 'Link', 'Unlink', '-', 'TextColor', 'BGColor', '-', 'Table',
					'Chamilo', 'Image', 'EqnEditor', 'Smiley', '-',
					'Templates', 'SpecialChar', '-', 'Source' ] ];
	//	
	// config.toolbar_BasicMarkup =
	// [
	// ['Maximize','-','Bold','Italic','Underline','-','NumberedList',
	// 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule']
	// ];
	//	
	// config.toolbar_WikiPage =
	// [
	// ['Source','-','Maximize','-','Bold','Italic','Underline','-','NumberedList',
	// 'BulletedList','-','Link','Unlink','-','TextColor','BGColor','-','HorizontalRule','-','Image','Chamiloflash','-','Templates']
	// ];

	// config.toolbar_HandbookItem =
	// [
	// ['Bold','Italic','Underline','-','NumberedList',
	// 'BulletedList','-','TextColor','BGColor','-','HorizontalRule','-','chamiloHandbookLink','-','latex','-',
	// 'Source','-','Maximize']
	// ];

	// config.toolbar_RepositoryQuestion =
	// [
	// ['Maximize','PasteFromWord','-','Bold','Italic','Underline','-','NumberedList',
	// 'BulletedList','-','TextColor','BGColor','-','Chamilo','Image']
	// ] ;
	//		
	// config.toolbar_RepositorySurveyQuestion =
	// [
	// ['Maximize','PasteFromWord','-','Bold','Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList',
	// 'BulletedList','-','TextColor','BGColor']
	// ] ;

	// config.toolbar_Assessment =
	// [
	// ['Maximize','-','Bold','Italic','Underline','-','NumberedList',
	// 'BulletedList','-','TextColor','BGColor']
	// ] ;

	config.filebrowserChamiloBrowseUrl = web_path
			+ 'index.php?application=Chamilo\\Core\\Repository&go=html_editor_file&plugin=chamilo';
	config.filebrowserChamiloHandbookLinkBrowseUrl = web_path
			+ 'index.php?application=Chamilo\\Application\Handbook&go=handbook_topic_picker';
	config.latexDialogUrl = web_path
			+ 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/release/ckeditor/plugins/latex/dialogs/latex.html?a=b';

	config.contentsCss = [
			web_path
					+ 'Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor/release/ckeditor/contents.css',
			web_path
					+ 'index.php?application=Chamilo\\Libraries\\Ajax&go=CkeditorCss&theme='
					+ getTheme() ];

	config.disableNativeSpellChecker = false;
	config.allowedContent = true;
	config.resize_dir = 'both';
};
