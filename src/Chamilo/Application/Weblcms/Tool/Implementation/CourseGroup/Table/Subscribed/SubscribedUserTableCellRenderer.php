<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    // Inherited
    public function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User::PROPERTY_EMAIL :
                return '<a href="mailto:' . $user[User::PROPERTY_EMAIL] . '">' . $user[User::PROPERTY_EMAIL] . '</a>';
            case CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME :
                $subscriptionTime = $user[CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME];

                if ($subscriptionTime)
                {
                    return DatetimeUtilities::format_locale_date(
                        Translation::getInstance()->getTranslation('SubscriptionTimeFormat', null, Manager::context()),
                        $subscriptionTime);
                }

                return null;
        }

        return parent::render_cell($column, $user);
    }

    public function get_actions($userArray)
    {
        $user = new User($userArray);

        $toolbar = new Toolbar();
        $browser = $this->get_component();
        if ($browser->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $parameters = array();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_UNSUBSCRIBE;
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user->getId();
            $parameters[Manager::PARAM_COURSE_GROUP] = $browser->get_course_group()->getId();
            $unsubscribe_url = $browser->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Unsubscribe'),
                    Theme::getInstance()->getCommonImagePath('Action/Unsubscribe'),
                    $unsubscribe_url,
                    ToolbarItem::DISPLAY_ICON,
                    true));
        }

        $course_group = $browser->get_course_group();

        if (! $browser->is_allowed(WeblcmsRights::EDIT_RIGHT) && $course_group->is_self_unregistration_allowed() &&
             $course_group->is_member($user) && $browser->get_user()->get_id() == $user->getId())
        {
            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->getId();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $browser->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Unsubscribe'),
                    Theme::getInstance()->getCommonImagePath('Action/Unsubscribe'),
                    $unsubscribe_url,
                    ToolbarItem::DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
