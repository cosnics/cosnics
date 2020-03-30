(function ($) {
    function addSlider() {
        var selectElement = $(this);
        var id = selectElement.attr("name");
        var minValue = parseInt($('option:first', selectElement).val());
        var maxValue = parseInt($('option:last', selectElement).val());
        var sliderName = 'slider_' + id;

        var slider = $(
            '<div id="' + sliderName + '"><div class="ui-slider-handle" id="slider_caption_' + id + '" /></div>');
        selectElement.after(slider);
        selectElement.toggle();

        var sliderSelector = $('#' + sliderName);
        var handle = $('.ui-slider-handle', slider);

        sliderSelector.slider({
            orientation: 'horizontal',
            min: minValue,
            max: maxValue,
            change: function () {
                var value = sliderSelector.slider('value');
                selectElement.val(value);
                handle.text(value);
            },
            create: function () {
                handle.text($(this).slider("value"));
            },
            slide: function (event, ui) {
                handle.text(ui.value);
            }
        });
    }

    $(document).ready(function () {
        $(".rating-slider").each(addSlider);
    });

})(jQuery);