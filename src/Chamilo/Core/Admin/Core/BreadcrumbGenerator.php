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

        $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER));
        $breadcrumb_trail->add(new Breadcrumb($redirect->getUrl(), Translation :: get('TypeName')));

        $tab = 'core';

        $redirect = new Redirect(
            array(Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER, BrowserComponent :: PARAM_TAB => $tab));
        $breadcrumb_trail->add(
            new BreadCrumb(
                $redirect->getUrl(),
                Translation :: get((string) StringUtilities :: getInstance()->createString($tab)->upperCamelize())));

        $redirect = new Redirect(
            array(Manager :: PARAM_ACTION => Manager :: ACTION_ADMIN_BROWSER, BrowserComponent :: PARAM_TAB => $tab));
        $breadcrumb_trail->add(
            new BreadCrumb($redirect->getUrl(), Translation :: get('TypeName', null, $component->context())));
    }
}