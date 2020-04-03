(function ($) {
    $.fn
        .extend({
            calendarFrequency: function (options) {

                // Frequency options
                const frequency = {1: 'daily', 2: 'weekly', 3: 'weekdays', 4: 'biweekly', 5: 'monthly', 6: 'yearly'};

                // Selectors
                const selectorFrequency = 'select[name="frequency"]';
                const selectorFrequencyOption = '.frequency-option';

                // Settings list and the default values
                let defaults = {
                    defaultValues: null,
                };

                // The calendar frequency element
                const self = this;

                function frequencySelected(event) {
                    let currentFrequencyValue = $(selectorFrequency).val();
                    $(selectorFrequencyOption).not(frequency[currentFrequencyValue]).hide();
                    $(selectorFrequencyOption + '.frequency-' + frequency[currentFrequencyValue]).show();
                }

                function frequencyRangeSelected(event) {
                    event.preventDefault();

                    hideFrequencyRange();
                    $('.' + $(this).data('range')).show();

                    $('.frequency-range a.btn').removeClass('btn-primary');
                    $(this).addClass('btn-primary');
                }

                function frequencyWeeklyByDaySelected(event) {
                    event.preventDefault();

                    if ($(this).hasClass('btn-primary')) {
                        $(this).removeClass('btn-primary');

                        $('select[name="weekly[byday][]"] option[value=' + $(this).data('value') + ']').removeAttr(
                            'selected');
                    }
                    else {
                        $(this).addClass('btn-primary');

                        $('select[name="weekly[byday][]"] option[value=' + $(this).data('value') + ']').attr(
                            'selected', 'selected');
                    }
                }

                function frequencyMonthlyOptionSelected(event) {
                    event.preventDefault();

                    hideFrequencyMonthlyOptions();
                    $('.' + $(this).data('option')).show();

                    $('.frequency-monthly-option a.btn').removeClass('btn-primary');
                    $('select[name="monthly[option]"] option').removeAttr('selected');

                    $(this).addClass('btn-primary');
                    $('select[name="monthly[option]"] option[value=' + $(this).data('value') + ']').attr(
                        'selected', 'selected');
                }

                function frequencyMonthlyByDaySelected(event) {
                    $('.frequency-monthly-byday-day a.btn').removeClass('btn-primary');
                    $('select[name="monthly[byday][day]"] option').removeAttr('selected');

                    $(this).addClass('btn-primary');
                    $('select[name="monthly[byday][day]"] option[value=' + $(this).data('value') + ']').attr(
                        'selected', 'selected');
                }

                function frequencyMonthlyBymonthdaySelected(event) {
                    event.preventDefault();

                    if ($(this).hasClass('btn-primary')) {
                        $(this).removeClass('btn-primary');
                    }
                    else {
                        $(this).addClass('btn-primary');
                    }
                }

                function frequencyYearlyOptionSelected(event) {
                    event.preventDefault();

                    hideFrequencyYearlyOptions();
                    $('.' + $(this).data('option')).show();

                    $('.frequency-yearly-option a.btn').removeClass('btn-primary');
                    $(this).addClass('btn-primary');
                }

                function frequencyYearlyByDaySelected(event) {
                    $('.frequency-yearly-byday-day a.btn').removeClass('btn-primary');
                    $(this).addClass('btn-primary');
                }

                function frequencyYearlyBymonthdaySelected(event) {
                    event.preventDefault();

                    if ($(this).hasClass('btn-primary')) {
                        $(this).removeClass('btn-primary');
                    }
                    else {
                        $(this).addClass('btn-primary');
                    }
                }

                function hideFrequencyRange() {
                    $('.frequency-range-count').hide();
                    $('.frequency-range-until').hide();
                }

                function hideFrequencyMonthlyOptions() {
                    $('.frequency-monthly-byday').hide();
                    $('.frequency-monthly-bymonthday').hide();
                }

                function hideFrequencyYearlyOptions() {
                    $('.frequency-yearly-byday').hide();
                    $('.frequency-yearly-bymonthday').hide();
                }

                /**
                 * Initializes the calendar Frequency
                 */
                function init() {
                    hideFrequencyMonthlyOptions();
                    hideFrequencyYearlyOptions();
                    hideFrequencyRange();
                    $('.frequency-option').hide();

                    $(self).on('change', selectorFrequency, frequencySelected);

                    $(self).on('click', '.frequency-range a.btn', frequencyRangeSelected);
                    $(self).on('click', '.frequency-weekly-byday a.btn', frequencyWeeklyByDaySelected);

                    $(self).on('click', '.frequency-monthly-option a.btn', frequencyMonthlyOptionSelected);
                    $(self).on('click', '.frequency-monthly-byday-day a.btn', frequencyMonthlyByDaySelected);
                    $(self).on('click', '.frequency-monthly-bymonthday a.btn', frequencyMonthlyBymonthdaySelected);

                    $(self).on('click', '.frequency-yearly-option a.btn', frequencyYearlyOptionSelected);
                    $(self).on('click', '.frequency-yearly-byday-day a.btn', frequencyYearlyByDaySelected);
                    $(self).on('click', '.frequency-yearly-bymonthday a.btn', frequencyYearlyBymonthdaySelected);
                }

                return this.each(init);
            }
        });
})(jQuery);
