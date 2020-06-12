<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Service\GroupService;

/**
 * Class PlatformGroupUsersSubscriber
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupUsersSubscriber
{
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var CourseGroupService
     */
    protected $courseGroupService;

    /**
     * PlatformGroupUsersSubscriber constructor.
     *
     * @param GroupService $groupService
     * @param CourseGroupService $courseGroupService
     */
    public function __construct(GroupService $groupService, CourseGroupService $courseGroupService)
    {
        $this->groupService = $groupService;
        $this->courseGroupService = $courseGroupService;
    }

    /**
     * @param CourseGroup $courseGroup
     * @param array $platformGroupIds
     */
    public function subscribeUsersFromPlatformGroupsInCourseGroup(CourseGroup $courseGroup, array $platformGroupIds)
    {
        $groups = $this->groupService->findGroupsByIds($platformGroupIds);

        $groupUserIds = [];
        foreach($groups as $group)
        {
            $groupUserIds = array_merge($groupUserIds, $group->get_users(true, true));
        }

        $groupUserIds = array_unique($groupUserIds);

        $this->courseGroupService->subscribeUsersWithoutMaxCapacityCheckById($courseGroup, $groupUserIds);
    }
}
