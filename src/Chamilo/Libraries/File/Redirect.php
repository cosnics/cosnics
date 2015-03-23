<?php
namespace Chamilo\Libraries\File;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * $Id: redirect.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common
 */
class Redirect
{
    // Different redirect types
    const ARGUMENT_SEPARATOR = '&';

    /**
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @var string[]
     */
    private $filterParameters;

    /**
     *
     * @var boolean
     */
    private $encodeEntities;

    /**
     *
     * @param string[] $parameters
     * @param string[] $filterParameters
     * @param boolean $encodeEntities
     */
    public function __construct($parameters = array (), $filterParameters = array(), $encodeEntities = false)
    {
        $this->parameters = $parameters;
        $this->filterParameters = $filterParameters;
        $this->encodeEntities = $encodeEntities;
    }

    /**
     *
     * @return string[]
     */
    private function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param string[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return string[]
     */
    public function getFilterParameters()
    {
        return $this->filterParameters;
    }

    /**
     *
     * @param string[] $filterParameters
     */
    public function setFilterParameters($filterParameters)
    {
        $this->filterParameters = $filterParameters;
    }

    /**
     *
     * @return boolean
     */
    public function getEncodeEntities()
    {
        return $this->encodeEntities;
    }

    /**
     *
     * @param boolean $encodeEntities
     */
    public function setEncodeEntities($encodeEntities)
    {
        $this->encodeEntities = $encodeEntities;
    }

    public function toUrl()
    {
        $this->writeHeader($this->getUrl());
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        $baseUrl = $this->getCurrentUrl(false, false) . $_SERVER['PHP_SELF'];
        return $this->getWebLink($baseUrl);
    }

    /**
     *
     * @return string[]
     */
    private function getFilteredParameters()
    {
        $parameters = $this->getParameters();
        $filterParameters = $this->getFilterParameters();

        if (empty($filterParameters))
        {
            return $parameters;
        }

        $filterParameters = is_array($filterParameters) ? $filterParameters : array($filterParameters);
        $filteredParameters = array();

        foreach ($parameters as $key => $value)
        {
            if (! in_array($key, $filterParameters))
            {
                $filteredParameters[$key] = $value;
            }
        }

        return $filteredParameters;
    }

    /**
     *
     * @param string $url
     * @param array $parameters
     * @param boolean $encode_entities
     * @return string
     */
    private function getWebLink($url, $parameters = array (), $encode_entities = false)
    {
        $parameters = $this->getFilteredParameters();

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

        if ($this->getEncodeEntities())
        {
            $url = htmlentities($url);
        }

        return $url;
    }

    /**
     *
     * @param string $url
     */
    private function writeHeader($url)
    {
        if (headers_sent($filename, $line))
        {
            throw new Exception('headers already sent in ' . $filename . ' on line ' . $line);
        }

        $response = new RedirectResponse($url);
        $response->send();
    }

    /**
     * Returns the full URL of the current page, based upon env variables Env variables used: $_SERVER['HTTPS'] =
     * (on|off|) $_SERVER['HTTP_HOST'] = value of the Host: header $_SERVER['SERVER_PORT'] = port number (only used if
     * not http/80,https/443) $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
     *
     * @return string Current URL
     */
    public function getCurrentUrl($includeRequest = true)
    {
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

        $parts = array();

        $parts[] = $protocol;
        $parts[] = $host;
        $parts[] = $port;

        if ($includeRequest)
        {
            /**
             * Filter php_self to avoid a security vulnerability.
             */
            $requestUri = substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r"));

            if ($this->getEncodeEntities())
            {
                $requestUri = htmlentities($requestUri, ENT_QUOTES);
            }

            $parts[] = $requestUri;
        }

        return implode('', $parts);
    }

    /**
     *
     * @param string[] $parameters
     * @param string[] $filterParameters
     * @param boolean $encodeEntities
     * @return string
     * @deprecated No longer use this static method, it's only available for backwards compatibility
     */
    public static function get_url($parameters = array (), $filterParameters = array(), $encodeEntities = false)
    {
        $redirect = new self($parameters, $filterParameters, $encodeEntities);
        return $redirect->getUrl();
    }

    /**
     *
     * @param string[] $parameters
     * @param string[] $filterParameters
     * @param boolean $encodeEntities
     * @return string
     * @deprecated No longer use this static method, it's only available for backwards compatibility
     */
    public static function url($parameters = array (), $filterParameters = array(), $encodeEntities = false)
    {
        $redirect = new self($parameters, $filterParameters, $encodeEntities);
        $redirect->toUrl();
    }
}
