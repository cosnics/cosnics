<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Security;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Use UrlGenerator now
 */
class Redirect
{
    public const ARGUMENT_SEPARATOR = '&';

    public static ?Security $security = null;

    private ?string $anchor;

    private bool $encodeEntities;

    /**
     * @var string[]
     */
    private array $filterParameters;

    /**
     * @var string[]
     */
    private array $parameters;

    /**
     * @param string[] $parameters
     * @param string[] $filterParameters
     */
    public function __construct(
        array $parameters = [], array $filterParameters = [], bool $encodeEntities = false, ?string $anchor = null
    )
    {
        $this->parameters = $parameters;
        $this->filterParameters = $filterParameters;
        $this->encodeEntities = $encodeEntities;
        $this->anchor = $anchor;
    }

    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    public function setAnchor(?string $anchor)
    {
        $this->anchor = $anchor;
    }

    /**
     * Returns the full URL of the current page, based upon env variables Env variables used: $_SERVER['HTTPS'] =
     * (on|off|) $_SERVER['HTTP_HOST'] = value of the Host: header $_SERVER['SERVER_PORT'] = port number (only used if
     * not http/80,https/443) $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
     */
    public function getCurrentUrl(bool $includeRequest = true): string
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

    public function getEncodeEntities(): bool
    {
        return $this->encodeEntities;
    }

    public function setEncodeEntities(bool $encodeEntities)
    {
        $this->encodeEntities = $encodeEntities;
    }

    /**
     *
     * @return string[]
     */
    public function getFilterParameters(): array
    {
        return $this->filterParameters;
    }

    /**
     * @param string[] $filterParameters
     */
    public function setFilterParameters(array $filterParameters)
    {
        $this->filterParameters = $filterParameters;
    }

    /**
     * @return string[]
     */
    private function getFilteredParameters(): array
    {
        $parameters = $this->getParameters();
        $filterParameters = $this->getFilterParameters();

        if (empty($filterParameters))
        {
            return $parameters;
        }

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
     * @return string[]
     */
    private function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @throws \Exception
     */
    public function getSecurity(): Security
    {
        if (self::$security === null)
        {
            self::$security =
                DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(Security::class);
        }

        return self::$security;
    }

    /**
     * @throws \Exception
     */
    public function getUrl(): string
    {
        $baseUrl = $this->getCurrentUrl(false) . $_SERVER['PHP_SELF'];
        $baseUrl = $this->getSecurity()->removeXSS($baseUrl);

        return $this->getWebLink($baseUrl);
    }

    protected function getWebLink(string $url): string
    {
        $parameters = $this->getFilteredParameters();

        if (count($parameters))
        {
            // remove anchor
            $anchor = strstr($url, '#', false);
            if ($anchor)
            {
                $url = strstr($url, '#', true);
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

    public function setFilterParameter(string $key, $value)
    {
        $this->filterParameters[$key] = $value;
    }

    public function setParameter(string $key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param string[] $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @throws \Exception
     */
    public function toUrl()
    {
        $this->writeHeader($this->getUrl());
    }

    /**
     * @throws \Exception
     */
    public function writeHeader(string $url)
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
