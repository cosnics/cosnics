<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Cell renderer for an all subscribed course user browser table.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class AllSubscribedUserTableCellRenderer extends RecordTableCellRenderer implements
    TableCellRendererActionsColumnSupport
{

    /**
     * Cache to store the status of unknown groups after retrieval.
     */
    private $unknown_status_cache;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param Table $table
     */
    public function __construct($table)
    {
        parent :: __construct($table);
        $this->unknown_status_cache = array();
    }

    /**
     * Renders a given cell.
     *
     * @param $column type
     * @param mixed[] $user_with_subscription_status_and_type
     *
     * @return string
     */
    public function render_cell($column, $user_with_subscription_status_and_type)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case AllSubscribedUserTableColumnModel :: SUBSCRIPTION_TYPE :
                $type = $user_with_subscription_status_and_type[AllSubscribedUserTableColumnModel :: SUBSCRIPTION_TYPE];
                switch ($type)
                {
                    case 1 :
                        return Translation :: get('SubscribedDireclty');
                    case 2 :
                        return Translation :: get('SubscribedGroup');
                    default :
                        return ($type % 2 == 0) ? Translation :: get('SubscribedGroup') : Translation :: get(
                            'SubscribedDirecltyAndGroup');
                }
            case AllSubscribedUserTableColumnModel :: SUBSCRIPTION_STATUS :
                switch ($user_with_subscription_status_and_type[AllSubscribedUserTableColumnModel :: SUBSCRIPTION_STATUS])
                {
                    case CourseUserRelation :: STATUS_TEACHER :
                        return Translation :: get('CourseAdmin');
                    case CourseUserRelation :: STATUS_STUDENT :
                        return Translation :: get('Student');
                    default :
                        return Translation :: get('Unknown');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user_with_subscription_status_and_type[User :: PROPERTY_PLATFORM_ADMIN] == '1')
                {
                    return Translation :: get('PlatformAdministrator');
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_EMAIL :
                $email = $user_with_subscription_status_and_type[User :: PROPERTY_EMAIL];

                if (PlatformSetting :: get('active_online_email_editor'))
                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_EMAIL;
                    $parameters[Manager :: PARAM_OBJECTS] = $user_with_subscription_status_and_type[User :: PROPERTY_ID];
                    $email_url = $this->get_component()->get_url($parameters);
                }
                else
                {
                    $email_url = 'mailto:' . $email;
                }
                return '<a href="' . $email_url . '">' . $email . '</a>';
        }

        return parent :: render_cell($column, $user_with_subscription_status_and_type);
    }

    /**
     * Gets the action links to display
     *
     * @param mixed[] $user_with_subscription_status
     *
     * @return string
     */
    public function get_actions($user_with_subscription_status_and_type)
    {
        $user_id = $user_with_subscription_status_and_type[User :: PROPERTY_ID];

        // construct the toolbar
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_USER_DETAILS;
        $parameters[Manager :: PARAM_TAB] = Request :: get(Manager :: PARAM_TAB);
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user_id;
        $details_url = $this->get_component()->get_url($parameters);

        // always show details
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Details'),
                Theme :: getInstance()->getCommonImagePath('action_details'),
                $details_url,
                ToolbarItem :: DISPLAY_ICON));

        // display the actions to change the individual status and unsubscribe
        // if:
        // (1) the user has edit rights
        // AND
        // (2) the row is not the current user
        // AND
        // (3) the row is not a group-only subscription
        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            if ($user_id != $this->get_component()->get_user()->get_id() && $user_with_subscription_status_and_type[AllSubscribedUserTableColumnModel :: SUBSCRIPTION_TYPE] %
                 2)
            {
                if ($this->get_component()->get_user()->is_platform_admin() || CourseManagementRights :: get_instance()->is_allowed(
                    CourseManagementRights :: TEACHER_UNSUBSCRIBE_RIGHT,
                    $this->get_component()->get_course_id(),
                    CourseManagementRights :: TYPE_COURSE,
                    $user_id))

                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_UNSUBSCRIBE;
                    $parameters[Manager :: PARAM_TAB] = Request :: get(Manager :: PARAM_TAB);
                    $parameters[Manager :: PARAM_OBJECTS] = $user_id;
                    $unsubscribe_url = $this->get_component()->get_url($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('DirectUnsubscribe'),
                            Theme :: getInstance()->getCommonImagePath('action_unsubscribe'),
                            $unsubscribe_url,
                            ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('UnsubscribeNotAvailable'),
                            Theme :: getInstance()->getCommonImagePath('action_unsubscribe_na'),
                            null,
                            ToolbarItem :: DISPLAY_ICON));
                }

                $weblcms_manager_namespace = \Chamilo\Application\Weblcms\Manager :: context();

                switch ($user_with_subscription_status_and_type[AllSubscribedUserTableColumnModel :: SUBSCRIPTION_STATUS])
                {
                    case CourseUserRelation :: STATUS_TEACHER :
                        $status_change_url = $this->get_component()->get_status_changer_url(
                            $user_id,
                            CourseUserRelation :: STATUS_STUDENT);

                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation :: get('MakeStudent'),
                                Theme :: getInstance()->getImagePath(
                                    $weblcms_manager_namespace,
                                    'Action/SubscribeStudent'),
                                $status_change_url,
                                ToolbarItem :: DISPLAY_ICON));
                        break;
                    case CourseUserRelation :: STATUS_STUDENT :
                        $status_change_url = $this->get_component()->get_status_changer_url(
                            $user_id,
                            CourseUserRelation :: STATUS_TEACHER);

                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation :: get('MakeTeacher'),
                                Theme :: getInstance()->getImagePath(
                                    $weblcms_manager_namespace,
                                    'Action/SubscribeTeacher'),
                                $status_change_url,
                                ToolbarItem :: DISPLAY_ICON));
                        break;
                }
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('UnsubscribeNotAvailable'),
                        Theme :: getInstance()->getCommonImagePath('action_unsubscribe_na'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }

            // if we have editing rights, display the reporting action
            $params = array();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user_id;
            $params[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_REPORTING;
            $parameters[Manager :: PARAM_TAB] = Request :: get(Manager :: PARAM_TAB);
            $reporting_url = $this->get_component()->get_url($params);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Report'),
                    Theme :: getInstance()->getCommonImagePath('action_reporting'),
                    $reporting_url,
                    ToolbarItem :: DISPLAY_ICON));
        }

        // add action for view as user
        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT)) // ->get_parent()->is_teacher())
        {
            if ($user_id != $this->get_component()->get_user()->get_id())
            {
                $parameters = array();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_AS;
                $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user_id;
                $view_as_url = $this->get_component()->get_url($parameters);

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('ViewAsUser'),
                        Theme :: getInstance()->getCommonImagePath('action_login'),
                        $view_as_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        // add actions from connectors

        $connector_items = DataManager :: get_connector_toolbar_items($user_id);
        foreach ($connector_items as $item)
        {
            $toolbar->add_item($item);
        }

        // return
        return $toolbar->as_html();
    }
}
