<?php

namespace Chamilo\Application\Lti\Domain;

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
}