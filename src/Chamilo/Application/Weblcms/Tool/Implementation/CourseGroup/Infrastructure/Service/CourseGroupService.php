<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use InvalidArgumentException;

/**
 * Course group service to help with the management of course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupService implements CourseGroupServiceInterface
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository
     */
    protected $courseGroupRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    /**
     * CourseGroupService constructor.
     *
     * @param CourseGroupRepository $courseGroupRepository
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager $courseGroupDecoratorsManager
     */
    public function __construct(
        CourseGroupRepository $courseGroupRepository, CourseGroupDecoratorsManager $courseGroupDecoratorsManager
    )
    {
        $this->courseGroupRepository = $courseGroupRepository;
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;
    }

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId)
    {
        return $this->courseGroupRepository->countCourseGroupsInCourse($courseId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $userIds
     */
    public function removeUsersFromAllCourseGroupsByIds(Course $course, array $userIds = [])
    {
        $courseGroupUserRelations =
            $this->courseGroupRepository->findCourseGroupUserRelationsForCourseAndUserIds($course, $userIds);
        foreach ($courseGroupUserRelations as $courseGroupUserRelation)
        {
            $this->removeCourseGroupUserRelation($courseGroupUserRelation);
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUserFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $courseGroupUserRelation =
            $this->courseGroupRepository->findCourseGroupUserRelationByCourseGroupAndUser($courseGroup, $user);

        if (!$courseGroupUserRelation instanceof CourseGroupUserRelation)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'There is no subscription from the given user %s in the given course group %s', $user->getId(),
                    $courseGroup->getId()
                )
            );
        }

        $this->courseGroupDecoratorsManager->unsubscribeUser($courseGroup, $user);
        $this->courseGroupRepository->delete($courseGroupUserRelation);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation $courseGroupUserRelation
     */
    public function removeCourseGroupUserRelation(CourseGroupUserRelation $courseGroupUserRelation)
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId($courseGroupUserRelation->get_course_group());

        $user = new User();
        $user->setId($courseGroupUserRelation->get_user());

        $this->courseGroupDecoratorsManager->unsubscribeUser($courseGroup, $user);
        $this->courseGroupRepository->delete($courseGroupUserRelation);
    }
}