<?php
namespace Chamilo\Core\Repository;

use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;

class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{

    /**
     * Generates the breadcrumb for the package name
     */
    protected function generate_package_breadcrumb()
    {
        if ($this->get_component()->get_user() instanceof User)
        {
            $breadcrumb_trail = $this->get_breadcrumb_trail();

            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = Manager :: context();

            if ($this->get_component()->getWorkspace() instanceof Workspace)
            {
                $parameters[Manager :: PARAM_WORKSPACE_ID] = $this->get_component()->getWorkspace()->getId();
            }

            $redirect = new Redirect($parameters);

            $breadcrumb_trail->add(
                new Breadcrumb($redirect->getUrl(), $this->get_component()->getWorkspace()->getTitle()));
        }
    }
}