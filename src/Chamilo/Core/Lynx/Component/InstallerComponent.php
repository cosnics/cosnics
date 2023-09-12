<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Core\Lynx\Action\PackageInstaller;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

class InstallerComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $context = $this->getRequest()->query->get(self::PARAM_CONTEXT);
        $installer = new PackageInstaller($context);
        $installer->run();

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                null, Translation::get(
                'InstallingPackage', ['PACKAGE' => Translation::get('TypeName', null, $context)]
            )
            )
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $installer->getResult(true);

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
