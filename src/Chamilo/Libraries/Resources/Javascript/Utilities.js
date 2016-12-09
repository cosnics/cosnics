var translations = [];
var paths = [];
var settings = [];
var theme;
var ajaxUri = getPath('WEB_PATH') + 'index.php';

// Get a platform setting
function getPlatformSetting(variable, application)
{
    if (typeof (settings[application]) == 'undefined'
            || (typeof (settings[application]) !== 'undefined' && typeof (settings[application][variable]) == 'undefined'))
    {
        
        if (typeof (settings[application]) == 'undefined')
        {
            settings[application] = [];
        }
        
        settings[application][variable] = getUtilities('platform_setting', {
            variable : variable,
            context : application
        });
    }
    
    return settings[application][variable];
}

// Get a translation
function getTranslation(string, parameters, context)
{
    if (typeof (translations[context]) == 'undefined'
            || (typeof (translations[context]) !== 'undefined' && typeof (translations[context][string]) == 'undefined'))
    {
        
        if (typeof (translations[context]) == 'undefined')
        {
            translations[context] = [];
        }
        
        translations[context][string] = getUtilities('translation', {
            string : string,
            parameters : parameters,
            context : context
        });
    }
    
    return translations[context][string];
}

// Get a platform path
function getPath(path)
{
    // we should avoid doing chatty calls. When possible needed data should be
    // loaded during page creation.
    
    if (path.toUpperCase() == 'WEB_PATH' && window.rootWebPath !== undefined)
    {
        return window.rootWebPath;
    }
    
    if (typeof (paths[path]) == 'undefined')
    {
        paths[path] = getUtilities('path', {
            path : path
        });
    }
    
    return paths[path];
}

// Get the current theme
function getTheme()
{
    if (typeof (theme) == 'undefined')
    {
        theme = getUtilities('theme');
    }
    
    return theme;
}

// Get a memorized variable
function getMemory(variable)
{
    return getUtilities('memory', {
        action : 'get',
        variable : variable
    });
}

// Set a memorized variable
function setMemory(variable, value)
{
    getUtilities('memory', {
        action : 'set',
        variable : variable,
        value : value
    });
}

// Clear a memorized variable
function clearMemory(variable)
{
    getUtilities('memory', {
        action : 'clear',
        variable : variable
    });
}

// General function to retrieve and process utilities-calls.
function getUtilities(type, parameters)
{
    var result;
    
    if (typeof parameters == "undefined")
    {
        parameters = new Object();
    }
    
    parameters.type = type;
    parameters.application = 'Chamilo\\Libraries\\Ajax';
    parameters.go = 'utilities';
    
    var response = $.ajax({
        type : "POST",
        url : ajaxUri,
        data : parameters,
        async : false
    }).success(function(json)
    {
        result = json.properties.result;
    });
    
    return result;
}

// Wrapper for an Ajax POST
function doAjaxPost(url, parameters)
{
    return doAjax("POST", url, parameters);
}

// Wrapper for an Ajax GET
function doAjaxGet(url, parameters)
{
    return doAjax("GET", url, parameters);
}

// Execute an Ajax postback
function doAjax(type, url, parameters)
{
    if (typeof parameters == "undefined")
    {
        parameters = new Object();
    }
    
    var response = $.ajax({
        type : type,
        url : url,
        dataType : "json",
        data : parameters,
        async : false
    }).responseText;
    
    return response;
}

// Return an HTML Editor
function renderHtmlEditor(editorName, editorOptions, editorLabel, editorAttributes)
{
    var defaults = {
        "name" : '',
        "label" : '',
        "options" : $.json.serialize({}),
        "attributes" : $.json.serialize({})
    };
    
    var parameters = new Object();
    parameters.name = editorName;
    
    if (typeof editorOptions != "undefined")
    {
        parameters.options = $.json.serialize(editorOptions);
    }
    
    if (typeof editorAttributes != "undefined")
    {
        parameters.attributes = $.json.serialize(editorAttributes);
    }
    
    if (typeof editorLabel != "undefined")
    {
        parameters.label = editorLabel;
    }
    
    parameters.application = 'Chamilo\\Libraries\\Ajax';
    parameters.go = 'HtmlEditorInstance';
    
    var ajaxParameters = $.extend(defaults, parameters);
    
    ajaxUri = getPath('WEB_PATH') + 'index.php';
    
    var result = doAjaxPost(ajaxUri, ajaxParameters);
    
    return result;
}

