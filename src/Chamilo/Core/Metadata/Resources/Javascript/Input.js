(function($) {

    window.addMetadataValue = function(currentElementIdentifier, selectedVocabularyItems) {
        $.each(selectedVocabularyItems, function(index, value) {
            $('#' + currentElementIdentifier).tagsinput('add', value);
        });
    };

    $(document).ready(function() {

        var ajaxUri = getPath('WEB_PATH') + 'index.php';

        var parameters = {
            'application' : 'Chamilo\\Core\\Metadata\\Vocabulary\\Ajax',
            'go' : 'Vocabulary'
        };

        $('.metadata-input').each(function(index) {
            var currentElement = $(this);

            currentElement.tagsinput({
                itemValue : 'id',
                itemText : 'value',
                freeInput : currentElement.hasClass('metadata-input-new') ? true : false,
                freeElementSelector : '#new-' + currentElement.attr('id'),
                maxTags : currentElement.data('elementValueLimit'),
                typeahead : {
                    source : function(query) {

                        var options = [];

                        parameters.schemaId = currentElement.data('schemaId');
                        parameters.schemaInstanceId = currentElement.data('schemaInstanceId');
                        parameters.elementId = currentElement.data('elementId');

                        var request = $.ajax({
                            type : "POST",
                            url : ajaxUri,
                            data : parameters,
                            dataType : 'json'
                        });

                        return request.done(function(data, textStatus, jqXHR) {
                            return data;
                        });
                    }
                }
            });
        });
    });

})(jQuery);
