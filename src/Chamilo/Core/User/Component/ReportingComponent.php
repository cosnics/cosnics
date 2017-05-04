<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * $Id: reporting.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package user.lib.user_manager.component
 */
class ReportingComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');
        
        $this->set_parameter(self::PARAM_USER_USER_ID, $this->getRequest()->query->get(self::PARAM_USER_USER_ID));
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $factory = new ApplicationFactory(
            \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

        return $factory->run();
    }
}
