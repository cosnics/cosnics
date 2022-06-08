<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Security;

/**
 * @package Chamilo\Libraries\Platform\Session
 *
 * @deprecated Use \Chamilo\Libraries\Platform\ChamiloRequest service now
 */
class Request
{

    public static ?Security $security = null;

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function environment($variable)
    {
        // TODO: Add the necessary security filters if and where necessary
        return $_ENV[$variable];
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function file($variable)
    {
        // TODO: Add the necessary security filters if and where necessary
        return $_FILES[$variable];
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
            return self::getSecurity()->removeXSS($_GET[$variable]);
        }

        return $default;
    }

    /**
     * @throws \Exception
     */
    public static function getSecurity(): Security
    {
        if (self::$security === null)
        {
            self::$security =
                DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(Security::class);
        }

        return self::$security;
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
            return self::getSecurity()->removeXSS($_POST[$variable]);
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
     * @param mixed $value
     */
    public static function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }
}
