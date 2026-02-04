<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function getAndSaveUserIdentifier(User $user): ?string
    {
        $userIdentifier = $this->getUserSettingService()->getSettingForUser(
            $user, 'Chamilo\Libraries', 'microsoft_graph_external_user_id'
        );

        if (empty($userIdentifier)) {
            $userIdentifier = $this->getUserIdentifier($user);

            $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                'Chamilo\Libraries', 'microsoft_graph_external_user_id', $user, $userIdentifier
            );
        }

        return $userIdentifier;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $users
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    public function getAzureUserIdentifiers(array $users): array
    {
        $azureIds = [];
        foreach ($users as $user) {
            $azureUserId = $this->getUserIdentifier($user);

            if (!empty($azureUserId)) {
                $azureIds[] = $azureUserId;
            }
        }

        return $azureIds;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    public function getUser(User $user): \Microsoft\Graph\Generated\Models\User
    {
        return $this->getUserRepository()->getUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException
     */
    public function getUserIdentifier(User $user): string
    {
        $graphUser = $this->getUser($user);

        if (!$graphUser->getId()) {
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