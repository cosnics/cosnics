<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

/**
 * @package Chamilo\Core\User\Component
 */
class ReportingComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $this->set_parameter(self::PARAM_USER_USER_ID, $this->getRequest()->query->get(self::PARAM_USER_USER_ID));

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }
}