// Destroy an HTML Editor
function destroyHtmlEditor(editorName)
{
    if (typeof CKEDITOR != 'undefined')
    {
        $('textarea.html_editor[name=\'' + editorName + '\']').ckeditorGet().destroy();
    }
    
    if (typeof tinyMCE != 'undefined')
    {
        $('textarea.html_editor[name=\'' + editorName + '\']').tinymce().destroy();
    }
}

// Popup window
function openPopup(url, width, height)
{
    
    width = width || '80%';
    height = height || '70%';
    
    if (typeof width == 'string' && width.length > 1 && width.substr(width.length - 1, 1) == '%')
    {
        width = parseInt(window.screen.width * parseInt(width, 10) / 100, 10);
    }
    if (typeof height == 'string' && height.length > 1 && height.substr(height.length - 1, 1) == '%')
    {
        height = parseInt(window.screen.height * parseInt(height, 10) / 100, 10);
    }
    
    if (width < 640)
    {
        width = 640;
    }
    
    if (height < 420)
    {
        height = 420;
    }
    
    var settings = {
        centerBrowser : 1, // center window over browser window? {1 (YES) or 0
        // (NO)}. overrides top and left
        centerScreen : 0, // center window over entire screen? {1 (YES) or 0
        // (NO)}. overrides top and left
        height : height, // sets the height in pixels of the window.
        left : 0, // left position when the window appears.
        location : 0, // determines whether the address bar is displayed {1
        // (YES) or 0 (NO)}.
        menubar : 0, // determines whether the menu bar is displayed {1 (YES)
        // or 0 (NO)}.
        resizable : 0, // whether the window can be resized {1 (YES) or 0
        // (NO)}. Can also be overloaded using resizable.
        scrollbars : 1, // determines whether scrollbars appear on the window {1
        // (YES) or 0 (NO)}.
        status : 0, // whether a status line appears at the bottom of the window
        // {1 (YES) or 0 (NO)}.
        width : width, // sets the width in pixels of the window.
        windowName : '_blank', // name of window set from the name attribute of
        // the element that invokes the click
        windowURL : url, // url used for the popup
        top : 0, // top position when the window appears.
        toolbar : 0
    // determines whether a toolbar (includes the forward and back buttons) is
    // displayed {1 (YES) or 0 (NO)}.
    };
    
    var windowFeatures = 'height=' + settings.height + ',width=' + settings.width + ',toolbar=' + settings.toolbar
            + ',scrollbars=' + settings.scrollbars + ',status=' + settings.status + ',resizable=' + settings.resizable
            + ',location=' + settings.location + ',menuBar=' + settings.menubar;
    
    settings.windowName = this.name || settings.windowName;
    settings.windowURL = this.href || settings.windowURL;
    var centeredY, centeredX;
    
    if (settings.centerBrowser)
    {
        
        if ($.browser.msie)
        {// hacked together for IE browsers
            centeredY = (window.screenTop - 120)
                    + ((((document.documentElement.clientHeight + 120) / 2) - (settings.height / 2)));
            centeredX = window.screenLeft + ((((document.body.offsetWidth + 20) / 2) - (settings.width / 2)));
        }
        else
        {
            centeredY = window.screenY + (((window.outerHeight / 2) - (settings.height / 2)));
            centeredX = window.screenX + (((window.outerWidth / 2) - (settings.width / 2)));
        }
        window.open(settings.windowURL, settings.windowName,
                windowFeatures + ',left=' + centeredX + ',top=' + centeredY).focus();
    }
    else if (settings.centerScreen)
    {
        centeredY = (screen.height - settings.height) / 2;
        centeredX = (screen.width - settings.width) / 2;
        window.open(settings.windowURL, settings.windowName,
                windowFeatures + ',left=' + centeredX + ',top=' + centeredY).focus();
    }
    else
    {
        window.open(settings.windowURL, settings.windowName,
                windowFeatures + ',left=' + settings.left + ',top=' + settings.top).focus();
    }
    return false;
}

