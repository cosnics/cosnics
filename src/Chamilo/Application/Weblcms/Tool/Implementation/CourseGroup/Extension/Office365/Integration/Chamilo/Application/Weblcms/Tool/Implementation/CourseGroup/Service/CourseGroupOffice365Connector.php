<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365Connector
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service
     */
    protected $office365Service;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository
     */
    protected $office365Repository;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface
     */
    protected $courseService;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service $office365Service
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository $office365Repository
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     * @param \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface $courseService
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        Office365Service $office365Service, Office365Repository $office365Repository,
        CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService,
        CourseServiceInterface $courseService, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->office365Service = $office365Service;
        $this->office365Repository = $office365Repository;
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
        $this->courseService = $courseService;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Creates an office365 group for a given CourseGroup
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function createGroupFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        if ($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new office365 group for the given course group %s' .
                    'since there is a group already available'
                ), $courseGroup->getId()
            );
        }

        $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);
        $groupId = $this->office365Service->createGroupByName($user, $courseGroupName);

        $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $groupId);

        $this->subscribeTeachers($courseGroup, $groupId);
        $this->subscribeCourseGroupUsers($courseGroup, $groupId);
    }

    /**
     * Creates or updates the office365 group for a given CourseGroup. When the CourseGroup was once created and
     * then unlinked, the system will restore the link and reinstate the subscriptions
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function createOrUpdateGroupFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            $this->createGroupFromCourseGroup($courseGroup, $user);

            return;
        }

        if ($reference->isLinked())
        {
            $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);
            $this->office365Service->updateGroupName($reference->getOffice365GroupId(), $courseGroupName);

            return;
        }

        $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);
        $this->office365Service->updateGroupName($reference->getOffice365GroupId(), $courseGroupName);
        $this->courseGroupOffice365ReferenceService->linkCourseGroupReference($reference);
        $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
        $this->subscribeCourseGroupUsers($courseGroup, $reference->getOffice365GroupId());
        $this->subscribeTeachers($courseGroup, $reference->getOffice365GroupId());

        return;
    }

    /**
     * Unlinks the given course group with the referenced office365 group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unlinkOffice365GroupFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        if (!$this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            return;
        }

        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
        $this->office365Service->removeAllMembersFromGroup($reference->getOffice365GroupId());

        try
        {
            $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (Office365UserNotExistsException $ex)
        {

        }

        $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
    }

    /**
     * Subscribes a user from a given course group to the Office365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        if (!$this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            return;
        }
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        try
        {
            $this->office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (Office365UserNotExistsException $ex)
        {

        }
    }

    /**
     * Unubscribes a user from a given course group from the Office365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        if (!$this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            return;
        }

        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        try
        {
            $this->office365Service->removeMemberFromGroup($reference->getOffice365GroupId(), $user);
        }
        catch (Office365UserNotExistsException $ex)
        {

        }
    }

    /**
     * Syncs the CourseGroup and course teacher subscriptions to the O365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function syncCourseGroupSubscriptions(CourseGroup $courseGroup)
    {
        if (!$this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            return;
        }

        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        $currentCourseGroupMemberIdentifiers = [];

        $courseGroupUsers = $courseGroup->get_members(false, false, true);
        foreach ($courseGroupUsers as $courseGroupUser)
        {
            $office365UserIdentifier = $this->office365Service->getOffice365UserIdentifier($courseGroupUser);
            if (!empty($office365UserIdentifier))
            {
                $currentCourseGroupMemberIdentifiers[] = $office365UserIdentifier;
            }
        }

        $course = new Course();
        $course->setId($courseGroup->get_course_code());

        $teachers = $this->courseService->getTeachersFromCourse($course);
        foreach ($teachers as $user)
        {
            $office365UserIdentifier = $this->office365Service->getOffice365UserIdentifier($user);
            if (!empty($office365UserIdentifier))
            {
                $currentCourseGroupMemberIdentifiers[] = $office365UserIdentifier;
            }
        }

        $office365GroupMemberIdentifiers = $this->office365Service->getGroupMembers($reference->getOffice365GroupId());

        $usersToAdd = array_diff($currentCourseGroupMemberIdentifiers, $office365GroupMemberIdentifiers);
        foreach ($usersToAdd as $userToAdd)
        {
            $this->office365Repository->subscribeMemberInGroup($reference->getOffice365GroupId(), $userToAdd);
        }

        $usersToRemove = array_diff($office365GroupMemberIdentifiers, $currentCourseGroupMemberIdentifiers);
        foreach ($usersToRemove as $userToRemove)
        {
            $this->office365Repository->removeMemberFromGroup($reference->getOffice365GroupId(), $userToRemove);
        }
    }

    /**
     * Returns the link for planner and makes sure that the user has access to it
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getPlannerUrlForVisit(CourseGroup $courseGroup, User $user)
    {
        $office365ReferenceService = $this->courseGroupOffice365ReferenceService;
        if (!$office365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            throw new \RuntimeException();
        }

        $reference = $office365ReferenceService->getCourseGroupReference($courseGroup);

        $office365Service = $this->office365Service;
        $office365Service->addMemberToGroup($reference->getOffice365GroupId(), $user);

        $baseUrl = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'planner_base_uri']
        );

        $planId = $reference->getOffice365PlanId();
        if (empty($planId))
        {
            $planId = $office365Service->getOrCreatePlanIdForGroup($reference->getOffice365GroupId());

            $office365ReferenceService->storePlannerReferenceForCourseGroup(
                $courseGroup, $reference->getOffice365GroupId(), $planId
            );
        }

        $plannerUrl = $baseUrl . '/#/plantaskboard?groupId=%s&planId=%s';
        $plannerUrl = sprintf($plannerUrl, $reference->getOffice365GroupId(), $planId);

        return $plannerUrl;
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
        foreach ($groupUsers as $groupUser)
        {
            try
            {
                $this->office365Service->addMemberToGroup($office365GroupId, $groupUser);
            }
            catch (Office365UserNotExistsException $ex)
            {

            }
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
        foreach ($teachers as $user)
        {
            try
            {
                $this->office365Service->addMemberToGroup($office365GroupId, $user);
            }
            catch (Office365UserNotExistsException $ex)
            {

            }
        }
    }

    /**
     * Creates an office365 group name for a given course group, adding the course title and visual code to the
     * name
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return string
     */
    protected function getOffice365GroupNameForCourseGroup(CourseGroup $courseGroup)
    {
        $courseGroupName = $courseGroup->get_name();
        $course = $this->courseService->getCourseById($courseGroup->get_course_code());
        if ($course instanceof Course)
        {
            $courseGroupName =
                $course->get_title() . ' - ' . $courseGroupName . ' (' . $course->get_visual_code() . ')';
        }

        return $courseGroupName;
    }
}