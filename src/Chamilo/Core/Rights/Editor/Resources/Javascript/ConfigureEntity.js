( function($) {

	var collapseItem = function(e) {
		e.preventDefault();

		var image = $("span", this);
		var originalClass = image.attr("class");
		var id = $(this).parent().attr('id');

		image.attr("class", "loadingMini");

        var ajaxUri = getPath('WEB_PATH') + 'index.php';

		var result = doAjaxPost(ajaxUri, {
			'application' : 'Chamilo\\Core\\Rights\\Editor\\Ajax',
			'go' : 'entity_right_location',
			'rights' : id,
            'locations': locations
		});

        result = eval('(' + result + ')');
		var success = result.properties.success;

        if(success)
        {
            var classResult = doAjaxPost(ajaxUri, {
            	'application' : 'Chamilo\\Core\\Rights\\Editor\\Ajax',
                'go' : 'entity_right_location_class',
                'rights' : id,
                'locations': locations
            });

            classResult = eval('(' + classResult + ')');
            var newClass = classResult.properties.new_class;

            if(newClass)
            {
                image.attr("class", newClass);
            }
        }
        else
        {
            image.attr("class", originalClass);
            alert(getTranslation('Failure', 'rights'));
        }
	};

	function bindIcons() {
		$("a.setRight").unbind();
		$("a.setRight").bind('click', collapseItem);
	}

	$(document).ready( function() {
		bindIcons();
	});

})(jQuery);