function scaleDimensions(width, height, imageProperties)
{
    if (imageProperties.width > width || imageProperties.height > height)
    {
        if (imageProperties.width >= imageProperties.height)
        {
            imageProperties.thumbnailWidth = width;
            imageProperties.thumbnailHeight = (imageProperties.thumbnailWidth / imageProperties.width)
                    * imageProperties.height;
        }
        else
        {
            imageProperties.thumbnailHeight = height;
            imageProperties.thumbnailWidth = (imageProperties.thumbnailHeight / imageProperties.height)
                    * imageProperties.width;
        }
    }
    else
    {
        imageProperties.thumbnailWidth = imageProperties.width;
        imageProperties.thumbnailHeight = imageProperties.height;
    }
    
    return imageProperties;
}

function asort(inputArr, sort_flags)
{
    // http://kevin.vanzonneveld.net
    // + original by: Brett Zamir (http://brett-zamir.me)
    // + improved by: Brett Zamir (http://brett-zamir.me)
    // + input by: paulo kuong
    // + improved by: Brett Zamir (http://brett-zamir.me)
    // + bugfixed by: Adam Wallner (http://web2.bitbaro.hu/)
    // % note 1: SORT_STRING (as well as natsort and natcasesort) might also be
    // % note 1: integrated into all of these functions by adapting the code at
    // % note 1: http://sourcefrog.net/projects/natsort/natcompare.js
    // % note 2: The examples are correct, this is a new way
    // % note 2: Credits to:
    // http://javascript.internet.com/math-related/bubble-sort.html
    // % note 3: This function deviates from PHP in returning a copy of the
    // array instead
    // % note 3: of acting by reference and returning true; this was necessary
    // because
    // % note 3: IE does not allow deleting and re-adding of properties without
    // caching
    // % note 3: of property position; you can set the ini of
    // "phpjs.strictForIn" to true to
    // % note 3: get the PHP behavior, but use this only if you are in an
    // environment
    // % note 3: such as Firefox extensions where for-in iteration order is
    // fixed and true
    // % note 3: property deletion is supported. Note that we intend to
    // implement the PHP
    // % note 3: behavior by default if IE ever does allow it; only gives
    // shallow copy since
    // % note 3: is by reference in PHP anyways
    // % note 4: Since JS objects' keys are always strings, and (the
    // % note 4: default) SORT_REGULAR flag distinguishes by key type,
    // % note 4: if the content is a numeric string, we treat the
    // % note 4: "original type" as numeric.
    // - depends on: strnatcmp
    // - depends on: i18n_loc_get_default
    // * example 1: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
    // * example 1: data = asort(data);
    // * results 1: data == {c: 'apple', b: 'banana', d: 'lemon', a: 'orange'}
    // * returns 1: true
    // * example 2: ini_set('phpjs.strictForIn', true);
    // * example 2: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
    // * example 2: asort(data);
    // * results 2: data == {c: 'apple', b: 'banana', d: 'lemon', a: 'orange'}
    // * returns 2: true
    var valArr = [], keyArr = [], k, i, ret, sorter, that = this, strictForIn = false, populateArr = {};
    
    switch (sort_flags)
    {
        case 'SORT_STRING':
            // compare items as strings
            sorter = function(a, b)
            {
                return that.strnatcmp(a, b);
            };
            break;
        case 'SORT_LOCALE_STRING':
            // compare items as strings, based on the current locale (set with
            // i18n_loc_set_default() as of PHP6)
            var loc = this.i18n_loc_get_default();
            sorter = this.php_js.i18nLocales[loc].sorting;
            break;
        case 'SORT_NUMERIC':
            // compare items numerically
            sorter = function(a, b)
            {
                return (a - b);
            };
            break;
        case 'SORT_REGULAR':
            // compare items normally (don't change types)
        default:
            sorter = function(a, b)
            {
                var aFloat = parseFloat(a), bFloat = parseFloat(b), aNumeric = aFloat + '' === a, bNumeric = bFloat
                        + '' === b;
                if (aNumeric && bNumeric)
                {
                    return aFloat > bFloat ? 1 : aFloat < bFloat ? -1 : 0;
                }
                else if (aNumeric && !bNumeric)
                {
                    return 1;
                }
                else if (!aNumeric && bNumeric)
                {
                    return -1;
                }
                return a > b ? 1 : a < b ? -1 : 0;
            };
            break;
    }
    
    var bubbleSort = function(keyArr, inputArr)
    {
        var i, j, tempValue, tempKeyVal;
        for (i = inputArr.length - 2; i >= 0; i--)
        {
            for (j = 0; j <= i; j++)
            {
                ret = sorter(inputArr[j + 1], inputArr[j]);
                if (ret < 0)
                {
                    tempValue = inputArr[j];
                    inputArr[j] = inputArr[j + 1];
                    inputArr[j + 1] = tempValue;
                    tempKeyVal = keyArr[j];
                    keyArr[j] = keyArr[j + 1];
                    keyArr[j + 1] = tempKeyVal;
                }
            }
        }
    };
    
    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    this.php_js.ini = this.php_js.ini || {};
    // END REDUNDANT
    strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value
            && this.php_js.ini['phpjs.strictForIn'].local_value !== 'off';
    populateArr = strictForIn ? inputArr : populateArr;
    
    // Get key and value arrays
    for (k in inputArr)
    {
        if (inputArr.hasOwnProperty(k))
        {
            valArr.push(inputArr[k]);
            keyArr.push(k);
            if (strictForIn)
            {
                delete inputArr[k];
            }
        }
    }
    try
    {
        // Sort our new temporary arrays
        bubbleSort(keyArr, valArr);
    }
    catch (e)
    {
        return false;
    }
    
    // Repopulate the old array
    for (i = 0; i < valArr.length; i++)
    {
        populateArr[keyArr[i]] = valArr[i];
    }
    
    return strictForIn || populateArr;
}

