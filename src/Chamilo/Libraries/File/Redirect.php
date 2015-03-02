<?php
namespace Chamilo\Libraries\File;

use Exception;

/**
 * $Id: redirect.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common
 */
class Redirect
{
    // Different redirect types
    const TYPE_LINK = 'link';
    const TYPE_URL = 'url';

    // Different link types
    const TYPE_CORE = 'core';
    const TYPE_APPLICATION = 'application';
    const TYPE_INDEX = 'index';
    const ARGUMENT_SEPARATOR = '&';

    /**
     *
     * @param string $application
     * @param array $parameters
     * @param array $filter
     * @param boolean $encode_entities
     */
    public static function link($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $link = self :: get_link($parameters, $filter, $encode_entities);
        self :: write_header($link);
    }

    /**
     *
     * @param string $url
     * @param array $parameters
     * @param boolean $encode_entities
     */
    public static function web_link($url, $parameters = array (), $encode_entities = false)
    {
        $link = self :: get_web_link($url, $parameters, $encode_entities);
        self :: write_header($link);
    }

    /**
     *
     * @param string $application
     * @param array $parameters
     * @param array $filter
     * @param boolean $encode_entities
     * @return string
     */
    public static function get_link($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $filtered_parameters = self :: filter_out_parameters($parameters, $filter);
        return self :: get_web_link('index.php', $filtered_parameters, $encode_entities);
    }

    private static function filter_out_parameters($parameters, $filter)
    {
        if (empty($filter))
            return $parameters;

        $filter = is_array($filter) ? $filter : array($filter);
        $filtered_parameters = array();

        foreach ($parameters as $key => $value)
        {
            if (! in_array($key, $filter))
            {
                $filtered_parameters[$key] = $value;
            }
        }

        return $filtered_parameters;
    }

    /**
     *
     * @param array $parameters
     * @param array $filter
     * @param boolean $encode_entities
     */
    public static function url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $url = self :: get_url($parameters, $filter, $encode_entities);
        self :: write_header($url);
    }

    /**
     *
     * @param array $parameters
     * @param array $filter
     * @param boolean $encode_entities
     * @return string
     */
    public static function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $url = $_SERVER['PHP_SELF'];

        $filtered_parameters = self :: filter_out_parameters($parameters, $filter);
        return self :: get_web_link($url, $filtered_parameters, $encode_entities);
    }

    /**
     *
     * @param string $url
     * @param array $parameters
     * @param boolean $encode_entities
     * @return string
     */
    public static function get_web_link($url, $parameters = array (), $encode_entities = false)
    {
        if (count($parameters))
        {
            // remove anchor
            $anchor = strstr($url, "#", false);
            if ($anchor)
            {
                $url = strstr($url, "#", true);
            }
            if (strpos($url, '?') === false)
            {
                $url .= '?';
            }
            else
            {
                $url .= self :: ARGUMENT_SEPARATOR;
            }
            // Because the argument separator can be defined in the php.ini
            // file, we explicitly add it as a parameter here to avoid
            // trouble when parsing the resulting urls
            $url .= http_build_query($parameters, '', self :: ARGUMENT_SEPARATOR);
            $url .= $anchor;
        }

        if ($encode_entities)
        {
            $url = htmlentities($url);
        }
        return $url;
    }

    /**
     *
     * @param string $url
     */
    public static function write_header($url)
    {
        if (headers_sent($filename, $line))
        {
            throw new Exception('headers already sent in ' . $filename . ' on line ' . $line);
        }
        header('Location: ' . $url);
        exit();
    }

    /**
     * Returns the full URL of the current page, based upon env variables Env variables used: $_SERVER['HTTPS'] =
     * (on|off|) $_SERVER['HTTP_HOST'] = value of the Host: header $_SERVER['SERVER_PORT'] = port number (only used if
     * not http/80,https/443) $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
     *
     * @return string Current URL
     */
    public static function current_url($encode_entities = false)
    {
        global $_SERVER;

        /**
         * Filter php_self to avoid a security vulnerability.
         */
        $php_request_uri = substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r"));

        if ($encode_entities)
        {
            $php_request_uri = htmlentities($php_request_uri, ENT_QUOTES);
        }

        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
        {
            $protocol = 'https://';
        }
        else
        {
            $protocol = 'http://';
        }
        $host = $_SERVER['HTTP_HOST'];
        if ($_SERVER['SERVER_PORT'] != '' && (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
             ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443')))
        {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        else
        {
            $port = '';
        }

        return $protocol . $host . $port . $php_request_uri;
    }
}
