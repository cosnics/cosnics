<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Storage solution for the Graph access token
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{

    protected SessionInterface $session;

    protected User $user;

    protected UserSettingService $userSettingService;

    public function __construct(
        User $user, UserSettingService $userSettingService, SessionInterface $session
    )
    {
        $this->user = $user;
        $this->userSettingService = $userSettingService;
        $this->session = $session;
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
        $accessTokenData = $this->getSession()->get('graph_delegated_access_token');

        if (empty($accessTokenData))
        {
            return null;
        }

        return new AccessToken(json_decode($accessTokenData, true));
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function storeApplicationAccessToken(AccessToken $accessToken)
    {
        $this->getUserSettingService()->createUserSettingForSettingAndUser(
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
        $this->session->set('graph_delegated_access_token', json_encode($accessToken->jsonSerialize()));
    }
}