(function($) {

	$(document).ready(
			function() {
				$('.faq-item-title').click(function() {
					if ($(this).hasClass('faq-item-title-description')) {
						$(this).removeClass('faq-item-title-description');
						$('div.description', $(this).parent()).hide();
					} else {
						$(this).addClass('faq-item-title-description');
						$('div.description', $(this).parent()).show();
					}
				});

				var hash = window.location.hash;
				if (hash) {
					$(hash + ' >li>.ui-tabs>.admin_tab>.faq-item-title').addClass(
							'faq-item-title-description');
					$(hash + ' >li>.ui-tabs>.admin_tab>div.description').show();
				}
			});

})(jQuery);