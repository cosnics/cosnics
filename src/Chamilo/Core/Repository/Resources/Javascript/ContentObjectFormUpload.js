$(function() {

	function showNewCategory(e, ui) {
		e.preventDefault();
		$("div#new_category").show();
		$("input#add_category").hide();
	}

	$(document)
			.ready(
					function() {
						if (typeof support_attachments != 'undefined') {
							$('#uploadify')
									.uploadify(
											{
												'swf' : getPath('WEB_PATH')
														+ 'Chamilo/Configuration/Plugin/jquery/uploadify/uploadify.swf',
												'uploader' : getPath('WEB_PATH')
														+ 'index.php',
												'auto' : true,
												'progressData' : 'percentage',
												'formData' : {
													'user_id' : getMemory('_uid'),
													'application' : 'Chamilo\\Core\\Repository',
													'go' : 'upload_image'
												},
												onUploadSuccess : function(
														file, data, response) {
													var ajaxResult = eval('('
															+ data + ')');

													$(
															'#attachments_search_field')
															.val(
																	ajaxResult.properties.title);
													$('#tbl_attachments')
															.trigger(
																	'update_search');

													$(
															'#lo_'
																	+ ajaxResult.properties.id)
															.trigger('activate');
												}
											});

						}

						$("div#new_category").hide();
						$("input#add_category").show();
						$("input#add_category").on('click', showNewCategory);
					});

});