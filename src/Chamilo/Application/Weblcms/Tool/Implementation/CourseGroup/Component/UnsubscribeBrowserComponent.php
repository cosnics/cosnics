<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed\SubscribedUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_unsubscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class UnsubscribeBrowserComponent extends Manager implements TableSupport, DelegateComponent
{

    private $action_bar;

    private $course_group;

    public function run()
    {
        $course_group_id = Request :: get(self :: PARAM_COURSE_GROUP);

        $course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $course_group_id);
        if (! $course_group)
            throw new ObjectNotExistException(Translation :: get('CourseGroup'), $course_group_id);

        $this->course_group = $course_group;
        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get('UnsubscribeBrowserComponent', array('GROUPNAME' => $course_group->get_name()))));

        $this->action_bar = $this->get_action_bar();

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<div style="clear: both;">&nbsp;</div>';

        if (Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS))
        {
            $users = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS);
            if (! is_array($users))
            {
                $users = array($users);
            }

            foreach ($users as $user)
            {
                $course_group->unsubscribe_users($user);
            }

            $message = Translation :: get(count($users) > 1 ? 'UsersUnsubscribed' : 'UserUnsubscribed');
            $this->redirect(
                $message,
                false,
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE,
                    self :: PARAM_COURSE_GROUP => $course_group_id));
        }

        $table = new SubscribedUserTable($this);
        $html[] = $this->action_bar->as_html();

        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getCommonImagePath() . 'place_group.png);">';
        $html[] = '<div class="title">' . $course_group->get_name() . '</div>';
        $html[] = '<div class="description">' . $course_group->get_description() . '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<b>' . Translation :: get('NumberOfMembers') . ':</b> ' . $course_group->count_members();
        $html[] = '<br /><b>' . Translation :: get('MaximumMembers') . ':</b> ' .
             $course_group->get_max_number_of_members();
        $html[] = '<br /><b>' . Translation :: get('SelfRegistrationAllowed') . ':</b> ' . ($course_group->is_self_registration_allowed() ? Translation :: get(
            'ConfirmYes',
            null,
            Utilities :: COMMON_LIBRARIES) : Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES));
        $html[] = '<br /><b>' . Translation :: get('SelfUnRegistrationAllowed') . ':</b> ' . ($course_group->is_self_unregistration_allowed() ? Translation :: get(
            'ConfirmYes',
            null,
            Utilities :: COMMON_LIBRARIES) : Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES));
        $html[] = '<br /><b>' . Translation :: get('RandomlySubscribed') . ':</b> ' . ($course_group->is_random_registration_done() ? Translation :: get(
            'ConfirmYes',
            null,
            Utilities :: COMMON_LIBRARIES) : Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES));
        $html[] = '</div>';

        $html[] = '<div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getCommonImagePath() . 'place_users.png);">';
        $html[] = '<div class="title">' . Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context()) .
             '</div>';
        $html[] = $table->as_html();
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_action_bar()
    {
        $course_group = $this->course_group;
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                $this->get_url($parameters),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $user = $this->get_parent()->get_user();

        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();

        if (! $this->get_parent()->is_teacher())
        {
            if ($course_group->is_self_registration_allowed() && ! $course_group->is_member($user))
            {
                $parameters = array();
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();
                $parameters[self :: PARAM_COURSE_GROUP_ACTION] = self :: ACTION_USER_SELF_SUBSCRIBE;
                $subscribe_url = $this->get_url($parameters);

                $action_bar->add_common_action(
                    new ToolbarItem(
                        Translation :: get('SubscribeToGroup'),
                        Theme :: getInstance()->getCommonImagePath() . 'action_subscribe.png',
                        $subscribe_url,
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            if ($course_group->is_self_unregistration_allowed() && $course_group->is_member($user))
            {
                $parameters = array();
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();
                $parameters[self :: PARAM_COURSE_GROUP_ACTION] = self :: ACTION_USER_SELF_UNSUBSCRIBE;
                $unsubscribe_url = $this->get_url($parameters);

                $action_bar->add_common_action(
                    new ToolbarItem(
                        Translation :: get('UnSubscribeFromGroup'),
                        Theme :: getInstance()->getCommonImagePath() . 'action_unsubscribe.png',
                        $unsubscribe_url,
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        else
        {
            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[self :: PARAM_COURSE_GROUP_ACTION] = self :: ACTION_MANAGE_SUBSCRIPTIONS;
            $subscribe_url = $this->get_url($parameters);

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('SubscribeUsers'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_subscribe.png',
                    $subscribe_url,
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            // export the list to a spreadsheet
            $parameters_export_subscriptions_overview = array();
            $parameters_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = self :: ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW;
            $parameters_export_subscriptions_overview[self :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath() . 'action_backup.png',
                    $this->get_url($parameters_export_subscriptions_overview),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        // $action_bar->add_tool_action(new ToolbarItem(Translation ::
        // get('Edit'), Theme :: getInstance()->getCommonImagePath().'action_edit.png',
        // $this->get_url(array(CourseGroupTool :: PARAM_ACTION =>
        // CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem ::
        // DISPLAY_ICON_AND_LABEL));
        // $action_bar->add_tool_action(new ToolbarItem(Translation ::
        // get('Delete'), Theme :: getInstance()->getCommonImagePath().'action_delete.png',
        // $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($course_group->get_document_category_id())
        {
            $type_name = 'document';
            $params = array();
            $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
            $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course_group->get_course_code();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $type_name;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager :: ACTION_BROWSE;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY] = $course_group->get_document_category_id();
            $url = $this->get_url($params);

            $namespace = \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($type_name);
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('TypeName', null, $namespace),
                    Theme :: getInstance()->getImagePath($namespace) . 'Logo/16.png',
                    $url,
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($course_group->get_forum_category_id())
        {
            $type_name = 'forum';
            $params = array();
            $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
            $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course_group->get_course_code();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $type_name;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager :: ACTION_BROWSE;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY] = $course_group->get_forum_category_id();
            $url = $this->get_url($params);

            $namespace = \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($type_name);
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('TypeName', null, $namespace),
                    Theme :: getInstance()->getImagePath($namespace) . 'Logo/16.png',
                    $url,
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    public function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                '*' . $query . '*');
            return new OrCondition($conditions);
        }
    }

    public function get_course_group()
    {
        return $this->course_group;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_group_unsubscribe_browser');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_COURSE_GROUP);
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
