<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\TeamNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\TeamRepository;
use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Model\Group;
use Microsoft\Graph\Model\Team;

/**
 * Class TeamService
 */
class TeamService
{
    const MAX_USERS = 5000;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected $userService;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService
     */
    protected $groupService;

    /**
     * TeamService constructor.
     *
     * @param GroupService $groupService
     * @param TeamRepository $teamRepository
     * @param UserService $userService
     */
    public function __construct(
        GroupService $groupService,
        TeamRepository $teamRepository,
        UserService $userService
    )
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param string $title
     * @param string $description
     * @param User $owner
     *
     * @return string
     * @throws \Exception
     */
    public function createTeam(string $title, string $description, User $owner)
    {
        $ownerAzureId = $this->userService->getAzureUserIdentifier($owner);

        return $this->teamRepository->createTeam($title, $description, $ownerAzureId);
    }

    /**
     * @param string $groupId
     * @param int $retryCounter
     *
     * @return Team
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @deprecated
     */
    public function addTeamToGroup(string $groupId, int $retryCounter = 0): Team
    { //todo queue implementation
        try
        {
            return $this->teamRepository->addTeamToGroup($groupId);
        }
        catch (ClientException $exception)
        {
            if ($exception->getCode() == 404 && $retryCounter < 3)
            {//group maybe not created due to replication delay
                $retryCounter ++;
                sleep(10);

                return $this->addTeamToGroup($groupId, $retryCounter);
            }
            else
            {
                throw $exception;
            }
        }
    }

    /**
     * @param string $groupId
     *
     * @return Team | null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     *
     */
    public function getTeam(string $groupId): ?Team
    {
        $team = $this->teamRepository->getTeam($groupId);

        if (!$team instanceof Team)
        {
            return null;
        }

        return $team;
    }

    /**
     * @param Group $group
     *
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws TeamNotFoundException
     */
    public function getTeamUrl(Group $group)
    {
        $team = $this->getTeam($group);
        if(!$team instanceof Team)
        {
            throw new TeamNotFoundException($group->getId());
        }

        return $team->getWebUrl();
    }

    /**
     * @param User $owner
     * @param string $teamName
     *
     * @return Team
     * @throws AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function createTeamByName(User $owner, string $teamName): Team
    {
        $groupId = $this->groupService->createGroupByName($owner, $teamName);

        return $this->addTeamToGroup($groupId);
    }

    /**
     * @param Team $team
     * @param string $newName
     */
    public function updateTeamName(Team $team, string $newName)
    {
        $this->groupService->updateGroupName($team->getId(), $newName);
    }

    /**
     * @param User $user
     * @param Team $team
     *
     * @return bool
     */
    public function isMember(User $user, Team $team): bool
    {
        return $this->groupService->isMemberOfGroup($team->getId(), $user);
    }

    /**
     * @param User $user
     * @param Team $team
     *
     * @return bool
     */
    public function isOwner(User $user, Team $team): bool
    {
        return $this->groupService->isOwnerOfGroup($team->getId(), $user);
    }

    /**
     * @param User $user
     * @param Team $team
     *
     * @throws AzureUserNotExistsException
     * @throws UnknownAzureUserIdException
     */
    public function addMember(User $user, Team $team)
    {
        $this->groupService->addMemberToGroup($team->getId(), $user);
    }

    /**
     * @param Team $team
     * @param User[] $members
     */
    public function removeTeamMembersNotInArray(Team $team, array $members)
    {
        $this->groupService->removeGroupMembersNotInArray($team->getId(), $members);
    }

    /**
     * @param Team $team
     * @param User[] $owners
     */
    public function removeTeamOwnersNotInArray(Team $team, array $owners)
    {
        $this->groupService->removeGroupOwnersNotInArray($team->getId(), $owners);
    }

    /**
     * @param User $user
     * @param Team $team
     *
     * @throws AzureUserNotExistsException
     */
    public function addOwner(User $user, Team $team)
    {
        $this->groupService->addOwnerToGroup($team->getId(), $user);
    }
}
