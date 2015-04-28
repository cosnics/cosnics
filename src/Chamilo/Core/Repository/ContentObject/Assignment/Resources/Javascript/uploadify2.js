$(function() {
	$(document)
			.ready(
					function() {
						$('#uploadify2')
								.uploadify(
										{
											'swf' : getPath('WEB_PATH')
													+ '/Configuration/Plugin/jquery/uploadify/uploadify.swf',
											'uploader' : getPath('WEB_PATH')
													+ 'index.php',
											'cancelImg' : getPath('WEB_PATH')
													+ '/Configuration/Plugin/jquery/uploadify/uploadify-cancel.png',
											'folder' : 'not_important',
											'auto' : true,
											'displayData' : 'percentage',
											'formData' : {
												'user_id' : getMemory('_uid'),
												'application' : 'Chamilo\\Core\\Repository\\Ajax',
												'go' : 'upload_image'
											},
											onComplete : function(evt, queueID,
													fileObj, response, data) {
												var fileName = fileObj.name
														.split('.');
												$(
														'#select_attachment_search_field')
														.val(fileName[0]);
												$('#tbl_select_attachment')
														.trigger(
																'update_search');
												var properties = eval('('
														+ response + ')');
												$('#lo_' + properties.id)
														.trigger('activate');
											}
										});
					});

});
