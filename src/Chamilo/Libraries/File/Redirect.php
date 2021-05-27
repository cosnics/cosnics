<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Platform\Security;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @var string
     */
    private $anchor;

    /**
     *
     * @param string[] $parameters
     * @param string[] $filterParameters
     * @param boolean $encodeEntities
     * @param string $anchor
     */
    public function __construct(
        $parameters = [], $filterParameters = [], $encodeEntities = false, $anchor = null
    )
    {
        $this->parameters = $parameters;
        $this->filterParameters = $filterParameters;
        $this->encodeEntities = $encodeEntities;
        $this->anchor = $anchor;
    }

    /**
     *
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     *
     * @param string $anchor
     */
    public function setAnchor($anchor)
    {
        $this->anchor = $anchor;
    }

    /**
     * Returns the full URL of the current page, based upon env variables Env variables used: $_SERVER['HTTPS'] =
     * (on|off|) $_SERVER['HTTP_HOST'] = value of the Host: header $_SERVER['SERVER_PORT'] = port number (only used if
     * not http/80,https/443) $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
     *
     * @param boolean $includeRequest
     *
     * @return string
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

        $parts = [];

        $parts[] = $protocol;
        $parts[] = $host;

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
        $filteredParameters = [];

        foreach ($parameters as $key => $value)
        {
            if (!in_array($key, $filterParameters))
            {
                $filteredParameters[$key] = $value;
            }
        }

        return $filteredParameters;
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
     * @return string
     */
    public function getUrl()
    {
        $security = new Security();
        $baseUrl = $this->getCurrentUrl(false) . $_SERVER['PHP_SELF'];
        $baseUrl = $security->removeXSS($baseUrl, false);

        return $this->getWebLink($baseUrl);
    }

    /**
     *
     * @param string $url
     *
     * @return string
     */
    private function getWebLink($url)
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
                $url .= self::ARGUMENT_SEPARATOR;
            }
            // Because the argument separator can be defined in the php.ini
            // file, we explicitly add it as a parameter here to avoid
            // trouble when parsing the resulting urls
            $url .= http_build_query($parameters, '', self::ARGUMENT_SEPARATOR);
            if ($this->getAnchor())
            {
                $url .= '#' . $this->getAnchor();
            }
            else
            {
                $url .= $anchor;
            }
        }

        if ($this->getEncodeEntities())
        {
            $url = htmlentities($url);
        }

        return $url;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setFilterParameter($key, $value)
    {
        $this->filterParameters[$key] = $value;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
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
     * Redirect to the Url
     * @throws \Exception
     */
    public function toUrl()
    {
        $this->writeHeader($this->getUrl());
    }

    /**
     *
     * @param string $url
     *
     * @throws \Exception
     */
    public function writeHeader($url)
    {
        if (headers_sent($filename, $line))
        {
            throw new Exception('headers already sent in ' . $filename . ' on line ' . $line);
        }

        $response = new RedirectResponse($url);
        $response->send();

        exit();
    }
}
