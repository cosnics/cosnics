<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * $Id: user_field_builder.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class UserFieldsBuilderComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUserFields');

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $application = $this->getApplicationFactory()->getApplication(
            \Chamilo\Configuration\Form\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $application->set_form_by_name('account_fields');

        return $application->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_fields_builder');
    }
}
