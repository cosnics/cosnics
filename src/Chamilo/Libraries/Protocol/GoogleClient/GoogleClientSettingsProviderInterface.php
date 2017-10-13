<?php
namespace Chamilo\Libraries\Protocol\GoogleClient;

/**
 * Settings provider to support the google client service
 *
 * @package Chamilo\Libraries\Protocol\GoogleClient
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
     * Returns the security refresh token for the google client
     * 
     * @return string
     */
    public function getRefreshToken();

    /**
     * Stores the access token from the google client into chamilo
     * 
     * @param string $accessToken
     *
     * @return bool
     */
    public function saveAccessToken($accessToken);

    /**
     * Stores the refresh token
     * 
     * @param $refreshToken
     * @return bool
     */
    public function saveRefreshToken($refreshToken);

    /**
     * Removes the access token
     * 
     * @return bool
     */
    public function removeAccessToken();

    /**
     * Removes the refresh token
     * 
     * @return bool
     */
    public function removeRefreshToken();
}