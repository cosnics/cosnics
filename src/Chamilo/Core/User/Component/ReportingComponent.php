<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
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

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this))->run();
    }
}
