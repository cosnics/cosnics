<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\File\Path;

/**
 * $Id: cookie.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.session
 */
class Cookie
{

    public function register($variable, $value, $expiration = '900')
    {
        setcookie($variable, $value, time() + $expiration, "", Path::getInstance()->getBasePath(true));
        $_COOKIE[$variable] = $value;
    }

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

    public function retrieve($variable)
    {
        return $_COOKIE[$variable];
    }

    public function get_user_id()
    {
        return self::retrieve('_uid');
    }
}
