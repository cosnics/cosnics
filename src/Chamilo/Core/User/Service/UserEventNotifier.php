<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEventNotifier implements UserEventListenerInterface
{
    /**
     * @var \Chamilo\Core\User\Service\UserEventListenerInterface[]
     */
    protected array $userEventListeners;

    public function __construct()
    {
        $this->userEventListeners = [];
    }

    public function addUserEventListener(UserEventListenerInterface $userEventListener): void
    {
        $this->userEventListeners[] = $userEventListener;
    }

    public function afterCreate(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterCreate($user);
        }

        return true;
    }

    public function afterDelete(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterDelete($user);
        }

        return true;
    }

    public function afterExport(User $actionUser, User $exportedUser): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterExport($actionUser, $exportedUser);
        }

        return true;
    }

    public function afterImport(User $actionUser, User $importedUser): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterImport($importedUser, $actionUser);
        }

        return true;
    }

    public function afterPasswordReset(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterPasswordReset($user);
        }

        return true;
    }

    public function afterQuota(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterQuota($user);
        }

        return true;
    }

    public function afterRegistration(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterRegistration($user);
        }

        return true;
    }

    public function afterUpdate(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->afterUpdate($user);
        }

        return true;
    }

    public function beforeDelete(User $user): bool
    {
        foreach ($this->userEventListeners as $userEventListener)
        {
            $userEventListener->beforeDelete($user);
        }

        return true;
    }
}