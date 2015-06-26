<?php
namespace Chamilo\Libraries\Platform;



use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package common.libraries.security
 */
class Security
{

    /**
     * This function tackles the XSS injections. Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     *
     * @param string	The variable to filter for XSS
     * @return string string
     */
    public function remove_XSS($variable, $is_admin = null)
    {
        if (is_null($is_admin))
        {
            $is_admin = self :: is_platform_admin_or_teacher();
        }

        if (! $is_admin)
        { // don't question the actions of platform admins, they know what they are doing

            if (is_array($variable))
            {
                return self :: remove_XSS_recursive($variable, $is_admin);
            }

            // from: http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php

            // Remove any attribute starting with "on" or xmlns
            $variable = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $variable);

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
                    '#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript)|xml)[^>]*+>#i',
                    '',
                    $variable);
            }
            while ($old_data !== $variable);

            // we are done...
        }
        return $variable;
    }

    public function remove_XSS_recursive($array, $is_admin = null)
    {
        foreach ($array as $key => $value)
        {
            $key2 = self :: remove_XSS($key, $is_admin);
            $value2 = (is_array($value)) ? self :: remove_XSS_recursive($value, $is_admin) : self :: remove_XSS(
                $value,
                $is_admin);

            unset($array[$key]);
            $array[$key2] = $value2;
        }
        return $array;
    }

    /**
     * Gets the user agent in the session to later check it with check_ua() to prevent most cases of session hijacking.
     *
     * @return void
     */
    public function get_ua()
    {
        Session :: register('sec_ua_seed', uniqid(rand(), true));
        Session :: register('sec_ua', Request :: server('HTTP_USER_AGENT') . Session :: retrieve('sec_ua_seed'));
    }

    /**
     * Checks the user agent of the client as recorder by get_ua() to prevent most session hijacking attacks.
     *
     * @return bool if the user agent is the same, false otherwise
     */
    public function check_ua()
    {
        $session_agent = Session :: retrieve('sec_ua');
        $current_agent = Request :: server('HTTP_USER_AGENT') . Session :: retrieve('sec_ua_seed');

        if (isset($session_agent) and $session_agent === $current_agent)
        {
            return true;
        }
        return false;
    }

    /**
     * This function sets a random token to be included in a form as a hidden field and saves it into the user's
     * session. This later prevents Cross-Site Request Forgeries by checking that the user is really the one that sent
     * this form in knowingly (this form hasn't been generated from another website visited by the user at the same
     * time). Check the token with check_token()
     *
     * @return string
     */
    public function get_token()
    {
        $token = Hashing :: hash(uniqid(rand(), true));
        Session :: register('sec_token', $token);
        return $token;
    }

    /**
     * This function checks that the token generated in get_token() has been kept (prevents Cross-Site Request Forgeries
     * attacks)
     *
     * @param string	The array in which to get the token ('get' or 'post')
     * @return bool if it's the right token, false otherwise
     */
    public function check_token($array = 'post')
    {
        $session_token = Session :: retrieve('sec_token');

        switch ($array)
        {
            case 'get' :
                $get_token = Request :: get('sec_token');
                if (isset($session_token) && isset($get_token) && $session_token === $get_token)
                {
                    return true;
                }
                return false;
            case 'post' :
                $post_token = Request :: post('sec_token');
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
        // Just in case, don't let anything slip
        return false;
    }

    /**
     * Checks whether or not the logged in user is a platform admin or a teacher
     *
     * @return bool
     */
    protected function is_platform_admin_or_teacher()
    {
        $user_id = Session :: get_user_id();

        if (! empty($user_id))
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(\Chamilo\Core\User\Storage\DataClass\User :: class_name(), (int) $user_id);

            return $user->is_platform_admin() || $user->is_teacher();
        }

        return false;
    }
}
