<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserService
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository
     */
    protected $userRepository;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * UserService constructor
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository $userRepository
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    public function __construct(UserRepository $userRepository, LocalSetting $localSetting)
    {
        $this->setUserRepository($userRepository);
        $this->setLocalSetting($localSetting);
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository $userRepository
     */
    protected function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected function getLocalSetting()
    {
        return $this->localSetting;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    protected function setLocalSetting(LocalSetting $localSetting)
    {
        $this->localSetting = $localSetting;
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string @todo should be value object
     */
    public function getAzureUserIdentifier(User $user)
    {
        $azureActiveDirectoryUserIdentifier = $this->getLocalSetting()->get(
            'external_user_id',
            'Chamilo\Libraries\Protocol\Microsoft\Graph',
            $user
        );

        if (empty($azureActiveDirectoryUserIdentifier))
        {
            $azureUser = $this->getUserRepository()->getAzureUser($user);

            if ($azureUser instanceof \Microsoft\Graph\Model\User)
            {
                $azureActiveDirectoryUserIdentifier = $azureUser->getId();
            }

            $this->getLocalSetting()->create(
                'external_user_id',
                $azureActiveDirectoryUserIdentifier,
                'Chamilo\Libraries\Protocol\Microsoft\Graph',
                $user
            );
        }

        return $azureActiveDirectoryUserIdentifier;
    }

    /**
     * @param User[] $users
     *
     * @return string[]
     */
    public function getAzureUserIdentifiers(array $users): array
    {
        $azureIds = [];
        foreach ($users as $user)
        {
            $azureUserId = $this->getAzureUserIdentifier($user);

            if (!empty($azureUserId))
            {
                $azureIds[] = $azureUserId;
            }
        }

        return $azureIds;
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
}