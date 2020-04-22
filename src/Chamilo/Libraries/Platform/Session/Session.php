<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Configuration\Configuration;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 *
 * @deprecated
 *
 * @see \Chamilo\Libraries\Platform\Session\SessionUtilities
 */
class Session
{

    public static function clear()
    {
        // session_regenerate_id();
        session_unset();
        $_SESSION = array();
    }

    public static function destroy()
    {
        session_unset();
        $_SESSION = array();
        session_destroy();
    }

    /**
     * @param string $variable
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($variable, $default = null)
    {
        if (is_array($_SESSION) && array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }
        else
        {
            return $default;
        }
    }

    /**
     * @return int
     */
    public static function getUserId()
    {
        return self::retrieve('_uid');
    }

    /**
     * @return int
     *
     * @deprecated
     *
     * @see getUserId
     */
    public static function get_user_id()
    {
        return self::getUserId();
    }

    /**
     * @param string $variable
     * @param mixed $value
     */
    public static function register($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    /**
     * @param string $variable
     * @param mixed $value
     */
    public static function registerIfNotSet($variable, $value)
    {
        $sessionValue = self::retrieve($variable);

        if (is_null($sessionValue))
        {
            self::register($variable, $value);
        }
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function retrieve($variable)
    {
        if (is_array($_SESSION) && array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }

        return null;
    }

    public static function start()
    {
        /**
         * Disables PHP automatically provided cache headers
         */
        session_cache_limiter('');

        $configuration = Configuration::getInstance();

        if ($configuration->is_available() && $configuration->is_connectable())
        {
            if ($configuration->get_setting(array('Chamilo\Configuration', 'session', 'session_handler')) == 'chamilo')
            {
                $session_handler = new SessionHandler();
                session_set_save_handler(
                    array($session_handler, 'open'), array($session_handler, 'close'), array($session_handler, 'read'),
                    array($session_handler, 'write'), array($session_handler, 'destroy'),
                    array($session_handler, 'garbage')
                );
            }

            $session_key = Configuration::get('Chamilo\Configuration', 'general', 'security_key');
            if (is_null($session_key))
            {
                $session_key = 'dk_sid';
            }

            session_name($session_key);
            session_start();
        }
        else
        {
            session_start();
        }
    }

    /**
     * @param string $variable
     */
    public static function unregister($variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            $_SESSION[$variable] = null;
            unset($GLOBALS[$variable]);
        }
    }
}
