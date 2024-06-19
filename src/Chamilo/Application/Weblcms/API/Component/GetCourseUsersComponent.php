<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Chamilo\Application\Weblcms\API\Model\APICourse;
use Chamilo\Application\Weblcms\API\Model\APIGroup;
use Chamilo\Application\Weblcms\API\Model\APIUser;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCourseUsersComponent extends Manager
{
    #[OA\Get(
        path: '/v1/courses/{course_id}/users',
        operationId: 'getCourseUsers',
        description: 'Retrieves the users of course by an id',
        summary: 'Retrieves the users of course by an id',
        security: [['oauth' => []]], tags: ['Courses'])
    ]
    #[OA\Parameter(
        name: 'course_id',
        description: 'ID',
        in: 'path',
        required: true,
    )]
    #[OA\Response(
        response: 200,
        description: 'The users of the course',
        content: new OA\JsonContent(
            ref: APIUser::class
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

        $teachersInCourse = [];
        $studentsInCourse = [];

        $enrollmentType = $this->getRequest()->getFromUrl('enrollment_type');
        if(empty($enrollmentType) || in_array('teacher', $enrollmentType))
        {
            $teachersInCourse = $this->getCourseService()->getTeachersFromCourse($course);
        }

        if(empty($enrollmentType) || in_array('student', $enrollmentType))
        {
            $studentsInCourse = $this->getCourseService()->getStudentsFromCourse($course);
        }

        $apiCourse = $this->toAPIUsers($course, $teachersInCourse, $studentsInCourse);
        return new JsonResponse($this->getSerializer()->serialize($apiCourse, 'json'), 200, [], true);
    }

    /**
     * @param Course $weblcmsCourse
     * @param array|User[] $teachersInCourse
     * @param array|User[] $studentsInCourse
     *
     * @return array|APICourse[]
     */
    protected function toAPIUsers(Course $weblcmsCourse, array $teachersInCourse, array $studentsInCourse): array
    {
        $users = [];

        foreach($teachersInCourse as $teacher)
        {
            $users[] = $this->toAPIUser($teacher, 'teacher');
        }

        foreach($studentsInCourse as $student)
        {
            $users[] = $this->toAPIUser($student, 'student');
        }

        return $users;
    }

    protected function toAPIUser(User $user, string $enrollmentType): APIUser
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
        $apiUser->setRole($enrollmentType);

        return $apiUser;
    }

    protected function getCourseService(): CourseService
    {
        return $this->getService(CourseService::class);
    }
}