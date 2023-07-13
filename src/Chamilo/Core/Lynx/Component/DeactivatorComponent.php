<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\NotAllowed;
use Chamilo\Core\Lynx\Action\PackageDeactivator;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

class DeactivatorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $context = $this->getRequest()->query->get(self::PARAM_CONTEXT);
        $deactivator = new PackageDeactivator($context);
        $deactivator->run();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, Translation::get(
                'DeactivatingPackage', ['PACKAGE' => Translation::get('TypeName', null, $this->context)]
            )
            )
        );

        if ($deactivator instanceof notallowed)
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $deactivator->getResult(true);

        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('BackToPackageOVerview'), new FontAwesomeGlyph('backward'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE])
            )
        );

        $html[] = $toolbar->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
