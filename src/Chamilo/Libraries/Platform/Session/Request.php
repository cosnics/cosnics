<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\Platform\Security;

/**
 * @package Chamilo\Libraries\Platform\Session
 *
 * @deprecated
 *
 * @see \Chamilo\Libraries\Platform\ChamiloRequest (use service)
 */
class Request
{

    /**
     * @var \Chamilo\Libraries\Platform\Security
     */
    public static $security;

    /**
     * @return \Chamilo\Libraries\Platform\Security
     */
    public static function get_security()
    {
        if (self::$security === null)
        {
            self::$security = new Security();
        }
        return self::$security;
    }

    /**
     * @param string $variable
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($variable, $default = null)
    {
        if (isset($_GET[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return self::get_security()->remove_XSS($_GET[$variable]);
        }

        return $default;
    }

    /**
     * @param string $variable
     * @param mixed $value
     */
    public static function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function post($variable)
    {
        if (isset($_POST[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return self::get_security()->remove_XSS($_POST[$variable]);
        }

        return null;
    }

    /**
     * @param string $variable
     * @param mixed $default
     *
     * @return mixed
     */
    public static function server($variable, $default = null)
    {
        if (isset($_SERVER[$variable]))
        {
            // TODO: Add the necessary security filters if and where necessary
            return $_SERVER[$variable];
        }

        return $default;
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function file($variable)
    {
        $value = $_FILES[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function environment($variable)
    {
        $value = $_ENV[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }
}
