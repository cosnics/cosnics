<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Storage solution for the Graph access token
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{

    protected SessionUtilities $sessionUtilities;

    protected User $user;

    protected UserSettingService $userSettingService;

    protected UserService $userservice;

    public function __construct(
        User $user, UserService $userservice, UserSettingService $userSettingService, SessionUtilities $sessionUtilities
    )
    {
        $this->user = $user;
        $this->userservice = $userservice;
        $this->userSettingService = $userSettingService;
        $this->sessionUtilities = $sessionUtilities;
    }

    public function getApplicationAccessToken(): ?AccessToken
    {
        $accessTokenData = $this->getUserSettingService()->getSettingForUser(
            $this->getUser(), 'Chamilo\Libraries\Protocol\Microsoft\Graph', 'access_token'
        );

        if (empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    public function getDelegatedAccessToken(): ?AccessToken
    {
        $accessTokenData = $this->getSessionUtilities()->get('graph_delegated_access_token');

        if (empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    public function getUserservice(): UserService
    {
        return $this->userservice;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function storeApplicationAccessToken(AccessToken $accessToken)
    {
        $this->getUserservice()->createUserSettingForSettingAndUser(
            'Chamilo\Libraries\Protocol\Microsoft\Graph', 'access_token', $this->getUser(),
            json_encode($accessToken->jsonSerialize())
        );
    }

    /**
     * Stores the delegated access token
     *
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     */
    public function storeDelegatedAccessToken(AccessToken $accessToken)
    {
        $this->sessionUtilities->register('graph_delegated_access_token', json_encode($accessToken->jsonSerialize()));
    }
}