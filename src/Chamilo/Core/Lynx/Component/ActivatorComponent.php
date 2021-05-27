<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\NotAllowed;
use Chamilo\Core\Lynx\Action\PackageActivator;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ActivatorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $context = Request::get(self::PARAM_CONTEXT);

        $activator = new PackageActivator($context);
        $activator->run();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, Translation::get(
                'ActivatingPackage', array('PACKAGE' => Translation::get('TypeName', null, $context))
            )
            )
        );

        if ($activator instanceof NotAllowed)
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $activator->get_result(true);

        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('BackToPackageOVerview'), new FontAwesomeGlyph('backward'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE))
            )
        );

        $html[] = $toolbar->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
