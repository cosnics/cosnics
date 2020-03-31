<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubscribedUserBrowser;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Cell renderer for a direct subscribed course user browser table, or users in a direct subscribed group.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class SubscribedUserBrowserTableCellRenderer extends RecordTableCellRenderer
    implements TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Gets the action links to display
     *
     * @param mixed $user_with_subscription_status The user for which the action links should be returned
     *
     * @return string A HTML representation of the action links
     */
    public function get_actions($user_with_subscription_status)
    {
        $user_id = $user_with_subscription_status[User::PROPERTY_ID];

        // construct the toolbar
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        // always show details
        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_USER_DETAILS;
        $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
        $details_url = $this->get_component()->get_url($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Details'), new FontAwesomeGlyph('info-circle'), $details_url,
                ToolbarItem::DISPLAY_ICON
            )
        );

        // display the actions to change the individual status and unsubscribe
        // if:
        // (1) the user is platform or course admin
        // AND
        // (2) the row is not the current user
        // AND
        // (3) we are not editing groups
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $group_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
            if ($user_id != $this->get_component()->get_user()->get_id() && !isset($group_id))
            {
                if ($this->get_component()->get_user()->is_platform_admin() ||
                    CourseManagementRights::getInstance()->is_allowed(
                        CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT, $this->get_component()->get_course_id(),
                        CourseManagementRights::TYPE_COURSE, $user_id
                    ))

                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_UNSUBSCRIBE;
                    $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
                    $parameters[Manager::PARAM_OBJECTS] = $user_id;
                    $unsubscribe_url = $this->get_component()->get_url($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('Unsubscribe'), new FontAwesomeGlyph('minus-square'), $unsubscribe_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('UnsubscribeNotAvailable'), new FontAwesomeGlyph(
                            'minus-square', array('text-muted')
                        ), null, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }

                $weblcms_manager_namespace = \Chamilo\Application\Weblcms\Manager::context();

                switch ($user_with_subscription_status[CourseEntityRelation::PROPERTY_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        $status_change_url = $this->get_component()->get_status_changer_url(
                            $user_id, CourseEntityRelation::STATUS_STUDENT
                        );

                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation::get('MakeStudent'),
                                new FontAwesomeGlyph('user-graduate', array(), null, 'fas'), $status_change_url,
                                ToolbarItem::DISPLAY_ICON
                            )
                        );
                        break;
                    case CourseEntityRelation::STATUS_STUDENT :
                        $status_change_url = $this->get_component()->get_status_changer_url(
                            $user_id, CourseEntityRelation::STATUS_TEACHER
                        );

                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation::get('MakeTeacher'), new FontAwesomeGlyph('user-tie', array(), null, 'fas'),
                                $status_change_url, ToolbarItem::DISPLAY_ICON
                            )
                        );
                        break;
                }
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('UnsubscribeNotAvailable'), new FontAwesomeGlyph(
                        'minus-square', array('text-muted')
                    ), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            // if we have editing rights, display the reporting action
            $params = array();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
            $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_REPORTING;
            $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
            $reporting_url = $this->get_component()->get_url($params);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Report'), new FontAwesomeGlyph('chart-pie'), $reporting_url,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $userViewAllowed = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'allow_view_as_user')
        );

        // add action for view as user
        if (($userViewAllowed || $this->get_component()->get_user()->is_platform_admin()) &&
            $this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT)) // get_parent()->is_teacher())
        {
            if ($user_id != $this->get_component()->get_user()->get_id())
            {
                $course_settings_controller = CourseSettingsController::getInstance();
                $course_access = $course_settings_controller->get_course_setting(
                    $this->get_component()->get_course(), CourseSettingsConnector::COURSE_ACCESS
                );

                // if ($course_access != CourseSettingsConnector :: COURSE_ACCESS_CLOSED)
                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_VIEW_AS;
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
                    $view_as_url = $this->get_component()->get_url($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('ViewAsUser'), new FontAwesomeGlyph('mask'), $view_as_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
        }

        // return
        return $toolbar->as_html();
    }

    /**
     * Renders a given cell.
     *
     * @param $column type
     * @param mixed $user_with_subscription_status User from the advanced join query in weblcms database class that
     *        includes his subscription status.
     *
     * @return string
     */
    public function render_cell($column, $user_with_subscription_status)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case CourseEntityRelation::PROPERTY_STATUS :
                switch ($user_with_subscription_status[CourseEntityRelation::PROPERTY_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return Translation::get('CourseAdmin');
                    case CourseEntityRelation::STATUS_STUDENT :
                        return Translation::get('Student');
                    default :
                        return Translation::get('Unknown');
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($user_with_subscription_status[User::PROPERTY_PLATFORMADMIN] == '1')
                {
                    return Translation::get('PlatformAdministrator');
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_EMAIL :
                $email = $user_with_subscription_status[User::PROPERTY_EMAIL];

                $activeOnlineEmailEditor = Configuration::getInstance()->get_setting(
                    array('Chamilo\Core\Admin', 'active_online_email_editor')
                );

                if ($activeOnlineEmailEditor)
                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_EMAIL;
                    $parameters[Manager::PARAM_OBJECTS] = $user_with_subscription_status[User::PROPERTY_ID];
                    $email_url = $this->get_component()->get_url($parameters);
                }
                else
                {
                    $email_url = 'mailto:' . $email;
                }

                return '<a href="' . $email_url . '">' . $email . '</a>';
        }

        return parent::render_cell($column, $user_with_subscription_status);
    }
}
