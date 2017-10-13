<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 */
class Cookie
{

    /**
     * @param string $variable
     * @param mixed $value
     * @param string $expiration
     */
    public function register($variable, $value, $expiration = '900')
    {
        setcookie($variable, $value, time() + $expiration, "", Path::getInstance()->getBasePath(true));
        $_COOKIE[$variable] = $value;
    }

    /**
     * @param string $variable
     */
    public function unregister($variable)
    {
        setcookie($variable, "", time() - 3600);
        $_COOKIE[$variable] = null;
        unset($GLOBALS[$variable]);
    }

    public function destroy()
    {
        $cookies = $_COOKIE;
        foreach ($cookies as $key => $value)
        {
            setcookie($key, "", time() - 3600);
        }
        $_COOKIE = array();
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public function retrieve($variable)
    {
        return $_COOKIE[$variable];
    }

    /**
     * @return int
     *
     * @deprecated
     *
     * @see getUserId
     */
    public function get_user_id()
    {
        return $this->getUserId();
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return self::retrieve('_uid');
    }
}
