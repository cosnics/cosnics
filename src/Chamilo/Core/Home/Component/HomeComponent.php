<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Factory;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Home\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $authenticationValidator = new AuthenticationValidator(
            $this->getRequest(),
            $this->getService('chamilo.configuration.service.configuration_consulter'));
        $authenticationValidator->validate();

        BreadcrumbTrail::getInstance()->truncate();

        $type = $this->getRequest()->query->get(self::PARAM_RENDERER_TYPE, Renderer::TYPE_BASIC);
        $rendererFactory = new Factory($type, $this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $rendererFactory->getRenderer()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
