var API_1484_11 = new Object(
);
var API = new Object(
);

API_1484_11.Initialize = ChamiloInitialize;
API_1484_11.Terminate = ChamiloTerminate;
API_1484_11.GetValue = ChamiloGetValue;
API_1484_11.SetValue = ChamiloSetValue;
API_1484_11.Commit = ChamiloCommit;
API_1484_11.GetLastError = ChamiloGetLastError;
API_1484_11.GetErrorString = ChamiloGetErrorString;
API_1484_11.GetDiagnostic = ChamiloGetDiagnostic;
API_1484_11.values = new Array(
);
API_1484_11.version = "1.0";

API.LMSInitialize = ChamiloInitialize;
API.LMSFinish = ChamiloTerminate;
API.LMSGetValue = ChamiloGetValue;
API.LMSSetValue = ChamiloSetValue;
API.LMSCommit = ChamiloCommit;
API.LMSGetLastError = ChamiloGetLastError;
API.LMSGetErrorString = ChamiloGetErrorString;
API.LMSGetDiagnostic = ChamiloGetDiagnostic;
API.values = new Array(
);
API.version = "1.0";

var last_error = 0;
var initialized = false;

function ChamiloInitialize(
    params
    )
{
    if (params && params != "")
    {
        last_error = 201;
        return "false";
    }

    if (initialized)
    {
        last_error = 103;
        return "false"
    }

    initialized = true;
    last_error = 0;

    return "true";
}

function ChamiloTerminate(
    params
    )
{
    if (params && params != "")
    {
        last_error = 201;
        return "false";
    }

    if (!initialized)
    {
        last_error = 301;
        return "false";
    }

    initialized = false;
    last_error = 0;

    var response = jQuery.ajax(
        {
            type:"POST",
            url:"./application/weblcms/tool/learning_path/php/ajax/scorm/terminate.php",
            data:{ tracker_id:tracker_id },
            async:false
        }
    ).responseText; //alert(response);

    check_redirect_conditions(
        this.values
    );

    return "true";
}

function check_redirect_conditions(
    values
    )
{
    var url = null;
    var request = values['adl.nav.request'];
    if (request == null)
    {
        return
    }

    if (request == 'continue')
    {
        url = continue_url;
    }

    if (request == 'previous')
    {
        url = previous_url;
    }

    var re = new RegExp(
        '{target=.*}jump'
    );
    if (request.match(
        re
    ))
    {
        var re = new RegExp(
            '{.*}'
        );
        var m = re.exec(
            request
        );
        var identifier = m[0];
        identifier = identifier.substr(
            8, identifier.length - 9
        );
        url = jump_urls[identifier];
    }

    if (url)
    {
        window.location = url;
    }
}

function ChamiloGetValue(
    variable
    )
{
    if (!initialized)
    {
        last_error = 122;
        last_error = 301;
        return "";
    }

    if (variable == "")
    {
        last_error = 301;
        return "";
    }
    //alert(variable);
    last_error = 0;

    var value = check_for_special_requests(
        variable
    );
    if (value)
    {
        return value;
    }

    value = this.values[variable];
    if (!value)
    {
        value = get_existing_value(
            variable
        );
    }

    if (value == "")
    {
        last_error = 403;
    }

    return value;
}

function check_for_special_requests(
    variable
    )
{
    if (variable == "adl.nav.request_valid.continue")
    {
        if (continue_url != null)
        {
            return "true";
        }
        else
        {
            return "false";
        }
    }

    if (variable == "adl.nav.request_valid.previous")
    {
        if (previous_url != null)
        {
            return "true";
        }
        else
        {
            return "false";
        }
    }

    var re = new RegExp(
        'adl.nav.request_valid.choice.{target=.*}'
    );
    if (variable.match(
        re
    ))
    {
        var re = new RegExp(
            '{.*}'
        );
        var m = re.exec(
            variable
        );
        var identifier = m[0];
        identifier = identifier.substr(
            8, identifier.length - 9
        );

        if (jump_urls[identifier] != null)
        {
            return "true";
        }
        else
        {
            return "false";
        }

    }
}

function ChamiloSetValue(
    variable, value
    )
{
    if (!initialized)
    {
        last_error = 132;
        last_error = 301;
        return "false";
    }

    if (variable == "")
    {
        last_error = 351;
        return "false";
    }

    if (!validate_set_variable(
        variable, value
    ))
    {
        return "false";
    }

    this.values[variable] = value;
    last_error = 0;

    var response = jQuery.ajax(
        {
            type:"POST",
            url:"./application/weblcms/tool/learning_path/php/ajax/scorm/set_value.php",
            data:{ tracker_id:tracker_id, variable:variable, value:value },
            async:false
        }
    ).responseText; //alert(response);

    if (response.substr(
        0, 5
    ) == 'error')
    {
        last_error = parseInt(
            response.substr(
                6, response.length - 6
            )
        );
        return "false";
    }

    return "true";
}

function validate_set_variable(
    variable, value
    )
{
    var re = new RegExp(
        'cmi.objectives.[0-9]*.id'
    );
    if (variable.match(
        re
    ))
    {
        var existing_value = get_existing_value(
            variable
        );
        if (existing_value.length != 0 && existing_value != value)
        {
            last_error = 351;
            return false;
        }
        else
        {
            return true;
        }
    }

    if (variable == 'cmi.completion_status')
    {
        var possible_values = ['incomplete', 'completed', 'not attempted', 'unknown'];
        if (!in_array(
            value, possible_values
        ))
        {
            last_error = 406;
            return false;
        }
    }

    return true;
}

function get_existing_value(
    variable
    )
{
    var value = jQuery.ajax(
        {
            type:"POST",
            url:"./application/weblcms/tool/learning_path/php/ajax/scorm/get_value.php",
            data:{ tracker_id:tracker_id, variable:variable},
            async:false
        }
    ).responseText;

    return value;
}

function ChamiloCommit(
    params
    )
{
    if (params && params != "")
    {
        last_error = 201;
        return "false";
    }

    if (!initialized)
    {
        last_error = 142;
        last_error = 301;
        return "false";
    }

    last_error = 0;

    return "true";
}

function ChamiloGetLastError(
    )
{
    return last_error;
}

function ChamiloGetErrorString(
    error_code
    )
{
    return "";
}

function ChamiloGetDiagnostic(
    )
{
    return "";
}

// Helper function

function translation(
    string, application
    )
{
    var translated_string = $.ajax(
        {
            type:"POST",
            url:"./common/javascript/ajax/translation.php",
            data:{ string:string, application:application },
            async:false
        }
    ).responseText;

    return translated_string;
}

function in_array(
    needle, haystack
    )
{
    for (var i = 0; i < haystack.length; i++)
    {
        if (haystack[i] == needle)
        {
            return true;
        }
    }

    return false;
}
