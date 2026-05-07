<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\User\Architecture\Enum\UserActivityTypeEnum;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
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
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Core\User\EventDispatcher\Subscriber
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ActivityUserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected UserTrackingRepository $userTrackingRepository, protected PageHeaders $pageConfiguration,
        protected OnlineService $onlineService
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserCreate(AfterUserCreateEvent $afterUserCreateEvent): void
    {
        $this->userTrackingRepository->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::CREATED, $afterUserCreateEvent->getUser()->getId(),
                $afterUserCreateEvent->getExecutingUser() instanceof User ?
                    $afterUserCreateEvent->getExecutingUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserDelete(AfterUserDeleteEvent $afterUserDeleteEvent): void
    {
        $this->userTrackingRepository->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::DELETED, $afterUserDeleteEvent->getUser()->getId(),
                $afterUserDeleteEvent->getExecutingUser() instanceof User ?
                    $afterUserDeleteEvent->getExecutingUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserEnterPage(AfterUserEnterPageEvent $afterUserEnterPage): void
    {
        $userIdentifier = $afterUserEnterPage->getUser()->getId();

        $this->onlineService->updateOnlineForUserIdentifierWithCurrentTime(
            $userIdentifier
        );

        $userVisit = new UserVisit(
            enterDate: time(), location: $afterUserEnterPage->getPageUri(), userIdentifier: $userIdentifier
        );
//        $userVisit->setUserIdentifier($userIdentifier);
//        $userVisit->setEnterDate(time());
//        $userVisit->setLocation($afterUserEnterPage->getPageUri());

        $this->userTrackingRepository->createUserVisit($userVisit);

        $this->pageConfiguration->addHtml('<script>var tracker="' . $userVisit->getId() . '";</script>');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserLogin(AfterUserLoginEvent $afterUserLoginEvent): void
    {
        $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGIN, $afterUserLoginEvent->getUser()->getId(),
            $afterUserLoginEvent->getClientIpAddress()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserPasswordReset(AfterUserPasswordResetEvent $afterUserPasswordResetEvent): void
    {
        $this->userTrackingRepository->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::PASSWORD_RESET, $afterUserPasswordResetEvent->getUser()->getId(),
                $afterUserPasswordResetEvent->getExecutingUser() instanceof User ?
                    $afterUserPasswordResetEvent->getExecutingUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserRegistration(AfterUserRegistrationEvent $afterUserRegistrationEvent): void
    {
        $this->userTrackingRepository->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::REGISTERED, $afterUserRegistrationEvent->getUser()->getId()
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function afterUserUpdate(AfterUserUpdateEvent $afterUserUpdateEvent): void
    {
        $this->userTrackingRepository->createUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::UPDATED, $afterUserUpdateEvent->getUser()->getId(),
                $afterUserUpdateEvent->getExecutingUser() instanceof User ?
                    $afterUserUpdateEvent->getExecutingUser()->getId() : null
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException
     */
    public function beforeUserLeavePage(BeforeUserLeavePageEvent $beforeUserLeavePage): void
    {
        $userVisit = $this->userTrackingRepository->findUserVisitByIdentifier(
            $beforeUserLeavePage->getUserVisitIdentifier()
        );

        if ($userVisit instanceof UserVisit) {
            $userVisit->setLeaveDate(time());

            $this->userTrackingRepository->updateUserVisit($userVisit);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function beforeUserLogout(BeforeUserLogoutEvent $beforeUserLogoutEvent): void
    {
        $this->createAuthenticationActivityFormParameters(
            UserAuthenticationActivity::ACTIVITY_LOGOUT, $beforeUserLogoutEvent->getUser()->getId(),
            $beforeUserLogoutEvent->getClientIpAddress()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    protected function createAuthenticationActivityFormParameters(int $action, string $userIdentifier, ?string $clientIp
    ): void
    {
        $userAuthenticationActivity = new UserAuthenticationActivity();

        $userAuthenticationActivity->setUserIdentifier($userIdentifier);
        $userAuthenticationActivity->setDate(time());
        $userAuthenticationActivity->setIp($clientIp);
        $userAuthenticationActivity->setAction($action);

        $this->userTrackingRepository->createUserAuthenticationActivity($userAuthenticationActivity);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterUserCreateEvent::class => 'afterUserCreate',
            AfterUserDeleteEvent::class => 'afterUserDelete',
            AfterUserLoginEvent::class => 'afterUserLogin',
            AfterUserPasswordResetEvent::class => 'afterUserPasswordReset',
            AfterUserRegistrationEvent::class => 'afterUserRegistration',
            AfterUserUpdateEvent::class => 'afterUserUpdate',
            AfterUserEnterPageEvent::class => 'afterUserEnterPage',
            BeforeUserLeavePageEvent::class => 'beforeUserLeavePage',
            BeforeUserLogoutEvent::class => 'beforeUserLogout'
        ];
    }

    protected function initializeUserActivityFromParameters(
        UserActivityTypeEnum $action, string $targetUserIdentifier, ?string $sourceUserIdentifier = null
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