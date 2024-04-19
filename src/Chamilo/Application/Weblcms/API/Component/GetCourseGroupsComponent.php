<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Chamilo\Application\Weblcms\API\Model\APIGroup;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Hogent\Integration\Ans\Service\ArrayUtilities;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCourseGroupsComponent extends Manager
{

    function run(): JsonResponse
    {
        $chamiloCourseService = $this->getCourseService();
        $chamiloCourseGroupService = $this->getCourseGroupService();

        $course = $chamiloCourseService->getCourseById($this->get_parameter(('course_id')));
        if(!$course instanceof Course)
        {
            throw new ObjectNotExistException('Course not found');
        }

        $courseGroups = $chamiloCourseGroupService->getCourseGroupsInCourse($course->getId())->getArrayCopy();

        $apiResponse = $this->toAPICourseGroups($course, $courseGroups);

        usort($apiResponse, function(APIGroup $a, APIGroup $b) {
            return $a->getName() <=> $b->getName();
        });

        return new JsonResponse($this->getSerializer()->serialize($apiResponse, 'json'), 200, [], true);
    }

    /**
     * @param Course $course
     * @param array $courseGroups
     * @return array|APIGroup[]
     */
    protected function toAPICourseGroups(Course $course, array $courseGroups): array
    {
        $arrayUtilities = new ArrayUtilities();
        $courseGroupsById = $arrayUtilities->toAssociativeArray($courseGroups, function(CourseGroup $courseGroup) {
            return $courseGroup->getId();
        });

        $apiGroups = [];
        foreach($courseGroups as $courseGroup)
        {
            $apiGroups[] = $this->toAPICourseGroup($course, $courseGroup, $courseGroupsById);
        }
        return $apiGroups;
    }

    /**
     * @param Course $course
     * @param CourseGroup $courseGroup
     * @param array|CourseGroup[] $courseGroupsById
     * @return APIGroup
     */
    protected function toAPICourseGroup(Course $course, CourseGroup $courseGroup, array $courseGroupsById): APIGroup
    {
        $apiCourseGroup = new APIGroup();

        $apiCourseGroup->setId($courseGroup->getId());
        $apiCourseGroup->setCourseId($course->getId());
        $apiCourseGroup->setDescription($courseGroup->get_description());
        $apiCourseGroup->setName($this->determineName($courseGroup, $courseGroupsById));
        $apiCourseGroup->setMembersCount($courseGroup->count_members());

        return $apiCourseGroup;
    }

    protected function determineName(CourseGroup $courseGroup, array $courseGroupsById, $level = 0): string
    {
        $name = $courseGroup->get_name();

        if($courseGroup->get_parent_id() !== 0)
        {
            $parentGroup = $courseGroupsById[$courseGroup->get_parent_id()];
            if($parentGroup->get_parent_id() !== 0)
            {
                $name = $this->determineName($parentGroup, $courseGroupsById, $level + 1) . ' - ' . $name;
            }
        }

        if($level == 0)
        {
            $name .= ' (' . $courseGroup->getId() . ')';
        }

        return $name;
    }

    protected function getCourseGroupService(): CourseGroupService
    {
        return $this->getService(CourseGroupService::class);
    }

    protected function getCourseService(): CourseService
    {
        return $this->getService(CourseService::class);
    }
}