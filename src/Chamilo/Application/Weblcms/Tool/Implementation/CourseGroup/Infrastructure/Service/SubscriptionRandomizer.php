<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class SubscriptionRandomizer
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
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
     * @param CourseGroup $courseGroup
     * @param CourseGroup $parentCourseGroup
     */
    public function subscribeRandomUsersInCourseGroup(CourseGroup $courseGroup, CourseGroup $parentCourseGroup)
    {
        $possibleUsers = $this->getPossibleUsers($courseGroup, $parentCourseGroup);
        if(empty($possibleUsers))
        {
            return;
        }

        $usersToSubscribe = $this->selectRandomUsers($possibleUsers, $courseGroup);
        $courseGroup->subscribe_users($usersToSubscribe);

        foreach($usersToSubscribe as $user)
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
        if($numberOfParentMembers == 0)
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

        foreach($parentCourseGroupMemberIds as $id)
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
    protected function filterPossibleUsersByCourseGroupAndSiblings(CourseGroup $parentCourseGroup, array $possibleUsers): array
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
     *
     * @return User[]
     */
    protected function selectRandomUsers(array $possibleUsers, CourseGroup $courseGroup): array
    {
        $totalNumberOfPossibleUsers = count($possibleUsers);

        // shuffle removes the indexes of the array and replaces them with simple numeric indexes so user id is no longer the key in the array
        shuffle($possibleUsers);

        $numberOfCurrentlySubscribedUsers = $courseGroup->count_members();
        $numberOfMaximumMembers = $courseGroup->get_max_number_of_members();
        $numberOfUsersToSelect = $numberOfMaximumMembers - $numberOfCurrentlySubscribedUsers;

        // Clamp this value to the maximum number of possible users due to issues with array_rand when asking for more items than there are available in the array
        $clampedNumberOfUsersToSelect = min($numberOfUsersToSelect, $totalNumberOfPossibleUsers);

        $randomUserIndexes = array_rand($possibleUsers, $clampedNumberOfUsersToSelect);
        $randomUsers = [];

        foreach($randomUserIndexes as $randomUserIndex)
        {
            $randomUsers[] = $possibleUsers[$randomUserIndex];
        }

        return $randomUsers;
    }
}
