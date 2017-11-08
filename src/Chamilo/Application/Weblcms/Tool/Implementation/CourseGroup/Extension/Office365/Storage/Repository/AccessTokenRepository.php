<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
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
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * AccessTokenRepository constructor.
     *
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(
        \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting, SessionUtilities $sessionUtilities
    )
    {
        $this->localSetting = $localSetting;
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * Returns the application access token
     *
     * @return AccessToken
     */
    public function getApplicationAccessToken()
    {
        $accessTokenData = $this->localSetting->get(
            'access_token', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );

        if (empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    /**
     * Stores the application access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeApplicationAccessToken(AccessToken $accessToken)
    {
        $this->localSetting->create(
            'access_token', json_encode($accessToken->jsonSerialize()),
            'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );
    }

    /**
     * Returns the delegated access token
     *
     * @return AccessToken
     */
    public function getDelegatedAccessToken()
    {
        $accessTokenData = $this->sessionUtilities->get('office365_delegated_access_token');

        if (empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    /**
     * Stores the delegated access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeDelegatedAccessToken(AccessToken $accessToken)
    {
        $this->sessionUtilities->register('office365_delegated_access_token', json_encode($accessToken->jsonSerialize()));
    }
}