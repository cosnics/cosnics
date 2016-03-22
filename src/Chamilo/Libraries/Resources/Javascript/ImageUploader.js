$(function() {

    function loadImage(imageUploaderData) {
        var imageUploaderName = imageUploaderData.prop('name');
        var imageUploaderContainer = $('#image-uploader-' + imageUploaderName);

        if (imageUploaderData.prop('value')) {
        $('.image-uploader-preview', imageUploaderContainer).prop('src', imageUploaderData.prop('value'));
        }
    }

    function readImage() {
        var imageUploaderFile = $(this);
        var imageUploaderContainer = $('#image-uploader-' + imageUploaderFile.data('element'));

        if (this.files && this.files[0]) {
        var FR = new FileReader();
        FR.onload = function(e) {
            $('.image-uploader-preview', imageUploaderContainer).attr("src", e.target.result);
            $('#' + imageUploaderFile.data('element')).prop('value', e.target.result);
        };
        FR.readAsDataURL(this.files[0]);
        }
    }

    $(document).ready(function() {
        $('.image-uploader-data').each(function(index) {
            loadImage($(this));
        });

        $(".image-uploader-file").change(readImage);
    });

});