$(function() {

	function reset(evt, ui) {
		setTimeout(function() {
			$('.iphone').setiPhoneCourseType();
			$('.viewablecheckbox').setViewableStyle();
			$("input[name=creation_groups_option]").init_everybody();
		}, 30);
	}

	$(document).ready(function() {
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setViewableStyle();
		$(document).on('click', ':reset', reset);
		$("input[name=creation_groups_option]").bind_everybody();
		$("input[name=creation_groups_option]").init_everybody();
	});

});

(function($) {
	$.iphoneCourseType = {
		defaults : {
			checkedLabel : 'ON',
			uncheckedLabel : 'OFF',
			background : '#fff'
		}
	}

	$.fn.setiPhoneCourseType = function() {
		return this
				.each(function() {
					var elem = $(this);

					if (!elem.is(':checkbox'))
						return;

					var handle = elem.siblings('.handle'), handlebg = handle
							.children('.bg'), offlabel = elem.siblings('.off'), onlabel = elem
							.siblings('.on'), container = elem
							.parent('.binary_checkbox'), rightside = container
							.outerWidth() - 39;
					tool = elem.attr('class').split(' ').slice(-1);
					defaultimage = $('.' + tool + '_elementdefault');
					image = $('img.' + tool);
					src = image.attr('src');

					container.click(function() {
						var is_onstate = (handle.position().left <= 0);
						tool = elem.attr('class').split(' ').slice(-1);
						defaultimage = $('.' + tool + '_elementdefault');
						image = $('img.' + tool);
						src = image.attr('src');
						imagesrc = '';
						imagesrcdisabled = '';

						if (is_onstate) {
							src = src.replace('Na.png', '.png');
							image.attr('src', src);
							defaultimage.css('display', 'inline');
						} else {
							src = src.replace('.png', 'Na.png');
							image.attr('src', src);
							defaultimage.css('display', 'none');
						}

						return false;
					});

					if (elem.is(':checked')) {
						offlabel.css({
							opacity : 0
						});
						onlabel.css({
							opacity : 1
						});
						handle.css({
							left : rightside
						});
						handlebg.css({
							left : 34
						});

						src = src.replace('Na.png', '.png');
						image.attr('src', src);

						defaultimage.css('display', 'inline');
					} else {
						offlabel.css({
							opacity : 1
						});
						onlabel.css({
							opacity : 0
						});
						handle.css({
							left : 0
						});
						handlebg.css({
							left : 0
						});

						src = src.replace('.png', 'Na.png');
						image.attr('src', src);

						defaultimage.css('display', 'none');
					}
				});
	};
})(jQuery);