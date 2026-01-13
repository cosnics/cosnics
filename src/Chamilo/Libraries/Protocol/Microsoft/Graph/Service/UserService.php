<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     */
    public function getAndSaveUserIdentifier(User $user): ?string
    {
        $userIdentifier = $this->getUserSettingService()->getSettingForUser(
            $user, 'Chamilo\Libraries', 'microsoft_graph_external_user_id'
        );

        if (empty($userIdentifier))
        {
            $userIdentifier = $this->getUserIdentifier($user);

            try
            {
                $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                    'Chamilo\Libraries', 'microsoft_graph_external_user_id', $user, $userIdentifier
                );
            }
            catch (StorageMethodException|StorageNoResultException|CacheException)
            {
            }
        }

        return $userIdentifier;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     */
    public function getUser(User $user): \Microsoft\Graph\Generated\Models\User
    {
        return $this->getUserRepository()->getUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     */
    public function getUserIdentifier(User $user): string
    {
        $graphUser = $this->getUser($user);

        if (!$graphUser->getId())
        {
            throw new UserNotFoundException($user);
        }

        return $graphUser->getId();
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