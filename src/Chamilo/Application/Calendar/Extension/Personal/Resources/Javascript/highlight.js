$(function() {

	function switchVisibility(calendarEvent) {
		var ajaxUri = getPath('WEB_PATH') + 'index.php';

		var parameters = {
			'application' : 'Chamilo\\Application\\Calendar\\Ajax',
			'go' : 'calendar_event_visibility',
			'source' : $(calendarEvent).data('source')
		};

		var response = $.ajax({
			type : "POST",
			url : ajaxUri,
			data : parameters,
			async : false
		}).success(
				function(json) {
					if (json.result_code == 200) {
						var typeClass = $(calendarEvent).attr('class');
						var typeClasses = typeClass.split(" ");

						var eventBox = $(
								'.normal_calendar .calendar_table .'
										+ typeClasses[1]).parent().toggleClass(
								'event-hidden');

						$(calendarEvent).toggleClass('disabled');
						$(calendarEvent).toggleClass(
								'event-source-identifier-faded');
					}
				});
	}

	$(document).ready(
			function() {

				var originalColor = $(
						'.event-legend-container .event-source-identifier')
						.first().parent().css("border-top-color");

				$(document).on('click',
						'.event-legend-container .event-source-identifier',
						function(event) {
							switchVisibility(this);
						});
			});
});