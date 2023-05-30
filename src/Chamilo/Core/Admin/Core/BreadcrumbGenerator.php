<?php
namespace Chamilo\Core\Admin\Core;

use Chamilo\Core\Admin\Component\BrowserComponent;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * Admin breadcrumb generator.
 * Generates a breadcrumb based on the administration breadcrumb, the package and component
 * name. Includes the possibility to add additional breadcrumbs between the package breadcrumb and the component
 * breadcrumb
 *
 * @package common\libraries
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{
    /**
     * Generates the breadcrumb for the package name
     */
    protected function generatePackageBreadcrumb()
    {
        $breadcrumb_trail = $this->getBreadcrumbTrail();
        $component = $this->getApplication();

        $adminUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER
            ]
        );
        $breadcrumb_trail->add(new Breadcrumb($adminUrl, Translation::get('TypeName')));

        $parentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($component::CONTEXT);

        $adminTabUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER,
                BrowserComponent::PARAM_TAB => $parentNamespace
            ]
        );

        $breadcrumb_trail->add(
            new Breadcrumb($adminTabUrl, Translation::get('TypeName', null, $component::CONTEXT))
        );
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }
}