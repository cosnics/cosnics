<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Microsoft\Graph\Model\Team;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupTeamService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository
     */
    protected $platformGroupTeamRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var TeamService
     */
    protected $teamService;
    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * PlatformGroupTeamService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService
     * @param GroupSubscriptionService $groupSubscriptionService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository,
        \Chamilo\Core\Group\Service\GroupService $groupService, \Chamilo\Core\User\Service\UserService $userService,
        \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService, GroupSubscriptionService $groupSubscriptionService
    )
    {
        $this->platformGroupTeamRepository = $platformGroupTeamRepository;
        $this->groupService = $groupService;
        $this->userService = $userService;
        $this->teamService = $teamService;
        $this->groupSubscriptionService = $groupSubscriptionService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $teamName
     * @param array $groupIds
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException
     */
    public function createTeamForSelectedGroups(User $owner, Course $course, string $teamName, $groupIds = [])
    {
        $userCount = 0;

        foreach ($groupIds as $groupId)
        {
            $group = $this->groupService->getGroupByIdentifier($groupId);

            if (!$group instanceof Group)
            {
                continue;
            }

            $groups[] = $group;

            $userCount += $group->count_users(true, true);
        }

        if ($userCount >= TeamService::MAX_USERS)
        {
            throw new TooManyUsersException();
        }

        $team = $this->teamService->createTeamByName($owner, $teamName);

        $platformGroupTeam = new PlatformGroupTeam();
        $platformGroupTeam->setCourseId($course->getId());
        $platformGroupTeam->setName($teamName);
        $platformGroupTeam->setTeamId($team->getId());

        if (!$this->platformGroupTeamRepository->createPlatformGroupTeam($platformGroupTeam))
        {
            throw new \RuntimeException(
                sprintf('The new team for the platform group (%s) could not be created', $teamName)
            );
        }

        $groups = [];

        foreach ($groups as $group)
        {
            $platformGroupTeamRelation = new PlatformGroupTeamRelation();
            $platformGroupTeamRelation->setGroupId($group->getId());
            $platformGroupTeamRelation->setPlatformGroupTeamId($platformGroupTeam->getId());

            if (!$this->platformGroupTeamRepository->createPlatformGroupTeamRelation($platformGroupTeamRelation))
            {
                throw new \RuntimeException(
                    'The relation between the team %s and the group %s could not be created'
                );
            }
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function addGroupUsersToPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $groups = $this->platformGroupTeamRepository->findGroupsForPlatformGroupTeam($platformGroupTeam);
        $team = $this->getTeam($platformGroupTeam);
        $this->addGroupUsersToTeam($team, $groups->getArrayCopy());
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeTeamUsersNotInGroups(PlatformGroupTeam $platformGroupTeam)
    {
        $groups = $this->platformGroupTeamRepository->findGroupsForPlatformGroupTeam($platformGroupTeam);
        $team = $this->getTeam($platformGroupTeam);

        $userIds = [];

        foreach ($groups as $group)
        {
            $userIds = array_merge($userIds, $this->groupSubscriptionService->findUserIdsInGroupAndSubgroups($group));
        }

        $users = $this->userService->findUsersByIdentifiers($userIds);

        $this->teamService->removeTeamMembersNotInArray($team, $users);
    }

    /**
     * @param int $platformGroupTeamId
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlatformGroupTeamById(int $platformGroupTeamId)
    {
        return $this->platformGroupTeamRepository->findPlatformGroupTeamById($platformGroupTeamId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getVisitTeamUrl(User $user, PlatformGroupTeam $platformGroupTeam)
    {
        $team = $this->getTeam($platformGroupTeam);

        if (!$team instanceof Team)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given team with id %s could not be found and can therefor not be visited',
                    $platformGroupTeam->getTeamId()
                )
            );
        }

        if (!$this->teamService->isOwner($user, $team))
        {
            $this->teamService->addMember($user, $team);
        }

        return $team->getWebUrl();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return array
     * @throws \Exception
     */
    public function getPlatformGroupTeamsForCourse(Course $course)
    {
        $platformGroupTeamsData =
            $this->platformGroupTeamRepository->findPlatformGroupTeamsWithPlatformGroupsForCourse($course);

        $data = [];

        foreach ($platformGroupTeamsData as $row)
        {
            $id = $row[PlatformGroupTeam::PROPERTY_ID];

            if (!array_key_exists($id, $data))
            {
                $newTeamName = $this->updateTeamNameById(
                    $id, $row[PlatformGroupTeam::PROPERTY_TEAM_ID], $row[PlatformGroupTeam::PROPERTY_NAME]
                );

                if (!$newTeamName)
                {
                    continue;
                }

                $row[PlatformGroupTeam::PROPERTY_NAME] = $newTeamName;

                $data[$id] = ['id' => $id, 'name' => $row[PlatformGroupTeam::PROPERTY_NAME], 'groups' => []];
            }

            $data[$id]['groups'][] = [
                'name' => $row[PlatformGroupTeamRepository::ALIAS_GROUP_NAME],
                'code' => $row[PlatformGroupTeamRepository::ALIAS_GROUP_CODE]
            ];
        }

        return array_values($data);
    }

    /**
     * Helper method for the record iterator above. Cleans up deleted teams and updates team names when changed
     *
     * @param int $platformGroupId
     * @param string $teamId
     * @param string $currentName
     *
     * @return null|string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    protected function updateTeamNameById(int $platformGroupId, string $teamId, string $currentName)
    {
        $team = $this->teamService->getTeam($teamId);
        if (!$team instanceof Team)
        {
            $platformGroupTeam = $this->findPlatformGroupTeamById($platformGroupId);
            $this->deleteRemovedTeam($platformGroupTeam);

            return null;
        }

        $teamName = $team->getProperties()['displayName'];

        if ($teamName != $currentName)
        {
            $platformGroupTeam = $this->findPlatformGroupTeamById($platformGroupId);
            $platformGroupTeam->setName($teamName);
            if (!$this->platformGroupTeamRepository->updatePlatformGroupTeam($platformGroupTeam))
            {
                throw new \RuntimeException(
                    'Could not update the platform group team with id ' . $platformGroupTeam->getId()
                );
            }
        }

        return $teamName;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return \Microsoft\Graph\Model\Team|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    protected function getTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $team = $this->teamService->getTeam($platformGroupTeam->getTeamId());

        if (is_null($team))
        {
            $this->deletePlatformGroupTeam($platformGroupTeam);

            return null;
        }

        return $team;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     */
    protected function deleteRemovedTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $this->platformGroupTeamRepository->deleteRelationsForPlatformGroupTeam($platformGroupTeam);
        $this->platformGroupTeamRepository->deletePlatformGroupTeam($platformGroupTeam);
    }

    /**
     * @param \Microsoft\Graph\Model\Team $team
     * @param array $groups
     */
    protected function addGroupUsersToTeam(Team $team, array $groups = [])
    {
        $userIds = [];

        foreach ($groups as $group)
        {
            $userIds = array_merge($userIds, $this->groupSubscriptionService->findUserIdsInGroupAndSubgroups($group));
        }

        $users = $this->userService->findUsersByIdentifiers($userIds);

        foreach ($users as $user)
        {
            try
            {
                $this->teamService->addMember($user, $team);
            }
            catch (AzureUserNotExistsException $exception)
            {
            }
        }
    }

    protected function deletePlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        throw new \RuntimeException('Not implemented');
    }
}