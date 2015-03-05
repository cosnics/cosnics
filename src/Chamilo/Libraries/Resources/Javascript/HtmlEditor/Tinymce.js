function myFileBrowser(field_name, url, type, win) {

//	alert("Field_Name: " + field_name + "\nURL: " + url + "\nType: " + type
//			+ "\nWin: " + win); // debug/testing
	
	if (type == 'media')
	{
		
	}

	/* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
	   the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
	   These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

	var cmsURL = getPath('WEB_PATH') + 'common/html/formvalidator/html_editor/html_editor_file_browser/index.php?plugin=' + type + '&repoviewer_action=browser';

	tinyMCE.activeEditor.windowManager.open( {
		file : cmsURL,
		title : 'Browse Repository',
		width : '700', // Your dimensions may differ - toy around with them!
		height : '500',
		resizable : "yes",
		close_previous : "no"
	}, {
		window : win,
		input : field_name
	});
	
	return false;
}
