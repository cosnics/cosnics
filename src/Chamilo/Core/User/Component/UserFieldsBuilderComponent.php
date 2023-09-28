<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\User\Component
 */
class UserFieldsBuilderComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUserFields');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $application = $this->getApplicationFactory()->getApplication(
            \Chamilo\Configuration\Form\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );
        $application->set_form_by_name('account_fields');

        return $application->run();
    }
}
