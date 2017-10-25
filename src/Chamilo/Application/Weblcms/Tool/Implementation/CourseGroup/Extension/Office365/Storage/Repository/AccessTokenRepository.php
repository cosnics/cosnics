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
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * AccessTokenRepository constructor.
     *
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    public function __construct(\Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting)
    {
        $this->localSetting = $localSetting;
    }

    /**
     * Returns the access token
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        $accessTokenData = $this->localSetting->get(
            'access_token', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );

        if(empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    /**
     * Stores the access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeAccessToken(AccessToken $accessToken)
    {
        $this->localSetting->create(
            'access_token', json_encode($accessToken->jsonSerialize()),
            'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );
    }
}