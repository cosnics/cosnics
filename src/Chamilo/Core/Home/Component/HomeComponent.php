<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\UserInterface\HomeRenderer\HomeRenderer;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(): Response
    {
        $authenticationValidator = $this->getAuthenticationValidator();
        $authenticationValidator->validate();

        $currentTabIdentifier = $this->getRequest()->query->get(self::PARAM_TAB_ID);

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getHomeRenderer()->render($currentTabIdentifier, $this->getUser());
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    protected function getHomeRenderer(): HomeRenderer
    {
        return $this->getService(HomeRenderer::class);
    }
}
