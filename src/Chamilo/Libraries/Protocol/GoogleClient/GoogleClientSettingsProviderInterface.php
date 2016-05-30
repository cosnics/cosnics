<?php

namespace Chamilo\Libraries\Protocol\GoogleClient;

/**
 * Settings provider to support the google client service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface GoogleClientSettingsProviderInterface
{
    /**
     * Returns the developer key for the google client
     *
     * @return string
     */
    public function getDeveloperKey();

    /**
     * Returns the client id for the google client
     *
     * @return string
     */
    public function getClientId();

    /**
     * Returns the client secret for the google client
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Returns the scopes for the google client
     *
     * @return string
     */
    public function getScopes();

    /**
     * Returns the security access token for the google client
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Stores the access token from the google client into chamilo
     *
     * @param string $accessToken
     *
     * @return bool
     */
    public function saveAccessToken($accessToken);

    /**
     * Removes the access token
     *
     * @return bool
     */
    public function removeAccessToken();

}