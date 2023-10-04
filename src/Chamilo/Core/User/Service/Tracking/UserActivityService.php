<?php
namespace Chamilo\Core\User\Service\Tracking;

use Chamilo\Core\Admin\Service\WhoIsOnlineService;
use Chamilo\Core\User\Service\UserEventListenerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\DataClass\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 * @package Chamilo\Core\User\Service\Tracking
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivityService implements UserEventListenerInterface
{
    protected ?User $currentUser;

    protected PageConfiguration $pageConfiguration;

    protected UserTrackingRepository $userTrackingRepository;

    protected WhoIsOnlineService $whoIsOnlineService;

    public function __construct(
        UserTrackingRepository $userTrackingRepository, ?User $currentUser, PageConfiguration $pageConfiguration,
        WhoIsOnlineService $whoIsOnlineService
    )
    {
        $this->userTrackingRepository = $userTrackingRepository;
        $this->currentUser = $currentUser;
        $this->pageConfiguration = $pageConfiguration;
        $this->whoIsOnlineService = $whoIsOnlineService;
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

    public function afterEnterPage(User $user, string $pageUri): bool
    {
        if (!$this->getWhoIsOnlineService()->updateWhoIsOnlineForUserIdentifierWithCurrentTime(
            $user->getId()
        ))
        {
            return false;
        }

        $userVisit = new UserVisit();
        $userVisit->setUserIdentifier($user->getId());
        $userVisit->setEnterDate(time());
        $userVisit->setLocation($pageUri);

        if (!$this->getUserTrackingRepository()->createUserVisit($userVisit))
        {
            return false;
        }

        $this->getPageConfiguration()->addHtmlHeader('<script>var tracker=' . $userVisit->getId() . ';</script>');

        return true;
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

    public function afterLogin(User $user, ?string $clientIp): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGIN, $user, $clientIp
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

    public function beforeLeavePage(User $user, string $userVisitIdentifier): bool
    {
        $userVisit = $this->getUserTrackingRepository()->findUserVisitByIdentifier($userVisitIdentifier);

        if ($userVisit instanceof UserVisit)
        {
            $userVisit->setLeaveDate(time());

            return $this->getUserTrackingRepository()->updateUserVisit($userVisit);
        }

        return true;
    }

    public function beforeLogout(User $user, ?string $clientIp): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGOUT, $user, $clientIp
        );
    }

    protected function createAuthenticationActivityFormParameters(int $action, User $user, ?string $clientIp): bool
    {
        $userAuthenticationActivity = new UserAuthenticationActivity();

        $userAuthenticationActivity->setUserIdentifier($user->getId());
        $userAuthenticationActivity->setDate(time());
        $userAuthenticationActivity->setIp($clientIp);
        $userAuthenticationActivity->setAction($action);

        return $this->getUserTrackingRepository()->createUserAuthenticationActivity($userAuthenticationActivity);
    }

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->pageConfiguration;
    }

    public function getUserTrackingRepository(): UserTrackingRepository
    {
        return $this->userTrackingRepository;
    }

    public function getWhoIsOnlineService(): WhoIsOnlineService
    {
        return $this->whoIsOnlineService;
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