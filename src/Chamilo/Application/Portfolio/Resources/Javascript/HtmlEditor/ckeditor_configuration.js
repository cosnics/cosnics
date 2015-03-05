/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.extraPlugins = 'chamilo,chamilofakeobjects,latex,chamiloHandbookLink';
	config.removePlugins = 'flash,elementspath,resize';
	config.menu_groups = 'clipboard,'
			+ 'form,'
			+ 'tablecell,tablecellproperties,tablerow,tablecolumn,table,'
			+ 'anchor,link,image,flash,'
			+ 'checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea,div,'
			+ 'chamilo,latex';

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
			[ 'Image', 'Chamiloflash', 'Table', 'HorizontalRule', 'Smiley',
					'SpecialChar', 'PageBreak' ], '/',
			[ 'Styles', 'Format', 'Font', 'FontSize' ],
			[ 'TextColor', 'BGColor' ],
			[ 'Maximize', 'ShowBlocks', '-', 'About' ] ];

	config.toolbar_Webpage = [
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
			[ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar',
					'PageBreak' ], '/',
			[ 'Styles', 'Format', 'Font', 'FontSize' ],
			[ 'TextColor', 'BGColor' ],
			[ 'Maximize', 'ShowBlocks', '-', 'About' ] ];
	config.toolbar_Html = [
			[ 'Maximize', '-', 'Font', 'FontSize', 'Format', 'Bold', 'Italic',
					'Underline', 'Strike', '-', 'Subscript', 'Superscript',
					'-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord' ],
			'/',
			[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',
					'-', 'NumberedList', 'BulletedList', '-', 'Outdent',
					'Indent', 'Blockquote', '-', 'TextColor', 'BGColor', '-',
					'HorizontalRule', 'Link', 'Unlink', '-', 'Image',
					'Chamiloflash', 'Table', '-', 'Source' ] ];

	config.toolbar_Basic = [ [ 'Maximize', '-', 'Styles', 'Format', 'Font',
			'FontSize', '-', 'Bold', 'Italic', 'Underline', '-',
			'NumberedList', 'BulletedList', 'HorizontalRule', '-', 'Link',
			'Unlink', '-', 'TextColor', 'BGColor', '-', 'Table', 'Image',
			'Chamiloflash'/* ,'Chamiloflashvideo' */, 'Chamiloyoutube',
			'Chamilovideo', 'Chamiloaudio', 'latex', '-', 'Templates',
			'SpecialChar', '-', 'Source' ] ];

	config.toolbar_BasicMarkup = [ [ 'Maximize', '-', 'Bold', 'Italic',
			'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link',
			'Unlink', '-', 'TextColor', 'BGColor', '-', 'HorizontalRule' ] ];

	config.toolbar_WikiPage = [ [ 'Source', '-', 'Maximize', '-', 'Bold',
			'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-',
			'Link', 'Unlink', '-', 'TextColor', 'BGColor', '-',
			'HorizontalRule', '-', 'Image', 'Chamiloflash', '-', 'Templates' ] ];

	config.toolbar_HandbookItem = [ [ 'Bold', 'Italic', 'Underline', '-',
			'NumberedList', 'BulletedList', '-', 'TextColor', 'BGColor', '-',
			'HorizontalRule', '-', 'chamiloHandbookLink', '-', 'latex', '-',
			'Source', '-', 'Maximize' ] ];

	config.toolbar_RepositoryQuestion = [ [ 'Maximize', 'PasteFromWord', '-',
			'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList',
			'-', 'TextColor', 'BGColor', '-', 'Image', 'Chamiloflash' ] ];

	config.toolbar_RepositorySurveyQuestion = [ [ 'Maximize', 'PasteFromWord',
			'-', 'Bold', 'Italic', 'Underline', '-', 'JustifyLeft',
			'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
			'NumberedList', 'BulletedList', '-', 'TextColor', 'BGColor' ] ];

	config.toolbar_Assessment = [ [ 'Maximize', '-', 'Bold', 'Italic',
			'Underline', '-', 'NumberedList', 'BulletedList', '-', 'TextColor',
			'BGColor' ] ];

	config.filebrowserChamiloHandbookLinkBrowseUrl = web_path
			+ 'common/libraries/php/shared/launcher/index.php?application=html_editor_file&plugin=handbook_topic&repoviewer_action=browser';
	config.latexDialogUrl = web_path
			+ 'configuration/plugin/html_editor/ckeditor/plugins/latex/dialogs/latex.html?a=b';

};
