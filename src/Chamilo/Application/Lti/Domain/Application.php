<?php

namespace Chamilo\Application\Lti\Domain;

use IMSGlobal\LTI\OAuth\OAuthConsumer;

/**
 * Class Application
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class Application
{
    /**
     * @var string
     */
    protected $ltiUrl;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * Application constructor.
     *
     * @param string $ltiUrl
     * @param string $key
     * @param string $secret
     */
    public function __construct(string $ltiUrl, string $key, string $secret)
    {
        $this->ltiUrl = $ltiUrl;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getLtiUrl(): string
    {
        return $this->ltiUrl;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Transforms the application to an OAuth consumer for further use
     *
     * @return \IMSGlobal\LTI\OAuth\OAuthConsumer
     */
    public function toOAuthConsumer()
    {
        return new OAuthConsumer($this->getKey(), $this->getSecret());
    }
}