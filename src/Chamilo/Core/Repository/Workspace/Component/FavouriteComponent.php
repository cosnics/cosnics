<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteComponent extends TabComponent implements DelegateComponent
{

    public function build()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\Workspace\Favourite\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_WORKSPACE_ID);
    }
}