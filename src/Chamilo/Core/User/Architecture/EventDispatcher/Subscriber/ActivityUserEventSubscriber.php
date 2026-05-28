<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Subscriber;

use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\User\Architecture\Enum\UserActivityTypeEnum;
use Chamilo\Core\User\Architecture\Enum\UserAuthenticationActivityTypeEnum;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Core\User\Storage\Entity\UserActivity;
use Chamilo\Core\User\Storage\Entity\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\Entity\UserVisit;
use Chamilo\Core\User\Storage\Repository\UserActivityRepository;
use Chamilo\Core\User\Storage\Repository\UserAuthenticationActivityRepository;
use Chamilo\Core\User\Storage\Repository\UserVisitRepository;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\User\EventDispatcher\Subscriber
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ActivityUserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected UserActivityRepository $userActivityRepository, protected PageHeaders $pageConfiguration,
        protected OnlineService $onlineService, protected UserVisitRepository $userVisitRepository,
        protected UserAuthenticationActivityRepository $userAuthenticationActivityRepository
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserCreate(AfterUserCreateEvent $afterUserCreateEvent): void
    {
        $this->userActivityRepository->saveUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::CREATED, $afterUserCreateEvent->user->getIdentifier(),
                $afterUserCreateEvent->executingUser instanceof User ?
                    $afterUserCreateEvent->executingUser->getIdentifier() : null
            ), $afterUserCreateEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserDelete(AfterUserDeleteEvent $afterUserDeleteEvent): void
    {
        $this->userActivityRepository->saveUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::DELETED, $afterUserDeleteEvent->user->getIdentifier(),
                $afterUserDeleteEvent->executingUser instanceof User ?
                    $afterUserDeleteEvent->executingUser->getIdentifier() : null
            ), $afterUserDeleteEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserEnterPage(AfterUserEnterPageEvent $afterUserEnterPage): void
    {
        $userIdentifier = $afterUserEnterPage->user->getIdentifier();

        $this->onlineService->updateOnlineForUserIdentifierWithCurrentTime($userIdentifier->toString());

        $userVisit = new UserVisit();

        $userVisit->setIdentifier(new UuidV7());
        $userVisit->setUserIdentifier($userIdentifier);
        $userVisit->setEnterDate(time());
        $userVisit->setLocation($afterUserEnterPage->pageUri);

        $this->userVisitRepository->saveUserVisit($userVisit, $afterUserEnterPage->flush);

        $this->pageConfiguration->addHtml('<script>var tracker="' . $userVisit->getIdentifier() . '";</script>');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserLogin(AfterUserLoginEvent $afterUserLoginEvent): void
    {
        $this->createAuthenticationActivityFromParameters(
            UserAuthenticationActivityTypeEnum::LOGIN, $afterUserLoginEvent->user->getIdentifier(),
            $afterUserLoginEvent->clientIpAddress, $afterUserLoginEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserPasswordReset(AfterUserPasswordResetEvent $afterUserPasswordResetEvent): void
    {
        $this->userActivityRepository->saveUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::PASSWORD_RESET, $afterUserPasswordResetEvent->user->getIdentifier(),
                $afterUserPasswordResetEvent->executingUser instanceof User ?
                    $afterUserPasswordResetEvent->executingUser->getIdentifier() : null
            ), $afterUserPasswordResetEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserRegistration(AfterUserRegistrationEvent $afterUserRegistrationEvent): void
    {
        $this->userActivityRepository->saveUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::REGISTERED, $afterUserRegistrationEvent->user->getIdentifier()
            ), $afterUserRegistrationEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function afterUserUpdate(AfterUserUpdateEvent $afterUserUpdateEvent): void
    {
        $this->userActivityRepository->saveUserActivity(
            $this->initializeUserActivityFromParameters(
                UserActivityTypeEnum::UPDATED, $afterUserUpdateEvent->user->getIdentifier(),
                $afterUserUpdateEvent->executingUser instanceof User ?
                    $afterUserUpdateEvent->executingUser->getIdentifier() : null
            ), $afterUserUpdateEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function beforeUserLeavePage(BeforeUserLeavePageEvent $beforeUserLeavePage): void
    {
        try {
            $userVisit = $this->userVisitRepository->findUserVisitByIdentifier(
                $beforeUserLeavePage->userVisitIdentifier
            );

            $userVisit->setLeaveDate(time());

            $this->userVisitRepository->saveUserVisit($userVisit, $beforeUserLeavePage->flush);
        }
        catch (NoSuchUserVisitException) {
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function beforeUserLogout(BeforeUserLogoutEvent $beforeUserLogoutEvent): void
    {
        $this->createAuthenticationActivityFromParameters(
            UserAuthenticationActivityTypeEnum::LOGOUT, $beforeUserLogoutEvent->user->getIdentifier(),
            $beforeUserLogoutEvent->clientIpAddress, $beforeUserLogoutEvent->flush
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    protected function createAuthenticationActivityFromParameters(
        UserAuthenticationActivityTypeEnum $action, Uuid $userIdentifier, ?string $clientIp, bool $flush = true
    ): void
    {
        $userAuthenticationActivity = new UserAuthenticationActivity();

        $userAuthenticationActivity->setIdentifier(new UuidV7());
        $userAuthenticationActivity->setUserIdentifier($userIdentifier);
        $userAuthenticationActivity->setDate(time());
        $userAuthenticationActivity->setIp($clientIp);
        $userAuthenticationActivity->setAction($action);

        $this->userAuthenticationActivityRepository->saveUserAuthenticationActivity(
            $userAuthenticationActivity, $flush
        );
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
        UserActivityTypeEnum $action, Uuid $targetUserIdentifier, ?Uuid $sourceUserIdentifier = null
    ): UserActivity
    {
        $userActivity = new UserActivity();

        $userActivity->setIdentifier(new UuidV7());
        $userActivity->setAction($action);
        $userActivity->setDate(time());
        $userActivity->setSourceUserIdentifier($sourceUserIdentifier);
        $userActivity->setTargetUserIdentifier($targetUserIdentifier);

        return $userActivity;
    }
}