(function($) {
	var segments = [];
	function showSegmentPreview(e, ui) {
		var segment = $(this);
		var segmentId = segment.attr('id');
		segmentId = segmentId.split('-');
		var videoId = segmentId[1];
		segmentId = segmentId[3];

		var previewImage = $("img#video-" + videoId + "-segment-preview-"
				+ segmentId);
		var timelineSegmentPreview = $("#video-" + videoId
				+ "-timeline-segment-preview");
		timelineSegmentPreview.css('background-image', 'url(' + previewImage
				.attr("src") + ')');
		timelineSegmentPreview.css('width', '160px');
		timelineSegmentPreview.css('height', '120px');

		var imageHeight = 120;
		var eps = 4;
		var tooltipWidth = timelineSegmentPreview.width();
		if (tooltipWidth == 0) {
			tooltipWidth = 160;
		}
		var segment0Left = parseInt($("td#video-" + videoId + "-segment-0")
				.offset().left
				+ eps);
		var segmentLastRight = parseInt($(
				"#video-" + videoId + "-segment-table").width())
				+ segment0Left - 3 * eps;

		var segmentLeft = $("#video-" + videoId + "-segment-" + segmentId)
				.offset().left;
		var segmentTop = $("#video-" + videoId + "-segment-" + segmentId)
				.offset().top
				- $("#header").height() - 6;
		var segmentWidth = $("#video-" + videoId + "-segment-" + segmentId)
				.width();
		var pos = segmentLeft + segmentWidth / 2 - tooltipWidth / 2;
		// Check overflow on left Side
		if (pos < segment0Left) {
			pos = segment0Left;
		}
		// Check overflow on right Side
		if ((pos + tooltipWidth) > segmentLastRight) {
			pos -= (pos + tooltipWidth) - segmentLastRight;
		}

		timelineSegmentPreview.css('left', pos + 'px');
		timelineSegmentPreview.css('top', segmentTop - (imageHeight) + 'px');

		timelineSegmentPreview.show();
		// $("img.timeline-segment-preview#timeline-segment-preview-" +
		// segmentId)
		// .show();

	}

	function play(videoId, segmentId) {
		var time = parseFloat(segments[videoId][segmentId]);
		var videoDisplay = $('#matterhorn_preview_video').css('display');
		if (videoDisplay == 'none') {
			$("#matterhorn-audio-" + videoId)[0].currentTime = time / 1000;
			$("#matterhorn-audio-" + videoId)[0].play();
		} else {
			$("#matterhorn-video-" + videoId)[0].currentTime = time / 1000;
			$("#matterhorn-video-" + videoId)[0].play();
		}
	}

	function playSegmentPreview() {
		var segment = $(this);
		var segmentId = segment.attr('id');
		segmentId = segmentId.split('-');
		var videoId = segmentId[1];
		segmentId = segmentId[3];

		play(videoId, segmentId);
	}

	function playVideoSegmentPreview() {
		var segment = $(this);
		var segmentId = segment.attr('id');
		segmentId = segmentId.split('-');
		var videoId = segmentId[1];
		segmentId = segmentId[4];

		play(videoId, segmentId);
	}

	function hideSegmentPreview() {
		var segmentId = $(this).attr('id').split('-');
		var videoId = segmentId[1];

		var timelineSegmentPreview = $("#video-" + videoId
				+ "-timeline-segment-preview");
		timelineSegmentPreview.hide();
	}

	function triggerShowSegmentPreview() {
		var imageId = $(this).attr('id').split('-');
		var videoId = imageId[1];
		var segmentId = imageId[4];

		$('#video-' + videoId + '-segment-' + segmentId).trigger('mouseover');
	}

	$(document).ready(function() {
		var segment = $("div.matterhorn");
		var segmentId = segment.attr('id');
		segmentId = segmentId.split('-');
		var videoId = segmentId[1];

		if (typeof (segments[videoId]) === 'undefined') {

			var ajaxUri = getPath('WEB_PATH') + 'index.php';

			var parameters = {
				'application' : 'Chamilo\\Core\\Repository\\ContentObject\\Matterhorn\\Ajax',
				'go' : 'segments',
				'content_object' : videoId
			};

			var response = $.ajax( {
				type : "POST",
				url : ajaxUri,
				data : parameters,
				async : false
			}).success(function(json) {
				segments[videoId] = json.properties.segments;
			});
		}

		// Events in timeline
			$("td.segment").live('mouseout', hideSegmentPreview);
			$("td.segment").live('mouseover', showSegmentPreview);
			$("td.segment").live('click', playSegmentPreview);

			// Events on preview images
			$("img.segment-preview").live('click', playVideoSegmentPreview);
			$("img.segment-preview").live('mouseover',
					triggerShowSegmentPreview);
			$("img.segment-preview").live('mouseout', hideSegmentPreview);

			$("div.scrollable-segments").scrollable( {
				'disabledClass' : ".scrollable-segments-disabled",
				'items' : ".segment-items",
				'next' : ".scrollable-segments-next",
				'prev' : ".scrollable-segments-prev"
			});
		});

})(jQuery);