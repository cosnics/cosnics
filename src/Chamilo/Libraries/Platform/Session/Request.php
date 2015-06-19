<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\Platform\Security;

/**
 * $Id: request.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.session
 */
class Request
{

    // TODO OO design this class
    // when OO designing this class, $security should be considered as a dependency
    public static $security;

    public static function get_security()
    {
        if (self :: $security === null)
        {
            self :: $security = new Security();
        }
        return self :: $security;
    }

    public static function get($variable, $default = null)
    {
        if (isset($_GET[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return self :: get_security()->remove_XSS($_GET[$variable]);
        }

        return $default;
    }

    public static function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }

    public static function post($variable)
    {
        if (isset($_POST[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return self :: get_security()->remove_XSS($_POST[$variable]);
        }

        return null;
    }

    public static function server($variable, $default = null)
    {
        if (isset($_SERVER[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return $_SERVER[$variable];
        }

        return $default;
    }

    public static function file($variable)
    {
        $value = $_FILES[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    public static function environment($variable)
    {
        $value = $_ENV[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }
}
