<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Component\BrowserComponent;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;

/**
 * Admin breadcrumb generator.
 * Generates a breadcrumb based on the administration breadcrumb, the package and component
 * name. Includes the possibility to add additional breadcrumbs between the package breadcrumb and the component
 * breadcrumb
 *
 * @package Chamilo\Core\Admin\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{
    protected function generatePackageBreadcrumb(Application $application): void
    {
        $breadcrumb_trail = $this->getBreadcrumbTrail();
        $translator = $this->getTranslator();

        $adminUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER
            ]
        );
        $breadcrumb_trail->add(new Breadcrumb($adminUrl, $translator->trans('TypeName', [], Manager::CONTEXT)));

        $parentNamespace = $this->getClassnameUtilities()->getNamespaceParent($application::CONTEXT);

        $adminTabUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER,
                BrowserComponent::PARAM_TAB => $parentNamespace
            ]
        );

        $breadcrumb_trail->add(
            new Breadcrumb($adminTabUrl, $translator->trans('TypeName', [], $application::CONTEXT))
        );
    }
}