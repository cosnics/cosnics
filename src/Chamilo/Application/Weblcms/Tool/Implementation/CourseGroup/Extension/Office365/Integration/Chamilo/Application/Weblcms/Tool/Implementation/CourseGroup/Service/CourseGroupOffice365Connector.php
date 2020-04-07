<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\TeamNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Microsoft\Graph\Model\Team;

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
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface
     */
    protected $courseService;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param GroupService $groupService
     * @param TeamService $teamService
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     * @param \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface $courseService
     */
    public function __construct(
        GroupService $groupService, TeamService $teamService,
        CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService,
        CourseServiceInterface $courseService
    )
    {
        $this->groupService = $groupService;
        $this->teamService = $teamService;
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
        $this->courseService = $courseService;
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $owner
     *
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function createClassTeamFromCourseGroup(CourseGroup $courseGroup, User $owner)
    {
        return $this->createTeamTemplateMethod(
            $courseGroup, $owner,
            function (string $courseGroupName, User $owner) {
                return $this->teamService->createClassTeam($courseGroupName, $courseGroupName, $owner);
            }
        );
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $owner
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function createStandardTeamFromCourseGroup(CourseGroup $courseGroup, User $owner)
    {
        return $this->createTeamTemplateMethod(
            $courseGroup, $owner,
            function (string $courseGroupName, User $owner) {
                return $this->teamService->createStandardTeam($courseGroupName, $courseGroupName, $owner);
            }
        );
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $owner
     * @param \Closure $createTeamInTeamServiceFunction
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    protected function createTeamTemplateMethod(
        CourseGroup $courseGroup, User $owner, \Closure $createTeamInTeamServiceFunction
    )
    {
        if ($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new team for the given course group %s ' .
                    'because another team is still active'
                    , $courseGroup->getId()
                )
            );
        }

        $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);
        $teamId = $createTeamInTeamServiceFunction->call($this, $courseGroupName, $owner);

        $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $teamId);
        $this->subscribeCourseGroupUsers($courseGroup, $teamId);

        return $teamId;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return string
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     *
     * todo: check if name in group differs from course group. If so the user changed it, and we don't need to sync...
     *  to fix this you may want to alter the way the course groups are updated since this method already receives
     *  the changed course group name so comparison is not possible
     */
    public function updateTeamNameFromCourseGroup(CourseGroup $courseGroup)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            throw new \RuntimeException('No team found for course group ' . $courseGroup->getId());
        }

        $courseGroupName = $this->getOffice365GroupNameForCourseGroup($courseGroup);

        $this->groupService->updateGroupName(
            $reference->getOffice365GroupId(), $courseGroupName
        );

        return $reference->getOffice365GroupId();
    }

    /**
     * Unlinks the reference from the course group with the team. The reference is kept in stock for archiving
     * reasons so we can determine which team belongs to which group.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeTeamFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            return;
        }

//        try
//        {
//            $group = $this->groupService->getGroup($reference->getOffice365GroupId());
//            if ($group instanceof Group)
//            {
//                $this->groupService->removeAllMembersFromGroup($reference->getOffice365GroupId());
//
//                try
//                {
//                    $this->groupService->addOwnerToGroup($reference->getOffice365GroupId(), $user);
//                }
//                catch (AzureUserNotExistsException $ex)
//                {
//
//                }
//            }
//        }
//        catch (GroupNotExistsException $groupNotExistsException)
//        {
//
//        }

        $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
    }

    /**
     * Subscribes a user from a given course group to the Office365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            return;
        }

        try
        {
            $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
        {

        }
        catch (GroupNotExistsException $ex)
        {

        }
    }

    /**
     * Unubscribes a user from a given course group from the Office365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            return;
        }

        try
        {
            $this->groupService->removeMemberFromGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
        {

        }
        catch (GroupNotExistsException $ex)
        {

        }
    }

    /**
     * Syncs the CourseGroup and course teacher subscriptions to the O365 connected group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws TeamNotFoundException
     */
    public function syncCourseGroupSubscriptions(CourseGroup $courseGroup)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            return;
        }

        $team = $this->teamService->getTeam($reference->getOffice365GroupId());
        if(!$team instanceof Team)
        {
            throw new TeamNotFoundException($reference->getOffice365GroupId());
        }

        $courseGroupUsers = $courseGroup->get_members(true, true, true);

        $this->groupService->syncUsersToGroup($reference->getOffice365GroupId(), $courseGroupUsers);
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     *
     * @return string
     * @throws AzureUserNotExistsException
     * @throws GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\TeamNotFoundException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function getTeamUrlForVisit(CourseGroup $courseGroup, User $user)
    {
        $reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if (!$reference instanceof CourseGroupOffice365Reference)
        {
            throw new \RuntimeException('No team found for course group ' . $courseGroup->getId());
        }

        $teamUrl = $this->teamService->getTeamUrl($reference->getOffice365GroupId());

        $this->groupService->addMemberToGroup($reference->getOffice365GroupId(), $user);

        return $teamUrl;
    }

    /**
     * Subscribes the course group users in the office365 group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $office365GroupId
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
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
            catch (GroupNotExistsException $ex)
            {

            }
        }
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return bool
     */
    public function courseGroupHasTeam(CourseGroup $courseGroup)
    {
        return $this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup);
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
                $courseGroupName . ' - ' . $course->get_title() . ' (' . $course->get_visual_code() . ')';
        }

        return $courseGroupName;
    }
}
