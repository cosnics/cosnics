<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Chamilo\Application\Weblcms\API\Model\APIGroup;
use Chamilo\Application\Weblcms\API\Model\APIUser;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Hogent\Integration\Ans\Service\ArrayUtilities;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCourseGroupUsersComponent extends Manager
{

    function run(): JsonResponse
    {
        $chamiloCourseGroupService = $this->getCourseGroupService();

        $courseGroup = $chamiloCourseGroupService->getCourseGroupById($this->get_parameter('group_id'));
        if(!$courseGroup instanceof CourseGroup)
        {
            throw new ObjectNotExistException('Course group not found');
        }

        $apiResponse = $this->toAPIUsers($courseGroup);

        return new JsonResponse($this->getSerializer()->serialize($apiResponse, 'json'), 200, [], true);
    }

    protected function toAPIUsers(CourseGroup $courseGroup): array
    {
        $users = [];

        $members = $this->getCourseGroupService()->getMembersDirectlySubscribedInGroup($courseGroup);
        foreach($members as $member)
        {
            $users[] = $this->toAPIUser($member);
        }


        return $users;
    }

    protected function toAPIUser(User $user): APIUser
    {
        $apiUser = new APIUser();

        $apiUser->setId($user->getId());
        $apiUser->setLoginId($user->get_username());
        $apiUser->setFirstName($user->get_firstname());
        $apiUser->setLastName($user->get_lastname());
        $apiUser->setName($user->get_firstname() . ' ' . $user->get_lastname());
        $apiUser->setSortableName(str_replace(' ', '', ($user->get_lastname() . ',' . $user->get_firstname())));
        $apiUser->setEmail($user->get_email());
        $apiUser->setLocale('nl'); //todo: get locale from user settings
        $apiUser->setTimeZone(date_default_timezone_get());

        return $apiUser;
    }


    protected function getCourseGroupService(): CourseGroupService
    {
        return $this->getService(CourseGroupService::class);
    }
}