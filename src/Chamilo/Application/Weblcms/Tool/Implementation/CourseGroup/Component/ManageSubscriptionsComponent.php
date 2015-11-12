<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupSubscriptionsForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_manage_subscriptions.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class ManageSubscriptionsComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $course_group_id = Request :: get(self :: PARAM_COURSE_GROUP);
        $this->set_parameter(self :: PARAM_COURSE_GROUP, $course_group_id);

        $course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $course_group_id);

        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get('ManageSubscriptionComponent', array('GROUPNAME' => $course_group->get_name()))));
        // $trail = BreadcrumbTrail :: get_instance();

        $form = new CourseGroupSubscriptionsForm(
            $course_group,
            $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_MANAGE_SUBSCRIPTIONS,
                    self :: PARAM_COURSE_GROUP => $course_group->get_id())),
            $this);
        if ($form->validate())
        {
            $succes = $form->update_course_group_subscriptions();

            if ($succes)
                $message = Translation :: get(
                    'CourseGroupSubscriptionsUpdated',
                    array('OBJECT' => Translation :: get('CourseGroup'))) . // , Utilities :: COMMON_LIBRARIES
'<br />' . implode('<br />', $course_group->get_errors());
                // $message = 'CourseGroupSubscriptionsUpdated';
            else
                // $message = 'MaximumAmountOfMembersReached';

                $message = Translation :: get(
                    'ObjectNotUpdated',
                    array('OBJECT' => Translation :: get('CourseGroup')),
                    Utilities :: COMMON_LIBRARIES) . '<br />' . implode('<br />', $course_group->get_errors());
            $this->redirect(
                $message,
                ! $succes,
                array(
                    self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE,
                    self :: PARAM_COURSE_GROUP => $course_group->get_id()));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<h3>' . Translation :: get('CourseGroup') . ': ' . $course_group->get_name() . '</h3>';
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_group_manage_subscription');
    }
}
