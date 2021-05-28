<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Platform
 */
class Security
{
    use DependencyInjectionContainerTrait;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     * This function checks that the token generated in get_token() has been kept (prevents Cross-Site Request Forgeries
     * attacks)
     *
     * @param string $array
     *
     * @return boolean if it's the right token, false otherwise
     */
    public function checkToken($array = 'post')
    {
        $sessionUtilities = $this->getSessionUtilities();
        $request = $this->getRequest();

        $session_token = $sessionUtilities->retrieve('sec_token');

        switch ($array)
        {
            case 'get' :
                $get_token = $request->query->get('sec_token');
                if (isset($session_token) && isset($get_token) && $session_token === $get_token)
                {
                    return true;
                }

                return false;
            case 'post' :
                $post_token = $request->request->get('sec_token');
                if (isset($session_token) && isset($post_token) && $session_token === $post_token)
                {
                    return true;
                }

                return false;
            default :
                if (isset($session_token) && isset($array) && $session_token === $array)
                {
                    return true;
                }

                return false;
        }
    }

    /**
     * Checks the user agent of the client as recorder by get_ua() to prevent most session hijacking attacks.
     *
     * @return boolean
     */
    public function checkUa()
    {
        $sessionUtilities = $this->getSessionUtilities();
        $request = $this->getRequest();

        $session_agent = $sessionUtilities->retrieve('sec_ua');
        $current_agent = $request->server->get('HTTP_USER_AGENT') . $sessionUtilities->retrieve('sec_ua_seed');

        if (isset($session_agent) and $session_agent === $current_agent)
        {
            return true;
        }

        return false;
    }

    /**
     * This function checks that the token generated in get_token() has been kept (prevents Cross-Site Request Forgeries
     * attacks)
     *
     * @param string $array
     *
     * @return boolean if it's the right token, false otherwise
     * @deprecated
     *
     * @see checkToken
     */
    public function check_token($array = 'post')
    {
        return self::checkToken($array);
    }

    /**
     * Checks the user agent of the client as recorder by get_ua() to prevent most session hijacking attacks.
     *
     * @return boolean
     *
     * @deprecated
     *
     * @see checkUa
     */
    public function check_ua()
    {
        return self::checkUa();
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities|object
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    /**
     * This function sets a random token to be included in a form as a hidden field and saves it into the user's
     * session.
     * This later prevents Cross-Site Request Forgeries by checking that the user is really the one that sent
     * this form in knowingly (this form hasn't been generated from another website visited by the user at the same
     * time). Check the token with check_token()
     *
     * @return string
     */
    public function getToken()
    {
        $token = $this->getHashingUtilities()->hashString(uniqid(rand(), true));
        $this->getSessionUtilities()->register('sec_token', $token);

        return $token;
    }

    /**
     * Gets the user agent in the session to later check it with check_ua() to prevent most cases of session hijacking.
     */
    public function getUa()
    {
        $sessionUtilities = $this->getSessionUtilities();
        $sessionUtilities->register('sec_ua_seed', uniqid(rand(), true));
        $sessionUtilities->register(
            'sec_ua', $this->getRequest()->server->get('HTTP_USER_AGENT') . $sessionUtilities->retrieve('sec_ua_seed')
        );
    }

    /**
     * This function sets a random token to be included in a form as a hidden field and saves it into the user's
     * session.
     * This later prevents Cross-Site Request Forgeries by checking that the user is really the one that sent
     * this form in knowingly (this form hasn't been generated from another website visited by the user at the same
     * time). Check the token with check_token()
     *
     * @return string
     *
     * @deprecated
     *
     * @see getToken
     */
    public function get_token()
    {
        return $this->getToken();
    }

    /**
     * Gets the user agent in the session to later check it with check_ua() to prevent most cases of session hijacking.
     *
     * @deprecated
     *
     * @see getUa
     */
    public function get_ua()
    {
        self::getUa();
    }

    /**
     * This function tackles the XSS injections.
     * Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     *
     * @param string $variable
     * @param boolean $isAdmin
     *
     * @return string string
     */
    public static function removeXSS($variable)
    {
        if (is_array($variable))
        {
            return self::removeXSSRecursive($variable);
        }

        // from: http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
        // from: https://gist.github.com/mbijon/1098477

        // Remove any attribute starting with "on" or xmlns
        $variable = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $variable);

        // Remove javascript: and vbscript: protocols
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...', $variable
        );
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...', $variable
        );
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $variable
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $variable
        );
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $variable
        );
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>', $variable
        );

        // Remove namespaced elements (we do not need them)
        $variable = preg_replace('#</*\w+:\w[^>]*+>#i', '', $variable);

        do
        {
            // Remove really unwanted tags, but allow object|embed (for html editor)
            $old_data = $variable;
            $variable = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript)|xml)[^>]*+>#i',
                '', $variable
            );
        }
        while ($old_data !== $variable);

        return $variable;
    }

    /**
     *
     * @param string[] $array
     *
     * @return string[]
     */
    public static function removeXSSRecursive($array)
    {
        foreach ($array as $key => $value)
        {
            $key2 = self::removeXSS($key);
            $value2 = (is_array($value)) ? self::removeXSSRecursive($value) : self::removeXSS(
                $value
            );

            unset($array[$key]);
            $array[$key2] = $value2;
        }

        return $array;
    }

    /**
     * This function tackles the XSS injections.
     * Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     *
     * @param string $variable
     *
     * @return string string
     * @deprecated
     *
     * @see removeXSS
     */
    public static function remove_XSS($variable)
    {
        return self::removeXSS($variable);
    }

    /**
     *
     * @param string[] $array
     *
     * @return string[]
     *
     * @deprecated
     *
     * @see removeXSSRecursive
     */
    public static function remove_XSS_recursive($array)
    {
        return self::removeXSSRecursive($array);
    }
}
