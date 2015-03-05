<?php
namespace Chamilo\Core\Admin\Core;

use Chamilo\Core\Admin\Component\BrowserComponent;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
        
        $breadcrumb_trail->add(
            new Breadcrumb(
                Redirect :: get_link(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER), 
                    array(), 
                    false, 
                    Redirect :: TYPE_CORE), 
                Translation :: get('TypeName')));
        
        $tab = 'core';
        
        $breadcrumb_trail->add(
            new BreadCrumb(
                Redirect :: get_link(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER, 
                        BrowserComponent :: PARAM_TAB => $tab), 
                    array(), 
                    false, 
                    Redirect :: TYPE_CORE), 
                Translation :: get((string) StringUtilities :: getInstance()->createString($tab)->upperCamelize())));
        
        $breadcrumb_trail->add(
            new BreadCrumb(
                Redirect :: get_link(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER, 
                        BrowserComponent :: PARAM_TAB => $tab), 
                    array(), 
                    false, 
                    Redirect :: TYPE_CORE), 
                Translation :: get('TypeName', null, $component->context())));
    }
}