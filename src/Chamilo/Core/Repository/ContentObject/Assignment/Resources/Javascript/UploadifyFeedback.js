$(function() {
	$(document)
			.ready(
					function() {
						$('#uploadifyFeedback')
								.uploadify(
										{
											'swf' : getPath('WEB_PATH')
													+ 'Chamilo/Libraries/Resources/Javascript/Plugin/Uploadify/uploadify.swf',
											'uploader' : getPath('WEB_PATH')
													+ 'index.php',
											'auto' : true,
											'progressData' : 'percentage',
											'formData' : {
												'user_id' : getMemory('_uid'),
												'application' : 'Chamilo\\Core\\Repository\\Ajax',
												'go' : 'UploadImage'
											},
											onUploadSuccess : function(file,
													data, response) {
												var ajaxResult = eval('('
														+ data + ')');
												$(
														'#select_attachment_search_field')
														.val(
																ajaxResult.properties.title);
												$('#tbl_select_attachment')
														.trigger(
																'update_search');

												$(
														'#lo_'
																+ ajaxResult.properties.id)
														.trigger('activate');
												$('button#select_attachment_expand_button').trigger('click');
											}
										});
					});

});
