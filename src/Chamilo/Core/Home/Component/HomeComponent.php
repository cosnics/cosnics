<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Factory;
use Chamilo\Core\Home\Renderer\Renderer;
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
     * Runs this component and displays its output.
     */
    public function run()
    {
        $authenticationValidator = $this->getAuthenticationValidator();
        $authenticationValidator->validate();

        BreadcrumbTrail::getInstance()->truncate();

        $type = $this->getRequest()->query->get(self::PARAM_RENDERER_TYPE, Renderer::TYPE_BASIC);
        $rendererFactory = new Factory($type, $this);

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $rendererFactory->getRenderer()->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return AuthenticationValidator
     */
    protected function getAuthenticationValidator()
    {
        return $this->getService(AuthenticationValidator::class);
    }
}
