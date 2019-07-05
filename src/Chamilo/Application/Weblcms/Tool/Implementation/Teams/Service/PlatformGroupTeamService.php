<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation;
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
     * @var TeamService
     */
    protected $teamService;

    /**
     * PlatformGroupTeamService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository,
        \Chamilo\Core\Group\Service\GroupService $groupService,
        \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService
    )
    {
        $this->platformGroupTeamRepository = $platformGroupTeamRepository;
        $this->groupService = $groupService;
        $this->teamService = $teamService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $teamName
     * @param array $groupIds
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createTeamForSelectedGroups(User $owner, Course $course, string $teamName, $groupIds = [])
    {
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

        foreach ($groupIds as $groupId)
        {
            $group = $this->groupService->getGroupByIdentifier($groupId);

            if (!$group instanceof Group)
            {
                continue;
            }

            $groups[] = $group;

            $platformGroupTeamRelation = new PlatformGroupTeamRelation();
            $platformGroupTeamRelation->setGroupId($groupId);
            $platformGroupTeamRelation->setPlatformGroupTeamId($platformGroupTeam->getId());

            if (!$this->platformGroupTeamRepository->createPlatformGroupTeamRelation($platformGroupTeamRelation))
            {
                throw new \RuntimeException(
                    'The relation between the team %s and the group %s could not be created'
                );
            }
        }
//        $this->addGroupUsersToTeam($team, $groups);
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
        $this->addGroupUsersToTeam($team, $groups);
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

        $groupUserIds = $groupUsers = [];

        foreach ($groups as $group)
        {
            $groupUserIds[] = array_merge($groupUserIds, $group->get_users(true, true));
        }

        $groupUserIds = array_unique($groupUserIds);

        foreach ($groupUserIds as $groupUserId)
        {
            $user = new User();
            $user->setId($groupUserId);

            $groupUsers[] = $user;
        }

        $this->teamService->removeTeamMembersNotInArray($team, $groupUsers);
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
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getVisitTeamUrl(PlatformGroupTeam $platformGroupTeam)
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

        return $team->getWebUrl();
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
            $this->platformGroupTeamRepository->deleteRelationsForPlatformGroupTeam($platformGroupTeam);
            $this->platformGroupTeamRepository->deletePlatformGroupTeam($platformGroupTeam);

            return null;
        }

        return $team;
    }

    /**
     * @param \Microsoft\Graph\Model\Team $team
     * @param array $groups
     */
    protected function addGroupUsersToTeam(Team $team, array $groups = [])
    {
        foreach ($groups as $group)
        {
            $userIds = $group->get_users(true, true);
            foreach ($userIds as $userId)
            {
                $user = new User();
                $user->setId($userId);

                try
                {
                    $this->teamService->addMember($user, $team);
                }
                catch (AzureUserNotExistsException $exception)
                {

                }
            }
        }
    }

    protected function deletePlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        throw new \RuntimeException('Not implemented');
    }
}