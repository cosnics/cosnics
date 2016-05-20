(function($) {

	$(document).ready(
			function() {
				$("#matterhorn_preview_tabs").bind(
						"tabsshow",
						function(event, ui) {
							var videoDisplay = $('#matterhorn_preview_video')
									.css('display');
							if (videoDisplay == 'none') {
								$('video').each(function() {
									$(this)[0].pause();
								});
							} else {
								$('video').each(function() {
									$(this)[0].play();
								});
								$('#matterhorn_preview_video').append(
										$('.timeline-segment-preview'));
								$('#matterhorn_preview_video').append(
										$('.video-segment-table-border'));

							}

							var audioDisplay = $('#matterhorn_preview_audio')
									.css('display');
							if (audioDisplay == 'none') {
								$('audio').each(function() {
									$(this)[0].pause();
								});
							} else {
								$('audio').each(function() {
									$(this)[0].play();
								});
								$('#matterhorn_preview_audio').append(
										$('.timeline-segment-preview'));
								$('#matterhorn_preview_audio').append(
										$('.video-segment-table-border'));

							}
						});
			});

})(jQuery);