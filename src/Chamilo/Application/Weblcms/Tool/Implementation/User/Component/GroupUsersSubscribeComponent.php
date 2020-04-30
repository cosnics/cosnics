<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class GroupUsersSubscribeComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $course = $this->get_course();
        $groups = Request::get(self::PARAM_OBJECTS);

        if (! is_array($groups))
        {
            $groups = array($groups);
        }
        if (isset($course))
        {
            if (isset($groups) && $course->is_course_admin($this->get_user()))
            {
                foreach ($groups as $group_id)
                {
                    $this->subscribe_group($group_id, $course);
                }

                $success = true;

                if (count($groups) == 1)
                {
                    $message = 'GroupsSubscribedToCourse';
                }
                else
                {
                    $message = 'GroupsSubscribedToCourse';
                }

                $this->redirect(
                    Translation::get($message),
                    ($success ? false : true),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS));
            }
        }
    }

    public function subscribe_group($group_id, $course)
    {
        $group_users = DataManager::retrieves(
            GroupRelUser::class,
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new StaticConditionVariable($group_id))));

        while ($user = $group_users->next_result())
        {
            $user_id = $user->get_user_id();
            if ($user_id != $this->get_user_id())
            {
                $status = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_STATUS) ? Request::get(
                    \Chamilo\Application\Weblcms\Manager::PARAM_STATUS) : 5;
                \Chamilo\Application\Weblcms\Course\Storage\DataManager::subscribe_user_to_course(
                    $course->get_id(),
                    $status,
                    $user_id);
            }
        }

        $groups = DataManager::retrieves(
            Group::class,
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($group_id))));

        while ($group = $groups->next_result())
        {
            $this->subscribe_group($group->get_id(), $course);
        }
    }
}
