<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Chamilo\Application\Weblcms\API\Model\APICourse;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCourseComponent extends Manager
{
    #[OA\Get(path: '/v1/courses/{course_id}', operationId: 'getCourse', description: 'Retrieves a course by an id', summary: 'Retrieves a course by an id', security: [['oauth' => []]], tags: ['Courses'])]
    #[OA\Parameter(
        name: 'course_id',
        description: 'ID',
        in: 'path',
        required: true,
    )]
    #[OA\Response(
        response: 200,
        description: 'The course',
        content: new OA\JsonContent(
            ref: APICourse::class
        )
    )]
    function run(): JsonResponse
    {
        $chamiloCourseService = $this->getCourseService();
        $course = $chamiloCourseService->getCourseById($this->get_parameter(('course_id')));
        if(!$course instanceof Course)
        {
            throw new ObjectNotExistException('Course not found');
        }

        $apiCourse = $this->toAPICourse($course);
        return new JsonResponse($this->getSerializer()->serialize($apiCourse, 'json'), 200, [], true);
    }

    protected function toAPICourse(Course $weblcmsCourse): APICourse
    {
        $apiCourse = new APICourse();
        $apiCourse->setId($weblcmsCourse->getId());
        $apiCourse->setCourseCode($weblcmsCourse->get_visual_code());
        $apiCourse->setName($weblcmsCourse->get_title());
        $apiCourse->setAccountId($weblcmsCourse->get_titular_id());
        $apiCourse->setRootAccountId($weblcmsCourse->get_titular_id());

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($weblcmsCourse->get_creation_date());
        $apiCourse->setCreatedAt($dateTime->format(\DateTimeInterface::ATOM));

        return $apiCourse;
    }

    protected function getCourseService(): CourseService
    {
        return $this->getService(CourseService::class);
    }
}