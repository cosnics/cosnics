<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLeavePageEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveComponent extends Manager implements NoVisitTraceComponentInterface
{
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        EventDispatcherInterface $eventDispatcher, UrlGenerator $urlGenerator
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->eventDispatcher = $eventDispatcher;
    }

    public function run(?User $currentUser = null): Response
    {
        if ($currentUser instanceof User) {
            $this->getEventDispatcher()->dispatch(
                new BeforeUserLeavePageEvent($currentUser, $this->getRequest()->request->get('tracker'))
            );

            return JsonAjaxResult::success();
        }
        else {
            return JsonAjaxResult::badRequest();
        }
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}