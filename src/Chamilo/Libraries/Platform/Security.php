<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Libraries\Platform
 */
class Security
{
    use DependencyInjectionContainerTrait;

    /**
     * This function tackles the XSS injections.
     * Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     *
     * @param string $variable
     * @param boolean $isAdmin
     *
     * @return string string
     * @deprecated
     *
     * @see removeXSS
     */
    public function remove_XSS($variable, $isAdmin = null)
    {
        return self::removeXSS($variable, $isAdmin);
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
    public function removeXSS($variable, $isAdmin = null)
    {
        if (is_null($isAdmin))
        {
            $isAdmin = self::isPlatformAdminOrTeacher();
        }

        if (! $isAdmin)
        { // don't question the actions of platform admins, they know what they are doing

            if (is_array($variable))
            {
                return self::removeXSSRecursive($variable, $isAdmin);
            }

            // from: http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
            // from: https://gist.github.com/mbijon/1098477

            // Remove any attribute starting with "on" or xmlns
            $variable = preg_replace('#(<[^>]+?[\x00-\x20"\x2f\x5c\']+)(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $variable);

            // Remove javascript: and vbscript: protocols
            $variable = preg_replace(
                '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
                '$1=$2nojavascript...',
                $variable);
            $variable = preg_replace(
                '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
                '$1=$2novbscript...',
                $variable);
            $variable = preg_replace(
                '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
                '$1=$2nomozbinding...',
                $variable);

            // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
            $variable = preg_replace(
                '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
                '$1>',
                $variable);
            $variable = preg_replace(
                '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
                '$1>',
                $variable);
            $variable = preg_replace(
                '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
                '$1>',
                $variable);

            // Remove namespaced elements (we do not need them)
            $variable = preg_replace('#</*\w+:\w[^>]*+>#i', '', $variable);

            do
            {
                // Remove really unwanted tags, but allow object|embed (for html editor)
                $old_data = $variable;
                $variable = preg_replace(
                    '#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|s(?:cript)|xml)[^>]*+>#i',
                    '<invalid>',
                    $variable);
            }
            while ($old_data !== $variable);

            // we are done...
        }
        return $variable;
    }

    /**
     *
     * @param string[] $array
     * @param boolean $isAdmin
     *
     * @return string[]
     *
     * @deprecated
     *
     * @see removeXSSRecursive
     */
    public function remove_XSS_recursive($array, $isAdmin = null)
    {
        return self::removeXSSRecursive($array, $isAdmin);
    }

    /**
     *
     * @param string[] $array
     * @param boolean $isAdmin
     *
     * @return string[]
     */
    public function removeXSSRecursive($array, $isAdmin = null)
    {
        foreach ($array as $key => $value)
        {
            $key2 = self::removeXSS($key, $isAdmin);
            $value2 = (is_array($value)) ? self::removeXSSRecursive($value, $isAdmin) : self::remove_XSS(
                $value,
                $isAdmin);

            unset($array[$key]);
            $array[$key2] = $value2;
        }
        return $array;
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
     * Gets the user agent in the session to later check it with check_ua() to prevent most cases of session hijacking.
     */
    public function getUa()
    {
        Session::register('sec_ua_seed', uniqid(rand(), true));
        Session::register('sec_ua', Request::server('HTTP_USER_AGENT') . Session::retrieve('sec_ua_seed'));
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
     * Checks the user agent of the client as recorder by get_ua() to prevent most session hijacking attacks.
     *
     * @return boolean
     */
    public function checkUa()
    {
        $session_agent = Session::retrieve('sec_ua');
        $current_agent = Request::server('HTTP_USER_AGENT') . Session::retrieve('sec_ua_seed');

        if (isset($session_agent) and $session_agent === $current_agent)
        {
            return true;
        }
        return false;
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities|object
     */
    public function getHashingUtilities()
    {
        $this->initializeContainer();
        return $this->getService('chamilo.libraries.hashing.hashing_utilities');
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
        Session::register('sec_token', $token);
        return $token;
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
     * This function checks that the token generated in get_token() has been kept (prevents Cross-Site Request Forgeries
     * attacks)
     *
     * @param string $array
     *
     * @return boolean if it's the right token, false otherwise
     */
    public function checkToken($array = 'post')
    {
        $session_token = Session::retrieve('sec_token');

        switch ($array)
        {
            case 'get' :
                $get_token = Request::get('sec_token');
                if (isset($session_token) && isset($get_token) && $session_token === $get_token)
                {
                    return true;
                }
                return false;
            case 'post' :
                $post_token = Request::post('sec_token');
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
     * Checks whether or not the logged in user is a platform admin or a teacher
     *
     * @return boolean
     */
    protected function isPlatformAdminOrTeacher()
    {
        $user_id = Session::getUserId();

        if (! empty($user_id))
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                $user_id);

            return $user->is_platform_admin() || $user->is_teacher();
        }

        return false;
    }
}
