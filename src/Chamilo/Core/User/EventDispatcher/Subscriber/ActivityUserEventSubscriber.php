<?php
namespace Chamilo\Core\User\EventDispatcher\Subscriber;

use Chamilo\Core\Admin\Service\WhoIsOnlineService;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserExportEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserImportEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\DataClass\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\User\EventDispatcher\Subscriber
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityUserEventSubscriber implements EventSubscriberInterface
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

    public function afterCreate(AfterUserCreateEvent $afterUserCreateEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_CREATED, $afterUserCreateEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterDelete(AfterUserDeleteEvent $afterUserDeleteEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_DELETED, $afterUserDeleteEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterEnterPage(AfterUserEnterPageEvent $afterUserEnterPage): bool
    {
        $userIdentifier = $afterUserEnterPage->getUser()->getId();

        if (!$this->getWhoIsOnlineService()->updateWhoIsOnlineForUserIdentifierWithCurrentTime(
            $userIdentifier
        ))
        {
            return false;
        }

        $userVisit = new UserVisit();
        $userVisit->setUserIdentifier($userIdentifier);
        $userVisit->setEnterDate(time());
        $userVisit->setLocation($afterUserEnterPage->getPageUri());

        if (!$this->getUserTrackingRepository()->createUserVisit($userVisit))
        {
            return false;
        }

        $this->getPageConfiguration()->addHtmlHeader('<script>var tracker=' . $userVisit->getId() . ';</script>');

        return true;
    }

    public function afterExport(AfterUserExportEvent $afterUserExportEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_EXPORTED, $afterUserExportEvent->getTransferUser()->getId(),
                $afterUserExportEvent->getUser()->getId()
            )
        );
    }

    public function afterImport(AfterUserImportEvent $afterUserImportEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_IMPORTED, $afterUserImportEvent->getTransferUser()->getId(),
                $afterUserImportEvent->getUser()->getId()
            )
        );
    }

    public function afterLogin(AfterUserLoginEvent $afterUserLoginEvent): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGIN, $afterUserLoginEvent->getUser()->getId(),
            $afterUserLoginEvent->getClientIpAddress()
        );
    }

    public function afterPasswordReset(AfterUserPasswordResetEvent $afterUserPasswordResetEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_PASSWORD_RESET, $afterUserPasswordResetEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function afterRegistration(AfterUserRegistrationEvent $afterUserRegistrationEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_REGISTERED, $afterUserRegistrationEvent->getUser()->getId()
            )
        );
    }

    public function afterUpdate(AfterUserUpdateEvent $afterUserUpdateEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_UPDATED, $afterUserUpdateEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    public function beforeLeavePage(BeforeUserLeavePageEvent $beforeUserLeavePage): bool
    {
        $userVisit = $this->getUserTrackingRepository()->findUserVisitByIdentifier(
            $beforeUserLeavePage->getUserVisitIdentifier()
        );

        if ($userVisit instanceof UserVisit)
        {
            $userVisit->setLeaveDate(time());

            return $this->getUserTrackingRepository()->updateUserVisit($userVisit);
        }

        return true;
    }

    public function beforeLogout(BeforeUserLogoutEvent $beforeUserLogoutEvent): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGOUT, $beforeUserLogoutEvent->getUser()->getId(),
            $beforeUserLogoutEvent->getClientIpAddress()
        );
    }

    protected function createAuthenticationActivityFormParameters(int $action, string $userIdentifier, ?string $clientIp
    ): bool
    {
        $userAuthenticationActivity = new UserAuthenticationActivity();

        $userAuthenticationActivity->setUserIdentifier($userIdentifier);
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

    public static function getSubscribedEvents(): array
    {
        return [
            AfterUserCreateEvent::class => 'afterUserCreate',
            AfterUserDeleteEvent::class => 'afterUserDelete',
            AfterUserExportEvent::class => 'afterUserExport',
            AfterUserImportEvent::class => 'afterUserImport',
            AfterUserLoginEvent::class => 'afterUserLogin',
            AfterUserPasswordResetEvent::class => 'afterPasswordReset',
            AfterUserRegistrationEvent::class => 'afterRegistration',
            AfterUserUpdateEvent::class => 'afterUpdate',
            AfterUserEnterPageEvent::class => 'afterEnterPage',
            BeforeUserLeavePageEvent::class => 'beforeLeavePage',
            BeforeUserLogoutEvent::class => 'beforeLogout'
        ];
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