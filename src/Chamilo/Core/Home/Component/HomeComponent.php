<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\HomeRenderer;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \QuickformException
     */
    public function run()
    {
        $authenticationValidator = $this->getAuthenticationValidator();
        $authenticationValidator->validate();

        BreadcrumbTrail::getInstance()->truncate();

        $currentTabIdentifier = $this->getRequest()->query->get(self::PARAM_TAB_ID);
        $isGeneralMode = (bool) $this->getSession()->get(Manager::SESSION_GENERAL_MODE, false);

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getHomeRenderer()->render($currentTabIdentifier, $isGeneralMode, $this->getUser());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
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