function explode(delimiter, string, limit)
{
    // http://kevin.vanzonneveld.net
    // + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: kenneth
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: d3x
    // + bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // * example 1: explode(' ', 'Kevin van Zonneveld');
    // * returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}
    // * example 2: explode('=', 'a=bc=d', 2);
    // * returns 2: ['a', 'bc=d']
    var emptyArray = {
        0 : ''
    };
    
    // third argument is not required
    if (arguments.length < 2 || typeof arguments[0] == 'undefined' || typeof arguments[1] == 'undefined')
    {
        return null;
    }
    
    if (delimiter === '' || delimiter === false || delimiter === null)
    {
        return false;
    }
    
    if (typeof delimiter == 'function' || typeof delimiter == 'object' || typeof string == 'function'
            || typeof string == 'object')
    {
        return emptyArray;
    }
    
    if (delimiter === true)
    {
        delimiter = '1';
    }
    
    if (!limit)
    {
        return string.toString().split(delimiter.toString());
    }
    else
    {
        // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

function str_replace(search, replace, subject, count)
{
    // http://kevin.vanzonneveld.net
    // + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: Gabriel Paderni
    // + improved by: Philip Peterson
    // + improved by: Simon Willison (http://simonwillison.net)
    // + revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // + bugfixed by: Anton Ongson
    // + input by: Onno Marsman
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + tweaked by: Onno Marsman
    // + input by: Brett Zamir (http://brett-zamir.me)
    // + bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + input by: Oleg Eremeev
    // + improved by: Brett Zamir (http://brett-zamir.me)
    // + bugfixed by: Oleg Eremeev
    // % note 1: The count parameter must be passed as a string in order
    // % note 1: to find a global variable in which the result will be given
    // * example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // * returns 1: 'Kevin.van.Zonneveld'
    // * example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name},
    // lars');
    // * returns 2: 'hemmo, mars'
    var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0, f = [].concat(search), r = [].concat(replace), s = subject, ra = Object.prototype.toString
            .call(r) === '[object Array]', sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count)
    {
        this.window[count] = 0;
    }
    
    for (i = 0, sl = s.length; i < sl; i++)
    {
        if (s[i] === '')
        {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++)
        {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp)
            {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}

function sprintf()
{
    // http://kevin.vanzonneveld.net
    // + original by: Ash Searle (http://hexmen.com/blog/)
    // + namespaced by: Michael White (http://getsprink.com)
    // + tweaked by: Jack
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + input by: Paulo Freitas
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + input by: Brett Zamir (http://brett-zamir.me)
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // * example 1: sprintf("%01.2f", 123.1);
    // * returns 1: 123.10
    // * example 2: sprintf("[%10s]", 'monkey');
    // * returns 2: '[ monkey]'
    // * example 3: sprintf("[%'#10s]", 'monkey');
    // * returns 3: '[####monkey]'
    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
    var a = arguments, i = 0, format = a[i++];
    
    // pad()
    var pad = function(str, len, chr, leftJustify)
    {
        if (!chr)
        {
            chr = ' ';
        }
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };
    
    // justify()
    var justify = function(value, prefix, leftJustify, minWidth, zeroPad, customPadChar)
    {
        var diff = minWidth - value.length;
        if (diff > 0)
        {
            if (leftJustify || !zeroPad)
            {
                value = pad(value, minWidth, customPadChar, leftJustify);
            }
            else
            {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };
    
    // formatBaseX()
    var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad)
    {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {
            '2' : '0b',
            '8' : '0',
            '16' : '0x'
        }[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };
    
    // formatString()
    var formatString = function(value, leftJustify, minWidth, precision, zeroPad, customPadChar)
    {
        if (precision != null)
        {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
    };
    
    // doFormat()
    var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type)
    {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;
        
        if (substring == '%%')
        {
            return '%';
        }
        
        // parse flags
        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++)
        {
            switch (flags.charAt(j))
            {
                case ' ':
                    positivePrefix = ' ';
                    break;
                case '+':
                    positivePrefix = '+';
                    break;
                case '-':
                    leftJustify = true;
                    break;
                case "'":
                    customPadChar = flags.charAt(j + 1);
                    break;
                case '0':
                    zeroPad = true;
                    break;
                case '#':
                    prefixBaseX = true;
                    break;
            }
        }
        
        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth)
        {
            minWidth = 0;
        }
        else if (minWidth == '*')
        {
            minWidth = +a[i++];
        }
        else if (minWidth.charAt(0) == '*')
        {
            minWidth = +a[minWidth.slice(1, -1)];
        }
        else
        {
            minWidth = +minWidth;
        }
        
        // Note: undocumented perl feature:
        if (minWidth < 0)
        {
            minWidth = -minWidth;
            leftJustify = true;
        }
        
        if (!isFinite(minWidth))
        {
            throw new Error('sprintf: (minimum-)width must be finite');
        }
        
        if (!precision)
        {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        }
        else if (precision == '*')
        {
            precision = +a[i++];
        }
        else if (precision.charAt(0) == '*')
        {
            precision = +a[precision.slice(1, -1)];
        }
        else
        {
            precision = +precision;
        }
        
        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];
        
        switch (type)
        {
            case 's':
                return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
            case 'c':
                return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b':
                return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o':
                return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x':
                return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X':
                return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
            case 'u':
                return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd':
                number = (+value) | 0;
                prefix = number < 0 ? '-' : positivePrefix;
                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            case 'e':
            case 'E':
            case 'f':
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positivePrefix;
                method = [ 'toExponential', 'toFixed', 'toPrecision' ]['efg'.indexOf(type.toLowerCase())];
                textTransform = [ 'toString', 'toUpperCase' ]['eEfFgG'.indexOf(type) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
            default:
                return substring;
        }
    };
    
    return format.replace(regex, doFormat);
}