<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\CourseGroupOffice365ReferenceService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service
     */
    protected $office365Service;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\CourseGroupOffice365ReferenceService
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface
     */
    protected $courseService;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service $office365Service
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     * @param \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface $courseService
     */
    public function __construct(
        Office365Service $office365Service, CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService,
        CourseServiceInterface $courseService
    )
    {
        $this->office365Service = $office365Service;
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
        $this->courseService = $courseService;
    }

    /**
     * Decorates the create functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function createGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        if($this->usesPlanner($formValues))
        {
            $groupId = $this->office365Service->createGroupByName($user, $courseGroup->get_name());
            $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $groupId);
            $this->subscribeTeachers($courseGroup, $groupId);
            // connect plan id
        }
    }

    /**
     * Decorates the update functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function updateGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
        $hasReference = $this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup);

        if($this->usesPlanner($formValues))
        {
            if($hasReference)
            {
                $this->office365Service->updateGroupName($reference->getOffice365GroupId(), $courseGroup->get_name());
            }
            elseif($reference instanceof CourseGroupOffice365Reference && !$reference->isLinked())
            {
                $this->courseGroupOffice365ReferenceService->linkCourseGroupReference($reference);
                $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
                $this->subscribeCourseGroupUsers($courseGroup, $reference->getOffice365GroupId());
                $this->subscribeTeachers($courseGroup, $reference->getOffice365GroupId());
                //connect plan id
            }
            else
            {
                $groupId = $this->office365Service->createGroupByName($user, $courseGroup->get_name());
                $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $groupId);
                $this->subscribeCourseGroupUsers($courseGroup, $reference->getOffice365GroupId());
                $this->subscribeTeachers($courseGroup, $reference->getOffice365GroupId());
            }
        }
        else
        {
            $this->deleteGroup($courseGroup, $user);
        }
    }

    /**
     * Subscribes the course group users in the office365 group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $office365GroupId
     *
     */
    protected function subscribeCourseGroupUsers(CourseGroup $courseGroup, $office365GroupId)
    {
        $groupUsers = $courseGroup->get_members(false, false, true);
        foreach($groupUsers as $groupUser)
        {
            $this->office365Service->addMemberToGroup($office365GroupId, $groupUser);
        }
    }

    /**
     * Subscribes all the teachers that are currently subscribed to the course where the course group belongs to
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $office365GroupId
     */
    protected function subscribeTeachers(CourseGroup $courseGroup, $office365GroupId)
    {
        $course = new Course();
        $course->setId($courseGroup->get_course_code());

        $teachers = $this->courseService->getTeachersFromCourse($course);
        foreach($teachers as $user)
        {
            $this->office365Service->addMemberToGroup($office365GroupId, $user);
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function deleteGroup(CourseGroup $courseGroup, User $user)
    {
        if($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup))
        {
            $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
            $this->office365Service->removeAllUsersFromGroup($reference->getOffice365GroupId());
            $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
            $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
        }
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        if($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup))
        {
            $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
            $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        if($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup))
        {
            $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
            $this->office365Service->removeMemberFromGroup($reference->getOffice365GroupId(), $user);
        }
    }

    /**
     * @param array $formValues
     *
     * @return bool
     */
    protected function usesPlanner($formValues = [])
    {
        return boolval($formValues[CourseGroupFormDecorator::PROPERTY_USE_PLANNER]);
    }
}