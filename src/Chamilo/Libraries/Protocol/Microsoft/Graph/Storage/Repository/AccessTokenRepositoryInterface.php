<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Storage solution for the Microsoft Graph access token
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface AccessTokenRepositoryInterface
{

    /**
     * Returns the application access token
     *
     * @return AccessToken
     */
    public function getApplicationAccessToken();

    /**
     * Returns the delegated access token
     *
     * @return AccessToken
     */
    public function getDelegatedAccessToken();

    /**
     * Stores the application access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeApplicationAccessToken(AccessToken $accessToken);

    /**
     * Stores the delegated access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeDelegatedAccessToken(AccessToken $accessToken);
}