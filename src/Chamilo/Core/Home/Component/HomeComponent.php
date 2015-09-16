<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Core\Home\Renderer\Factory;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Configuration\Configuration;

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
        $authenticationValidator = new AuthenticationValidator(Configuration :: get_instance());
        $authenticationValidator->validate();

        BreadcrumbTrail :: get_instance()->truncate();

        $type = $this->getRequest()->query->get(Renderer :: PARAM_VIEW_TYPE, Renderer :: TYPE_BASIC);
        $rendererFactory = new Factory($type, $this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $rendererFactory->getRenderer()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
