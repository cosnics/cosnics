<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserService
{

    protected UserRepository $userRepository;

    protected UserSettingService $userSettingService;

    public function __construct(
        UserRepository $userRepository, UserSettingService $userSettingService
    )
    {
        $this->userRepository = $userRepository;
        $this->userSettingService = $userSettingService;
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->getUserRepository()->authorizeUserByAuthorizationCode($authorizationCode);
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function getAzureUserIdentifier(User $user): string
    {
        $azureActiveDirectoryUserIdentifier = $this->getUserSettingService()->getSettingForUser(
            $user, 'Chamilo\Libraries\Protocol\Microsoft\Graph', 'external_user_id'
        );

        if (empty($azureActiveDirectoryUserIdentifier))
        {
            $azureUser = $this->getUserRepository()->getAzureUser($user);

            if ($azureUser instanceof \Microsoft\Graph\Model\User)
            {
                $azureActiveDirectoryUserIdentifier = $azureUser->getId();
            }

            $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                'Chamilo\Libraries\Protocol\Microsoft\Graph', 'external_user_id', $user,
                $azureActiveDirectoryUserIdentifier
            );
        }

        return $azureActiveDirectoryUserIdentifier;
    }

    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

}