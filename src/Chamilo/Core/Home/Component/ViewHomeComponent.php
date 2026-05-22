<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\UserInterface\HomeRenderer\HomeRenderer;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewHomeComponent extends Manager implements NoAuthenticationSupportInterface
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected readonly AuthenticationValidator $authenticationValidator,
        protected readonly HomeRenderer $homeRenderer,
        protected readonly GroupMembershipRepository $groupMembershipEntityRepository,
        protected readonly GroupService $groupService
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        /**
         * TODO: Rights
         * - Platform admin
         * - Selected user(s)
         * - Selected group(s)
         * - Selected Entra group(s)
         * -> Via IDM or Graph API?
         * -> Mapping of usernames / user principals
         */
        $this->authenticationValidator->validate();

        //$this->groupMembershipEntityRepository->findGroupMembershipUserIdentifiersByGroupIdentifiers([Uuid::fromString('019df6e6-5100-7db4-9be1-6e958bc70a24')->toBinary()]);

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->homeRenderer->render(null, $currentUser);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
