/**
 * $Id: learning_path_item.js 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.javascript
 */
/**
 * @author Sven Vanpoucke
 */
(function($) {
	$(window).bind('beforeunload', function(e) {
		var ajaxUri = getPath('WEB_PATH') + 'index.php';

		var parameters = {
			'application' : trackerContext,
			'go' : 'leave_item',
			'tracker_id' : trackerId
		};

		var response = $.ajax({
			type : "POST",
			url : ajaxUri,
			data : parameters,
			async : false
		});
	});
})(jQuery);