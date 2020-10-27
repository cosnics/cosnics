<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Component;

use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\LoginTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\UserTemplate;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->set_parameter(\Chamilo\Core\User\Manager::PARAM_USER_USER_ID, $this->get_user_id());

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $template_id = Request::get(self::PARAM_TEMPLATE_ID, 1);
        $this->set_parameter(self::PARAM_TEMPLATE_ID, $template_id);

        switch ($template_id)
        {
            case LoginTemplate::TEMPLATE_ID :
                $class_name = LoginTemplate::class;
                break;
            case UserTemplate::TEMPLATE_ID :
                $class_name = UserTemplate::class;
                break;
            default :
                $class_name = LoginTemplate::class;
                break;
        }

        $application = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Reporting\Viewer\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $application->set_template_by_name($class_name);
        return $application->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(\Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_reporting');
    }
}

?>