<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Component;

use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\DataTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\LoginTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\UserTemplate;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

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
                $class_name = LoginTemplate::class_name();
                break;
            case DataTemplate::TEMPLATE_ID :
                $class_name = DataTemplate::class_name();
                break;
            case UserTemplate::TEMPLATE_ID :
                $class_name = UserTemplate::class_name();
                break;
            default :
                $class_name = DataTemplate::class_name();
                break;
        }
        
        $factory = new ApplicationFactory(
            \Chamilo\Core\Reporting\Viewer\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component = $factory->getComponent();
        $component->set_template_by_name($class_name);
        return $component->run();
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