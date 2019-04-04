<?php

namespace Chamilo\Application\Lti\Service\Security;

use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthToken;

/**
 * Class OAuthDataStore
 * @package Chamilo\Application\Lti\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class OAuthDataStore extends \IMSGlobal\LTI\OAuth\OAuthDataStore
{
    /**
     * @var \Chamilo\Application\Lti\Domain\Application
     */
    protected $application;

    /**
     * OAuthDataStore constructor.
     *
     * @param \Chamilo\Application\Lti\Domain\Application $application
     */
    public function __construct(\Chamilo\Application\Lti\Domain\Application $application)
    {
        $this->application = $application;
    }

    /**
     * Create an OAuthConsumer object for the tool consumer.
     *
     * @param string $consumerKey Consumer key value
     *
     * @return OAuthConsumer OAuthConsumer object
     */
    function lookup_consumer($consumerKey)
    {
        return $this->application->toOAuthConsumer();
    }

    /**
     * Create an OAuthToken object for the tool consumer.
     *
     * @param string $consumer   OAuthConsumer object
     * @param string $tokenType  Token type
     * @param string $token      Token value
     *
     * @return OAuthToken OAuthToken object
     */
    function lookup_token($consumer, $tokenType, $token)
    {
        return new OAuthToken($consumer, '');
    }

    /**
     * Lookup nonce value for the tool consumer.
     *
     * @param OAuthConsumer $consumer OAuthConsumer object
     * @param string $token Token value
     * @param string $nonce
     * @param string $timestamp Date/time of request
     *
     * @return boolean True if the nonce value already exists
     */
    function lookup_nonce($consumer, $token, $nonce, $timestamp)
    {
        return false;
    }
}