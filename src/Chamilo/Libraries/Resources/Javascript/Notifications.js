/*global $, window, handleResize, getWindowHeight, reinit, document, jQuery, destroy, setTimeout, clearTimeout */

$(function() {
	var windowHeight = getWindowHeight(), resizeTimer = null;
	var cdaMatchRegExp = /\[CDA context=\{([^"]*?)\}\](.*?)\[\/CDA\]/gi;
	var attributeReplacement = "$2";
	var replacement = attributeReplacement
			+ " <a class=\"cda_link\" href=\"http://translate.chamilo.org/translate.php?context=$1&variable=$2\"><img src=\"Configuration/Resources/Images/Aqua/Action/Translate.png\" style=\"width: 7px; height: 7px;\" /></a>";

	function hideMessages() {
		setTimeout("$('.normal-message').fadeOut(500);", 5000);
		setTimeout("$('.error-message').fadeOut(500);", 1000);
		setTimeout("$('.warning-message').fadeOut(500);", 15000);
	}

	function addClosers() {
		// Normal messages
		$(".normal-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_normal_message');
		});

		// Warning messages
		$(".warning-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_warning_message');
		});

		// Error messages
		$(".error-message").bind('mouseenter', function(e) {
			$("#closeMessage", this).attr('class', 'close_error_message');
		});

		// General functionality
		$(".normal-message, .warning_message, .error_message").bind(
				'mouseleave', function(e) {
					$("#closeMessage", this).attr('class', 'close_message');
				});
		$("[id|=closeMessage]").bind('click', function(e) {
			$(this).parent().fadeOut(500);
		});
	}

	function placeFooter() {
		var htmlHeight = $("body").outerHeight();

		if (htmlHeight > windowHeight) {
			$("#footer").css("position", "static");
			$("#footer").css("bottom", "");
			$("#footer").css("left", "");
			$("#footer").css("right", "");

			$("#main").css("margin-bottom", "0px");
		} else {
			$("#footer").css("position", "fixed");
			$("#footer").css("bottom", "0px");
			$("#footer").css("left", "0px");
			$("#footer").css("right", "0px");

			$("#main").css("margin-bottom", "30px");
		}

		$(window).bind('resize', handleResize);
	}

	function handleResize() {
		var currentHeight = getWindowHeight();

		if (resizeTimer) {
			clearTimeout(resizeTimer);
		}

		if (windowHeight !== currentHeight) {
			reinit();
		}
	}

	function getWindowHeight() {
		if (window.innerHeight) {
			return window.innerHeight;
		} else if (document.documentElement) {
			return document.documentElement.offsetHeight;
		}
	}

	function reinit() {
		windowHeight = getWindowHeight();
		destroy();
		placeFooter();
	}

	function destroy() {
		$(window).unbind('resize', handleResize);
	}

	function showColorTip(e, ui) {
		e.preventDefault();

		var eventsContent = '';
		var element = $(this);

		$("div.event", element.parent()).each(
				function(i) {
					eventsContent += $('<div>').append($(this).clone().show())
							.remove().html();
				});

		var tip = $('<span class="colorTip">'
				+ eventsContent
				+ '<span class="pointyTipShadow"></span><span class="pointyTip"></span></span>');

		element.append(tip).addClass('colorTipContainer');
		element.addClass('blue');

		tip.fadeIn('fast');
		tip.css('margin-left', -tip.outerWidth() / 2);
		tip.css('margin-top', -tip.outerHeight() - 7);

		// alert($(window).height());
	}

	function hideColorTip(e, ui) {
		e.preventDefault();
		$('span.colorTip', $(this)).fadeOut().remove();

	}

	function showMessage(e, ui) {
		if ($(this).hasClass("notification_mini")) {
			$(this).removeClass("notification_mini");
		}
		else
			$(this).addClass("notification_mini");
		
	}

	$(document)
			.ready(
					function() {
						addClosers();
						$(".notification").addClass("notification_mini");
						$(document).on('click', ".notification", showMessage);
						$(".navbar").tabula({
							cycle : false,
							follow : false,
							nextButton : ">>",
							prevButton : "<<"
						});

						$("#datepicker").datepicker();

						// $("body *").replaceText('[=', "" );
						// $("body *").replaceText(cdaMatchRegExpInAttribute,
						// "$2"
						// );
//						$("body *")
//								.replaceText(
//										cdaMatchRegExp,
//										"$2 <a class=\"cda_link\" href=\"http://translate.chamilo.org/translate.php?context=$1&variable=$2\"><img src=\""
//												+ getPath('REL_PATH')
//												+ "configuration/resources/images/aqua/action_translate.png\" style=\"width: 7px; height: 7px;\" /></a>",
//										"$2");

						// hideMessages();
						// placeFooter();
						// $('iframe:not(.processed)').TextAreaResizer();

						$("div.event_marker").hover(showColorTip, hideColorTip);

					});

});