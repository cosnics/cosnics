<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

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
        if ($courseEntityRelation instanceof CourseEntityRelation)
        {
            return;
        }

        $courseEntityRelation = new CourseEntityRelation();

        $courseEntityRelation->set_course_id($course->getId());
        $courseEntityRelation->setEntityId($group->getId());
        $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_GROUP);
        $courseEntityRelation->set_status($status);

        if (!$this->courseSubscriptionRepository->createCourseEntityRelation($courseEntityRelation))
        {
            throw new \RuntimeException(
                sprintf(
                    'The course entity relation for group %s with course %s could not be created', $group->getId(),
                    $course->getId()
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $status
     */
    public function subscribeUserToCourse(User $user, Course $course, $status = CourseEntityRelation::STATUS_TEACHER)
    {
        $courseEntityRelation = $this->findCourseEntityRelationForCourseAndUser($user, $course);
        if ($courseEntityRelation instanceof CourseEntityRelation)
        {
            return;
        }

        $courseEntityRelation = new CourseEntityRelation();

        $courseEntityRelation->set_course_id($course->getId());
        $courseEntityRelation->setEntityId($user->getId());
        $courseEntityRelation->setEntityType(CourseEntityRelation::ENTITY_TYPE_USER);
        $courseEntityRelation->set_status($status);

        if (!$this->courseSubscriptionRepository->createCourseEntityRelation($courseEntityRelation))
        {
            throw new \RuntimeException(
                sprintf(
                    'The course entity relation for user %s with course %s could not be created', $user->getId(),
                    $course->getId()
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function removeGroupFromCourse(Group $group, Course $course)
    {
        $courseEntityRelation = $this->findCourseEntityRelationForCourseAndGroup($group, $course);
        if ($courseEntityRelation instanceof CourseEntityRelation)
        {
            $this->deleteCourseEntityRelation($courseEntityRelation);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function removeUserFromCourse(User $user, Course $course)
    {
        $courseEntityRelation = $this->findCourseEntityRelationForCourseAndUser($user, $course);
        if ($courseEntityRelation instanceof CourseEntityRelation)
        {
            $this->deleteCourseEntityRelation($courseEntityRelation);
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function removeEveryoneFromCourse(Course $course)
    {
        if (!$this->courseSubscriptionRepository->removeEveryoneFromCourse($course))
        {
            throw new \RuntimeException(
                sprintf('The course entities could not be removed from course %s', $course->getId())
            );
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     */
    public function deleteCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        if (!$this->courseSubscriptionRepository->deleteCourseEntityRelation($courseEntityRelation))
        {
            throw new \RuntimeException(
                sprintf('The course entity relation with id %s could not be deleted', $courseEntityRelation->getId())
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseEntityRelationForCourseAndGroup(Group $group, Course $course)
    {
        return $this->courseSubscriptionRepository->findCourseEntityRelationForCourseAndGroup($group, $course);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseEntityRelationForCourseAndUser(User $user, Course $course)
    {
        return $this->courseSubscriptionRepository->findCourseEntityRelationForCourseAndUser($user, $course);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int|null $status
     *
     * @return array
     */
    public function findGroupsDirectlySubscribedToCourse(Course $course, int $status = null)
    {
        $groups = $this->courseSubscriptionRepository->findGroupsDirectlySubscribedToCourse($course, $status)
            ->getArrayCopy();

        foreach ($groups as $index => $group)
        {
            $leftValue = $group[Group::PROPERTY_LEFT_VALUE];
            $rightValue = $group[Group::PROPERTY_RIGHT_VALUE];

            $hasChildren = $leftValue != ($rightValue - 1);
            $groups[$index]['has_children'] = $hasChildren;
        }

        return $groups;
    }

}
