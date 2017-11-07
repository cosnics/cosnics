<?php
namespace Chamilo\Core\Admin\Core;

use Chamilo\Core\Admin\Component\BrowserComponent;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * Admin breadcrumb generator.
 * Generates a breadcrumb based on the administration breadcrumb, the package and component
 * name. Includes the possibility to add additional breadcrumbs between the package breadcrumb and the component
 * breadcrumb
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{

    /**
     * Generates the breadcrumb for the package name
     */
    protected function generate_package_breadcrumb()
    {
        $breadcrumb_trail = $this->get_breadcrumb_trail();
        $component = $this->get_component();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::package(), 
                Manager::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER));
        $breadcrumb_trail->add(new Breadcrumb($redirect->getUrl(), Translation::get('TypeName')));
        
        $parentNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($component->package());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::package(), 
                Manager::PARAM_ACTION => Manager::ACTION_ADMIN_BROWSER, 
                BrowserComponent::PARAM_TAB => $parentNamespace));
        $breadcrumb_trail->add(
            new BreadCrumb($redirect->getUrl(), Translation::get('TypeName', null, $component->package())));
    }
}