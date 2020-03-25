var day;
var month;
var year;
var hour;
var minute;
var second;
var clock_set = 0;

/**
 * Opens calendar window.
 *
 * @param   string      form name
 * @param   string      field name
 */
function openCalendar(form, field) {
    formblock = document.getElementById(form);
    forminputs = formblock.getElementsByTagName('select');
    var datevalues = [];
    var dateindex = 0;
    for (i = 0; i < forminputs.length; i++) {
        // regex here to check name attribute
        var regex = new RegExp(field, "i");
        if (regex.test(forminputs[i].getAttribute('name'))) {
            datevalues[dateindex++] = forminputs[i].value;
        }
    }

    var path = getPath('WEB_PATH') + 'index.php?application=Chamilo\\Libraries\\Ajax&go=CalendarPopup';
    window.open(path, 'calendar', 'location=no,status=no,resizable=no,width=300,height=300');
    day = datevalues[0];
    month = datevalues[1];
    year = datevalues[2];
    month--;
    formName = form;
    fieldName = field;
}

/**
 * Formats number to two digits.
 *
 * @param   int number to format.
 */
function formatNum2(i, valtype) {
    f = (i < 10 ? '0' : '') + i;
    if (valtype && valtype !== '') {
        switch (valtype) {
            case 'month':
                f = (f > 12 ? 12 : f);
                break;

            case 'day':
                f = (f > 31 ? 31 : f);
                break;
        }
    }

    return f;
}

/**
 * Formats number to four digits.
 *
 * @param   int number to format.
 */
function formatNum4(i) {
    return (i < 1000 ? i < 100 ? i < 10 ? '000' : '00' : '0' : '') + i;
}

/**
 * Initializes calendar window.
 */
function initCalendar(day_of_month_identifier) {
    if (!year && !month && !day) {
        max_year = window.opener.max_year;
        day = window.opener.day;
        month = window.opener.month;
        year = window.opener.year;
        if (isNaN(year) || isNaN(month) || isNaN(day) || day === 0) {
            dt = new Date();
            year = dt.getFullYear();
            month = dt.getMonth();
            day = dt.getDate();
        }
    }
    else {
        /* Moving in calendar */
        if (month > 11) {
            month = 0;
            year++;
        }
        if (month < 0) {
            month = 11;
            year--;
        }
    }

    if (document.getElementById) {
        cnt = document.getElementById("calendar_data");
    }
    else if (document.all) {
        cnt = document.all["calendar_data"];
    }

    cnt.innerHTML = "";

    str = "";

    //heading table
    str += '<table class="table"><tr><th class="monthyear" width="50%">';
    str += '<a href="javascript:month--; initCalendar(' + day_of_month_identifier + ');">&laquo;</a> ';
    str += month_names[month];

    if (year < max_year || (year == max_year && month < 11)) {
        str += ' <a href="javascript:month++; initCalendar(' + day_of_month_identifier + ');">&raquo;</a>';
    }

    str += '</th><th class="monthyear" width="50%">';
    str += '<a href="javascript:year--; initCalendar(' + day_of_month_identifier + ');">&laquo;</a> ';
    str += year;

    if (year < max_year) {
        str += ' <a href="javascript:year++; initCalendar(' + day_of_month_identifier + ');">&raquo;</a>';
    }

    str += '</th></tr></table>';
    str += '<table class="table table-striped" style="border-top: 0px;"><tr>';

    for (i = 0; i < 7; i++) {
        str += "<th class='daynames'>" + day_names[i] + "</th>";
    }

    str += "</tr>";

    var firstDay = new Date(year, month, day_of_month_identifier).getDay();
    var lastDay = new Date(year, month + 1, 0).getDate();

    str += "<tr>";

    dayInWeek = 0;

    for (i = 0; i < firstDay; i++) {
        str += "<td class=\"disabled_month\">&nbsp;</td>";
        dayInWeek++;
    }

    for (i = 1; i <= lastDay; i++) {
        if (dayInWeek == 7) {
            str += "</tr><tr>";
            dayInWeek = 0;
        }

        dispmonth = 1 + month;
        actVal = formatNum4(year) + "-" + formatNum2(dispmonth, 'month') + "-" + formatNum2(i, 'day');

        if (i == day) {
            style = ' class="highlight"';
        }
        else {
            if (dayInWeek == 0 || dayInWeek == 6) {
                style = ' class="weekend"';
            }
            else {
                style = '';
            }
        }

        str +=
            "<td" + style + "><a class=\"dateselector\" href=\"javascript:returnDate(" + i + "," + month + "," + year +
            ");\">" + i + "</a></td>";
        dayInWeek++;
    }

    for (i = dayInWeek; i < 7; i++) {
        str += "<td class=\"disabled_month\">&nbsp;</td>";
    }

    str += "</tr></table>";

    cnt.innerHTML = str;
}

/** * Returns date from calendar. * * @param string date text */

function returnDate(d, m, y) {
    formblock = window.opener.document.getElementById(window.opener.formName);
    forminputs = formblock.getElementsByTagName('select');
    var datevalues = [];
    var dateindex = 0;

    for (i = 0; i < forminputs.length; i++) {
        var regex = new RegExp(window.opener.fieldName, "i");
        if (regex.test(forminputs[i].getAttribute('name'))) {
            datevalues[dateindex++] = forminputs[i];
        }
    }

    datevalues[0].selectedIndex = (d - 1);
    datevalues[1].selectedIndex = m;
    datevalues[2].selectedIndex =
        datevalues[2].length - (datevalues[2].options[datevalues[2].length - 1].value - y) - 1; // selectedIndex = numberOfItems - (maxYear - selectedYear) - 1;
    window.close();
    window.opener.start_time_changed();
}
