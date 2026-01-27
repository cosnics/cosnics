<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserExportEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserImportEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\DataClass\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserTrackingRepository;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageConfiguration;
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

    protected OnlineService $whoIsOnlineService;

    public function __construct(
        UserTrackingRepository $userTrackingRepository, ?User $currentUser, PageConfiguration $pageConfiguration,
        OnlineService $whoIsOnlineService
    )
    {
        $this->userTrackingRepository = $userTrackingRepository;
        $this->currentUser = $currentUser;
        $this->pageConfiguration = $pageConfiguration;
        $this->whoIsOnlineService = $whoIsOnlineService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserCreate(AfterUserCreateEvent $afterUserCreateEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_CREATED, $afterUserCreateEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserDelete(AfterUserDeleteEvent $afterUserDeleteEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_DELETED, $afterUserDeleteEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserEnterPage(AfterUserEnterPageEvent $afterUserEnterPage): bool
    {
        $userIdentifier = $afterUserEnterPage->getUser()->getId();

        if (!$this->getWhoIsOnlineService()->updateOnlineForUserIdentifierWithCurrentTime(
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserExport(AfterUserExportEvent $afterUserExportEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_EXPORTED, $afterUserExportEvent->getTransferUser()->getId(),
                $afterUserExportEvent->getUser()->getId()
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserImport(AfterUserImportEvent $afterUserImportEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_IMPORTED, $afterUserImportEvent->getTransferUser()->getId(),
                $afterUserImportEvent->getUser()->getId()
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserLogin(AfterUserLoginEvent $afterUserLoginEvent): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGIN, $afterUserLoginEvent->getUser()->getId(),
            $afterUserLoginEvent->getClientIpAddress()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserPasswordReset(AfterUserPasswordResetEvent $afterUserPasswordResetEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_PASSWORD_RESET, $afterUserPasswordResetEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserRegistration(AfterUserRegistrationEvent $afterUserRegistrationEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_REGISTERED, $afterUserRegistrationEvent->getUser()->getId()
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function afterUserUpdate(AfterUserUpdateEvent $afterUserUpdateEvent): bool
    {
        return $this->getUserTrackingRepository()->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivity::ACTIVITY_UPDATED, $afterUserUpdateEvent->getUser()->getId(),
                $this->getCurrentUser() instanceof User ? $this->getCurrentUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function beforeUserLeavePage(BeforeUserLeavePageEvent $beforeUserLeavePage): bool
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function beforeUserLogout(BeforeUserLogoutEvent $beforeUserLogoutEvent): bool
    {
        return $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGOUT, $beforeUserLogoutEvent->getUser()->getId(),
            $beforeUserLogoutEvent->getClientIpAddress()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
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
            AfterUserPasswordResetEvent::class => 'afterUserPasswordReset',
            AfterUserRegistrationEvent::class => 'afterUserRegistration',
            AfterUserUpdateEvent::class => 'afterUserUpdate',
            AfterUserEnterPageEvent::class => 'afterUserEnterPage',
            BeforeUserLeavePageEvent::class => 'beforeUserLeavePage',
            BeforeUserLogoutEvent::class => 'beforeUserLogout'
        ];
    }

    public function getUserTrackingRepository(): UserTrackingRepository
    {
        return $this->userTrackingRepository;
    }

    public function getWhoIsOnlineService(): OnlineService
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