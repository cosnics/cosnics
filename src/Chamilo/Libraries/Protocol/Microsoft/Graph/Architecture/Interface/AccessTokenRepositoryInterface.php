<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Storage solution for the Microsoft Graph access token
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface AccessTokenRepositoryInterface
{
    public function getApplicationAccessToken(): ?AccessToken;

    public function getDelegatedAccessToken(): ?AccessToken;

    public function storeApplicationAccessToken(AccessToken $accessToken);

    public function storeDelegatedAccessToken(AccessToken $accessToken);
}