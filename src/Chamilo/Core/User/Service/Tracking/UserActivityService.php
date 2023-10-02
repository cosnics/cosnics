<?php
namespace Chamilo\Core\User\Service\Tracking;

use Chamilo\Core\User\Service\UserEventListenerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;

/**
 * @package Chamilo\Core\User\Service\Tracking
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivityService implements UserEventListenerInterface
{
    protected ?User $currentUser;

    protected UserTrackingRepository $userTrackingRepository;

    public function __construct(UserTrackingRepository $userTrackingRepository, ?User $currentUser)
    {
        $this->userTrackingRepository = $userTrackingRepository;
        $this->currentUser = $currentUser;
    }

    public function afterCreate(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_CREATED, $user->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterDelete(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_DELETED, $user->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterExport(User $actionUser, User $exportedUser): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_EXPORTED, $exportedUser->getId(), $actionUser->getId()
            )
        );
    }

    public function afterImport(User $actionUser, User $importedUser): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_IMPORTED, $importedUser->getId()
            )
        );
    }

    public function afterPasswordReset(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_PASSWORD_RESET, $user->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterQuota(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_QUOTA, $user->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterRegistration(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_REGISTERED, $user->getId()
            )
        );
    }

    public function afterUpdate(User $user): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_UPDATED, $user->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function beforeDelete(User $user): bool
    {
        return true;
    }

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }

    public function getUserTrackingRepository(): UserTrackingRepository
    {
        return $this->userTrackingRepository;
    }

    protected function initializeUserActivityFromParameters(
        int $action, string $targetUserIdentifier, ?string $sourceUserIdentifier = null
    ): UserActivity
    {
        $userActivity = new UserActivity();

        $userActivity->setAction($action);
        $userActivity->setDate(time());
        $userActivity->setSourceUserIdentifier($sourceUserIdentifier);
        $userActivity->setTargetUserIdentifier($targetUserIdentifier);

        return $userActivity;
    }
}