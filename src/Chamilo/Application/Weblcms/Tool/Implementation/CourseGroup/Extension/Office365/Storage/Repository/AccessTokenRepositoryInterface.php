<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Storage solution for the office365 access token
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AccessTokenRepositoryInterface
{
    /**
     * Returns the access token
     *
     * @return AccessToken
     */
    public function getAccessToken();

    /**
     * Stores the access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeAccessToken(AccessToken $accessToken);
}