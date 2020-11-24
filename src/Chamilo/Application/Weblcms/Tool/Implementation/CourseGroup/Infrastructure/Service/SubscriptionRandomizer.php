<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class SubscriptionRandomizer
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * TODO: refactor the randomization to optimize the process for multiple groups at once.
 *  Retrieve all the possible users (without the already subscribed users) and divide them first between the available
 *  groups (keep the already subscribed users into account so the groups are equally divided). Subscribe them after
 *  the randomization instead of for each group
 */
class SubscriptionRandomizer
{
    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    /**
     * SubscriptionRandomizer constructor.
     *
     * @param CourseGroupDecoratorsManager $courseGroupDecoratorsManager
     */
    public function __construct(CourseGroupDecoratorsManager $courseGroupDecoratorsManager)
    {
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;
    }

    /**
     * @param array $courseGroups
     * @param CourseGroup $parentCourseGroup
     */
    public function subscribeRandomUsersInCourseGroups(array $courseGroups, CourseGroup $parentCourseGroup)
    {
        $numberOfGroupsToRandomize = count($courseGroups);

        foreach ($courseGroups as $courseGroup)
        {
            $this->subscribeRandomUsersInCourseGroup($courseGroup, $parentCourseGroup, $numberOfGroupsToRandomize);
            DataManager::clear_course_group_users_cache();

            // Each group that has been processed is a group less to randomize because the possible users array is
            // recalculated
            $numberOfGroupsToRandomize--;
        }
    }

    /**
     * @param CourseGroup $courseGroup
     * @param CourseGroup $parentCourseGroup
     * @param int $numberOfGroupsToRandomize
     */
    public function subscribeRandomUsersInCourseGroup(
        CourseGroup $courseGroup, CourseGroup $parentCourseGroup, int $numberOfGroupsToRandomize = 1
    )
    {
        $possibleUsers = $this->getPossibleUsers($courseGroup, $parentCourseGroup);
        if (empty($possibleUsers))
        {
            return;
        }

        $usersToSubscribe = $this->selectRandomUsers($possibleUsers, $courseGroup, $numberOfGroupsToRandomize);
        $courseGroup->subscribe_users($usersToSubscribe);

        foreach ($usersToSubscribe as $user)
        {
            $this->courseGroupDecoratorsManager->subscribeUser($courseGroup, $user);
        }
    }

    /**
     * @param CourseGroup $courseGroup
     * @param CourseGroup $parentCourseGroup
     *
     * @return User[]
     */
    protected function getPossibleUsers(CourseGroup $courseGroup, CourseGroup $parentCourseGroup)
    {
        $numberOfParentMembers = $parentCourseGroup->count_members();
        if ($numberOfParentMembers == 0)
        {
            $possibleUsers = $this->getAllCourseUsers($courseGroup->get_course_code());
        }
        else
        {
            $possibleUsers = $this->getMembersFromParentCourseGroup($parentCourseGroup);
        }

        $possibleUsers = $this->filterPossibleUsersByCourseGroupAndSiblings($parentCourseGroup, $possibleUsers);

        return $possibleUsers;
    }

    /**
     * @param CourseGroup $parentCourseGroup
     *
     * @return User[]
     */
    protected function getMembersFromParentCourseGroup(CourseGroup $parentCourseGroup)
    {
        $parentCourseGroupMemberIds = $parentCourseGroup->get_members();
        $parentCourseGroupMembers = array();

        foreach ($parentCourseGroupMemberIds as $id)
        {
            $userData = [User::PROPERTY_ID => $id];
            $parentCourseGroupMembers[$id] = new User($userData);
        }

        return $parentCourseGroupMembers;
    }

    /**
     * @param int $courseId
     *
     * @return User[]
     */
    protected function getAllCourseUsers(int $courseId)
    {
        /** @var \Chamilo\Libraries\Storage\ResultSet\ResultSet $courseUsersResultSet */
        $courseUsersResultSet = CourseDataManager::retrieve_all_course_users($courseId);

        $courseUsers = array();
        if ($courseUsersResultSet)
        {
            while ($courseUserData = $courseUsersResultSet->next_result())
            {
                $courseUsers[$courseUserData[User::PROPERTY_ID]] = new User($courseUserData);
            }
        }

        return $courseUsers;
    }

    /**
     * @param CourseGroup $parentCourseGroup
     * @param User[] $possibleUsers
     *
     * @return User[]
     */
    protected function filterPossibleUsersByCourseGroupAndSiblings(CourseGroup $parentCourseGroup, array $possibleUsers
    ): array
    {
        /** @var CourseGroup[] $childGroups */
        $childGroups = $parentCourseGroup->get_children(false)->as_array();

        $max_number_subscriptions = $parentCourseGroup->get_max_number_of_course_group_per_member();
        $user_number_subscriptions = array();

        foreach ($childGroups as $courseGroup)
        {
            /** @var int[] $subscribed_users */
            $subscribed_users = $courseGroup->get_members(true, true);
            if ($subscribed_users)
            {
                foreach ($subscribed_users as $user_id)
                {
                    $user_number_subscriptions[$user_id] = $user_number_subscriptions[$user_id] + 1;
                }
            }
        }

        foreach ($user_number_subscriptions as $user_id => $number_subscriptions)
        {
            if ($number_subscriptions >= $max_number_subscriptions)
            {
                unset($possibleUsers[$user_id]);
            }
        }

        return $possibleUsers;
    }

    /**
     * @param array $possibleUsers
     * @param CourseGroup $courseGroup
     * @param int $numberOfGroupsToRandomize
     *
     * @return User[]
     */
    protected function selectRandomUsers(
        array $possibleUsers, CourseGroup $courseGroup, int $numberOfGroupsToRandomize = 1
    ): array
    {
        $totalNumberOfPossibleUsers = count($possibleUsers);
        $numberOfPossibleUsersPerGroup = $totalNumberOfPossibleUsers / $numberOfGroupsToRandomize;

        // Determine the maximum users for this group based on the minimum value between the setting of the group
        // and the maximum users that can be subscribed between the available groups that will be randomized.
        // This method makes sure that the groups are equally randomized rather than filled until their max
        $numberOfMaximumMembersSetting = $courseGroup->get_max_number_of_members();
        $numberOfMaximumMembers = $numberOfMaximumMembersSetting == 0 ? $numberOfPossibleUsersPerGroup :
            min($numberOfMaximumMembersSetting, $numberOfPossibleUsersPerGroup);

        // shuffle removes the indexes of the array and replaces them with simple numeric indexes so user id is no longer the key in the array
        shuffle($possibleUsers);

        $numberOfCurrentlySubscribedUsers = $courseGroup->count_members();
        $numberOfUsersToSelect = $numberOfMaximumMembers - $numberOfCurrentlySubscribedUsers;

        $randomUserIndexes = array_rand($possibleUsers, $numberOfUsersToSelect);
        $randomUsers = [];

        foreach ($randomUserIndexes as $randomUserIndex)
        {
            $randomUsers[] = $possibleUsers[$randomUserIndex];
        }

        return $randomUsers;
    }
}
