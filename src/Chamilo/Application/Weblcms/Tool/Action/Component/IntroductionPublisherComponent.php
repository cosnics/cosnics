<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: introduction_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.component
 */
class IntroductionPublisherComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface,
    DelegateComponent
{

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            throw new NotAllowedException();
        }

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager :: SETTING_TABS_DISABLED, true);

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                $applicationConfiguration);

            $component = $factory->getComponent();
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager :: SELECT_SINGLE);
            $component->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH_INTRODUCTION);

            return $component->run();
        }
        else
        {
            $pub = new ContentObjectPublication();
            $pub->set_content_object_id(\Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects());
            $pub->set_course_id($this->get_course_id());
            $pub->set_tool($this->get_tool_id());
            $pub->set_category_id(0);
            $pub->set_from_date(0);
            $pub->set_to_date(0);
            $pub->set_publisher_id(Session :: get_user_id());
            $pub->set_publication_date(time());
            $pub->set_modified_date(time());
            $pub->set_hidden(0);
            $pub->set_email_sent(0);
            $pub->set_show_on_homepage(0);
            $pub->set_allow_collaboration(1);

            $pub->ignore_display_order();

            $pub->create();

            $parameters = $this->get_parameters();
            $parameters['tool_action'] = null;

            $this->redirect(
                Translation :: get(
                    'ObjectPublished',
                    array('OBJECT' => Translation :: get('Introduction')),
                    Utilities :: COMMON_LIBRARIES),
                (false),
                $parameters);
        }
    }

    public function get_allowed_content_object_types()
    {
        return array(Introduction :: class_name());
    }
}
