(function($) {

    $(document).ready(function() {
        window.opener.addMetadataValue(elementIdentifier, selectedVocabularyItems);
        window.close();
    });

})(jQuery);
