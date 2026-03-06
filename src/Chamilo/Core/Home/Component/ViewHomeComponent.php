<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\UserInterface\HomeRenderer\HomeRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewHomeComponent extends Manager implements NoAuthenticationSupportInterface
{
    protected AuthenticationValidator $authenticationValidator;

    protected HomeRenderer $homeRenderer;

    public function __construct(AuthenticationValidator $authenticationValidator, HomeRenderer $homeRenderer)
    {
        $this->authenticationValidator = $authenticationValidator;
        $this->homeRenderer = $homeRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
        $authenticationValidator = $this->getAuthenticationValidator();
        $authenticationValidator->validate();

        $currentTabIdentifier = $this->getRequest()->query->get(self::PARAM_TAB_ID);

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getHomeRenderer()->render($currentTabIdentifier, $currentUser);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    protected function getHomeRenderer(): HomeRenderer
    {
        return $this->homeRenderer;
    }
}
