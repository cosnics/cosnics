<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;

/**
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class GroupUsersSubscribeComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $course = $this->get_course();
        $groups = $this->getRequest()->query->get(self::PARAM_OBJECTS);

        if (!is_array($groups))
        {
            $groups = [$groups];
        }
        if (isset($course))
        {
            if (isset($groups) && $course->is_course_admin($this->getUser()))
            {
                foreach ($groups as $group_id)
                {
                    $this->subscribe_group($group_id, $course);
                }

                if (count($groups) == 1)
                {
                    $message = 'GroupSubscribedToCourse';
                }
                else
                {
                    $message = 'GroupsSubscribedToCourse';
                }

                $this->redirectWithMessage(
                    $this->getTranslator()->trans($message, [], Manager::CONTEXT), false, [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS
                    ]
                );
            }
        }
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function subscribe_group($group_id, $course): void
    {
        $groupUserIdentifiers = $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($group_id);

        foreach ($groupUserIdentifiers as $groupUserIdentifier)
        {
            if ($groupUserIdentifier != $this->get_user_id())
            {
                $status = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_STATUS) ?
                    $this->getRequest()->query->get(
                        \Chamilo\Application\Weblcms\Manager::PARAM_STATUS
                    ) : 5;
                DataManager::subscribe_user_to_course(
                    $course->get_id(), $status, $groupUserIdentifier
                );
            }
        }

        $groups = $this->getGroupService()->findGroupsForParentIdentifier($group_id);

        foreach ($groups as $group)
        {
            $this->subscribe_group($group->getId(), $course);
        }
    }
}
