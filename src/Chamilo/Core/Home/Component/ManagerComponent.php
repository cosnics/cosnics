<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * $Id: manager.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.home_manager.component
 */
class ManagerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if ($this->get_user()->is_platform_admin())
        {
            \Chamilo\Libraries\Platform\Session\Session :: register(__NAMESPACE__ . '\general', '1');
        }
        Redirect :: link();
    }

    /**
     * Returns the admin breadcrumb generator
     * 
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
