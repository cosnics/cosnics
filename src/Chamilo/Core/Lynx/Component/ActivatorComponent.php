<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Core\Lynx\Action\PackageActivator;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use OutOfBoundsException;

class ActivatorComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {

        try
        {
            $context = $this->getCurrentContext();
            $activator = $this->getPackageActionFactory()->getPackageActivator($this->getCurrentContext());
            $translator = $this->getTranslator();

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    null, $translator->trans(
                    'ActivatingPackage', ['PACKAGE' => $translator->trans('TypeName', [], $context)]
                )
                )
            );

            $activator->run();

            $html = [];

            $html[] = $this->render_header();
            $html[] = $activator->getResult(true);

            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('BackToPackageOVerview'), new FontAwesomeGlyph('backward'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE])
                )
            );

            $html[] = $toolbar->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        catch (OutOfBoundsException)
        {
            throw new NotAllowedException();
        }


    }
}
