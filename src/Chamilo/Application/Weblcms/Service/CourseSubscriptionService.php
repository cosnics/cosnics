<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 * @package Chamilo\Application\Weblcms\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSubscriptionService
{
    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\CourseSubscriptionRepository
     */
    protected $courseSubscriptionRepository;

    /**
     * CourseSubscriptionService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Storage\Repository\CourseSubscriptionRepository $courseSubscriptionRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Storage\Repository\CourseSubscriptionRepository $courseSubscriptionRepository
    )
    {
        $this->courseSubscriptionRepository = $courseSubscriptionRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $status
     */
    public function subscribeGroupToCourse(Group $group, Course $course, $status = CourseEntityRelation::STATUS_TEACHER)
    {
        $courseEntityRelation = $this->findCourseEntityRelationForCourseAndGroup($group, $course);
        if($courseEntityRelation instanceof CourseEntityRelation)
        {
            echo $courseEntityRelation->getId();
            return;
        }

        $courseEntityRelation = new CourseEntityRelation();

        $courseEntityRelation->set_course_id($course->getId());
        $courseEntityRelation->setEntityId($group->getId());
        $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_GROUP);
        $courseEntityRelation->set_status($status);

        if(!$this->courseSubscriptionRepository->createCourseEntityRelation($courseEntityRelation))
        {
            throw new \RuntimeException('The course entity relation could not be created');
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function removeGroupFromCourse(Group $group, Course $course)
    {
        $courseEntityRelation = $this->findCourseEntityRelationForCourseAndGroup($group, $course);
        if($courseEntityRelation instanceof CourseEntityRelation)
        {
            $this->deleteCourseEntityRelation($courseEntityRelation);
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     */
    public function deleteCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        if(!$this->courseSubscriptionRepository->deleteCourseEntityRelation($courseEntityRelation))
        {
            throw new \RuntimeException('The course entity relation could not be created');
        }
    }

    public function findCourseEntityRelationForCourseAndGroup(Group $group, Course $course)
    {
        return $this->courseSubscriptionRepository->findCourseEntityRelationForCourseAndGroup($group, $course);
    }

}