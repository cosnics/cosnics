<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365Connector
{
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var TeamService
     */
    protected $teamService;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected $userService;

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
     * @param GroupService $groupService
     * @param TeamService $teamService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     * @param \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface $courseService
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        GroupService $groupService, TeamService $teamService, UserService $userService,
        CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService,
        CourseServiceInterface $courseService, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->groupService = $groupService;
        $this->teamService = $teamService;
        $this->userService = $userService;
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
        $this->courseService = $courseService;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Creates an office365 group for a given CourseGroup
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
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
        $groupId = $this->groupService->createGroupByName($user, $courseGroupName);

        $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $groupId);

        $this->subscribeCourseGroupUsers($courseGroup, $groupId);

        return $groupId;
    }

    /**
     * Creates an office365 group and a Team for a given CourseGroup
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createGroupAndTeamFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $office365GroupId = $this->createGroupFromCourseGroup($courseGroup, $user);
        $this->teamService->addTeamToGroup($office365GroupId);
        $this->courseGroupOffice365ReferenceService->addTeamToCourseGroupReference($courseGroup);
    }

    /**
     * Creates or updates the office365 group for a given CourseGroup. When the CourseGroup was once created and
     * then unlinked, the system will restore the link and reinstate the subscriptions
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createOrUpdateGroupFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            return $this->createGroupFromCourseGroup($courseGroup, $user);

        }

        if ($reference->isLinked())
        {
            $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);
            $this->groupService->updateGroupName($reference->getOffice365GroupId(), $courseGroupName); //todo: check if name in group differs from course group. If so the user changed it, and we don't need to sync...

            return $reference->getOffice365GroupId();
        }

        //if an office365 group was previously linked, we need to re-attach it and subscribe the course group users.
        $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup); //todo: check if name in group differs from course group. If so the user changed it, and we don't need to sync...
        $this->groupService->updateGroupName($reference->getOffice365GroupId(), $courseGroupName);
        $this->courseGroupOffice365ReferenceService->linkCourseGroupReference($reference);

        if(!$this->groupService->isOwnerOfGroup($reference->getOffice365GroupId(), $user))
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }

        $this->subscribeCourseGroupUsers($courseGroup, $reference->getOffice365GroupId());

        return $reference->getOffice365GroupId();

    }

    /**
     * Creates or updates the office365 group for a given CourseGroup. When the CourseGroup was once created and
     * then unlinked, the system will restore the link and reinstate the subscriptions
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createOrUpdateTeamFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if(!$reference->isLinked()) { //there is no course group
            $office365GroupId = $this->createOrUpdateGroupFromCourseGroup($courseGroup, $user);
        } else {
            $office365GroupId = $reference->getOffice365GroupId();
        }

        if(!$reference->hasTeam()) {
            if($this->teamService->getTeam($office365GroupId)) { //team already exists, just link it
                $this->courseGroupOffice365ReferenceService->linkTeam($reference);
            } else {
                $this->teamService->addTeamToGroup($office365GroupId);
            }
        }
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
        $this->groupService->removeAllMembersFromGroup($reference->getOffice365GroupId());

        try
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
        {

        }

        $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     */
    public function unlinkTeamFromOffice365Group(CourseGroup $courseGroup)
    {
        $office365Reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
        if(!$office365Reference) {
            return;
        }

        if(!$office365Reference->hasTeam()) {
            return;
        }

        $this->courseGroupOffice365ReferenceService->unlinkTeamFromCourseGroupReference($office365Reference);
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
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
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
            $this->groupService->removeMemberFromGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
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
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if(empty($reference)) {
            return;
        }

        if(!$reference->isLinked()) {
            return;
        }

        $courseGroupUsers = $courseGroup->get_members(true, true, true);

        $this->groupService->syncUsersToGroup($reference->getOffice365GroupId(), $courseGroupUsers);
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function getPlannerUrlForVisit(CourseGroup $courseGroup, User $user)
    {
        $office365ReferenceService = $this->courseGroupOffice365ReferenceService;
        if (!$office365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            throw new \RuntimeException();
        }

        $reference = $office365ReferenceService->getCourseGroupReference($courseGroup);

        if(!$this->groupService->isOwnerOfGroup($reference->getOffice365GroupId(), $user))
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }

        $baseUrl = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'planner_base_uri']
        );

        $planId = $reference->getOffice365PlanId();
        if (empty($planId))
        {
            $planId = $this->groupService->getOrCreatePlanIdForGroup($reference->getOffice365GroupId());

            $office365ReferenceService->storePlannerReferenceForCourseGroup(
                $courseGroup, $reference->getOffice365GroupId(), $planId
            );
        }

        $plannerUrl = $baseUrl . '/#/plantaskboard?groupId=%s&planId=%s';
        $plannerUrl = sprintf($plannerUrl, $reference->getOffice365GroupId(), $planId);

        return $plannerUrl;
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     * @return string
     * @throws AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     */
    public function getTeamUrlForVisit(CourseGroup $courseGroup, User $user)
    {
        $office365ReferenceService = $this->courseGroupOffice365ReferenceService;
        if (!$office365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            throw new \RuntimeException();
        }

        $reference = $office365ReferenceService->getCourseGroupReference($courseGroup);

        if(!$this->groupService->isOwnerOfGroup($reference->getOffice365GroupId(), $user))
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }

        $group = $this->groupService->getGroup($reference->getOffice365GroupId());

        return $this->teamService->getTeamUrl($group);
    }

    /**
     * Returns the link for the group space and makes sure that the user has access to it
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function getGroupUrlForVisit(CourseGroup $courseGroup, User $user)
    {
        $office365ReferenceService = $this->courseGroupOffice365ReferenceService;
        if (!$office365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            throw new \RuntimeException("Course group is not linked to office365 group");
        }

        $reference = $office365ReferenceService->getCourseGroupReference($courseGroup);

        if(!$this->groupService->isOwnerOfGroup($reference->getOffice365GroupId(), $user))
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }

        $groupUrl = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri']
        );

        try {
            $group = $this->groupService->getGroup($reference->getOffice365GroupId());
        } catch (GroupNotExistsException $exception) {
            throw new \RuntimeException('Office365 group not found');
        }


        return str_replace('{GROUP_ID}', $group->getMailNickname(), $groupUrl);

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
                $this->groupService->addMemberToGroup($office365GroupId, $groupUser);
            }
            catch (AzureUserNotExistsException $ex)
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
                $this->groupService->addMemberToGroup($office365GroupId, $user);
            }
            catch (AzureUserNotExistsException $ex)
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
            $courseGroupName = $courseGroupName . ' - ' . $course->get_title() . ' (' . $course->get_visual_code() . ')';
        }

        return $courseGroupName;
    }
}
