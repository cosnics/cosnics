(function ($) {
    $.fn
        .extend({
            calendarFrequency: function (options) {

                // Frequency options
                const frequency = {1: 'daily', 2: 'weekly', 3: 'weekdays', 4: 'biweekly', 5: 'monthly', 6: 'yearly'};
                const range = {1: 'none', 2: 'count', 3: 'until'};

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
                    let currentFrequencyValue = $(selectorFrequency, self).val();
                    $(selectorFrequencyOption, self).hide();
                    $(selectorFrequencyOption + '.frequency-' + frequency[currentFrequencyValue], self).show();
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

                        $('select[name="monthly[bymonthday][]"] option[value=' + $(this).data('value') +
                            ']').removeAttr(
                            'selected');
                    }
                    else {
                        $(this).addClass('btn-primary');
                        $('select[name="monthly[bymonthday][]"] option[value=' + $(this).data('value') + ']').attr(
                            'selected', 'selected');
                    }
                }

                function frequencyYearlyOptionSelected(event) {
                    event.preventDefault();

                    hideFrequencyYearlyOptions();
                    $('.' + $(this).data('option')).show();

                    $('.frequency-yearly-option a.btn').removeClass('btn-primary');
                    $('select[name="yearly[option]"] option').removeAttr('selected');

                    $(this).addClass('btn-primary');
                    $('select[name="yearly[option]"] option[value=' + $(this).data('value') + ']').attr(
                        'selected', 'selected');
                }

                function frequencyYearlyByDaySelected(event) {
                    $('.frequency-yearly-byday-day a.btn').removeClass('btn-primary');
                    $('select[name="yearly[byday][day]"] option').removeAttr('selected');

                    $(this).addClass('btn-primary');
                    $('select[name="yearly[byday][day]"] option[value=' + $(this).data('value') + ']').attr(
                        'selected', 'selected');
                }

                function frequencyYearlyBymonthdaySelected(event) {
                    event.preventDefault();

                    if ($(this).hasClass('btn-primary')) {
                        $(this).removeClass('btn-primary');

                        $('select[name="yearly[bymonthday][]"] option[value=' + $(this).data('value') + ']').removeAttr(
                            'selected');
                    }
                    else {
                        $(this).addClass('btn-primary');
                        $('select[name="yearly[bymonthday][]"] option[value=' + $(this).data('value') + ']').attr(
                            'selected', 'selected');
                    }
                }

                function hideFrequencyRange() {
                    $('.frequency-range-count', self).hide();
                    $('.frequency-range-until', self).hide();
                }

                function hideFrequencyMonthlyOptions() {
                    $('.frequency-monthly-byday', self).hide();
                    $('.frequency-monthly-bymonthday', self).hide();
                }

                function hideFrequencyYearlyOptions() {
                    $('.frequency-yearly-byday', self).hide();
                    $('.frequency-yearly-bymonthday', self).hide();
                }

                function addActions() {
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

                function setDefaults() {
                    let currentFrequencyValue = $(selectorFrequency, self).val();
                    let currentFrequencyValueName = frequency[currentFrequencyValue];
                    $(selectorFrequencyOption + '.frequency-' + frequency[currentFrequencyValue], self).show();

                    if (currentFrequencyValueName === 'weekly') {
                        let currentWeeklyByDays = getSelectionValue('weekly[byday][]', true);

                        if (currentWeeklyByDays) {
                            currentWeeklyByDays.forEach(function (value) {
                                $('.frequency-weekly-byday a.btn[data-value="' + value + '"]', self).addClass(
                                    'btn-primary');
                            });
                        }
                    }

                    if (currentFrequencyValueName === 'monthly') {
                        let currentMonthlyOption = getSelectionValue('monthly[option]', false);
                        $('.frequency-monthly-option a.btn[data-value="' + currentMonthlyOption + '"]', self).addClass(
                            'btn-primary');

                        $('.frequency-monthly-' + currentMonthlyOption).show();

                        let currentMonthlyByDay = getSelectionValue('monthly[byday][day]', true);

                        if (currentMonthlyByDay) {
                            $(
                                '.frequency-monthly-byday-day a.btn[data-value="' + currentMonthlyByDay + '"]',
                                self
                            ).addClass(
                                'btn-primary');
                        }

                        let currentMonthlyByMonthDays = getSelectionValue('monthly[bymonthday][]', true);

                        if (currentMonthlyByMonthDays) {
                            currentMonthlyByMonthDays.forEach(function (value) {
                                $('.frequency-monthly-bymonthday a.btn[data-value="' + value + '"]', self).addClass(
                                    'btn-primary');
                            });
                        }
                    }

                    if (currentFrequencyValueName === 'yearly') {
                        let currentYearlyOption = getSelectionValue('yearly[option]', false);
                        $('.frequency-yearly-option a.btn[data-value="' + currentYearlyOption + '"]', self).addClass(
                            'btn-primary');

                        $('.frequency-yearly-' + currentYearlyOption).show();

                        let currentYearlyByDay = getSelectionValue('yearly[byday][day]', true);

                        if (currentYearlyByDay) {
                            $(
                                '.frequency-yearly-byday-day a.btn[data-value="' + currentYearlyByDay + '"]',
                                self
                            ).addClass(
                                'btn-primary');
                        }

                        let currentYearlyByMonthDays = getSelectionValue('yearly[bymonthday][]', true);

                        if (currentYearlyByMonthDays) {
                            currentYearlyByMonthDays.forEach(function (value) {
                                $('.frequency-yearly-bymonthday a.btn[data-value="' + value + '"]', self).addClass(
                                    'btn-primary');
                            });
                        }
                    }

                    let currentFrequencyRangeValue = getSelectionValue('range', false);

                    if (currentFrequencyRangeValue) {
                        let currentFrequencyRangeValueName = range[currentFrequencyRangeValue];
                        $('.frequency-range a.btn[data-value="' + currentFrequencyRangeValue + '"]', self).addClass(
                            'btn-primary');

                        $('.frequency-range-' + currentFrequencyRangeValueName).show();
                    }
                }

                function getSelectionValue(selectName) {
                    return $('select[name="' + selectName + '"]', self).val();
                }

                /**
                 * Initializes the calendar Frequency
                 */
                function init() {
                    hideFrequencyMonthlyOptions();
                    hideFrequencyYearlyOptions();
                    hideFrequencyRange();
                    $('.frequency-option', self).hide();

                    addActions();
                    setDefaults();
                }

                return this.each(init);
            }
        });
})(jQuery);
