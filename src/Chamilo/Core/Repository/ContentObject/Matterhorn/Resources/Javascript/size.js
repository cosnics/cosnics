(function($) {
	var videoSize;
	function onresize() {
		var videoElements = $(".matterhorn-video");
		videoElements.each(function(index) {
			$(this).width(videoSize[$(this).attr('id')]);

			var videoId = $(this).attr('id');
			videoId = videoId.split('-');
			videoId = videoId[2];

			var elementWidth = $(this).width();
			var videoWidth = Math
					.round($(this).parents(".description").width() * 0.9);

			$(".matterhorn-audio").width(videoSize[$(this).attr('id')]);

			if (elementWidth > videoWidth) {
				$(this).width(videoWidth);
				$(".matterhorn-audio").width(videoWidth);
			} else if (elementWidth < videoWidth) {
				if (elementWidth < videoSize[$(this).attr('id')]) {
					if (videoSize[$(this).attr('id')] <= videoWidth) {
						$(this).width(videoWidth);
						$(".matterhorn-audio").width(videoWidth);
					} else {
						$(this).width(videoSize[$(this).attr('id')]);
						$(".matterhorn-audio").width(
								videoSize[$(this).attr('id')]);
					}
				}
			}
		});

		var segmentElements = $(".video-segment-table");
		segmentElements.each(function(index) {

			var videoSegmentId = $(this).attr('id');
			videoSegmentId = videoSegmentId.split('-');
			var videoId = videoSegmentId[1];
			var segmentWidth = $("#matterhorn-video-" + videoId).width();

			$(this).width(segmentWidth);
			$(this).parent().width(segmentWidth);
		});
	}

	$(document).ready(function() {
		var video = $(".matterhorn-video");
		var videoIds = [];
		video.each(function(index) {
			videoIds.push($(this).attr('id'));
		});

		var ajaxUri = getPath('WEB_PATH') + 'index.php';

		var parameters = {
			'application' : 'Chamilo\\Core\\Repository\\ContentObject\\Matterhorn\\Ajax',
			'go' : 'size',
			'video_ids' : videoIds,
			'quality' : $.query.get('quality')
		};
		
		if (videoIds.length > 0) {
			var response = $.ajax( {
				type : "POST",
				url : ajaxUri,
				data : parameters,
				async : false
			}).success(function(json) {
				videoSize = json.properties.size;
				$(window).resize(onresize);
				onresize();
				
			});
		}
	});

})(jQuery);