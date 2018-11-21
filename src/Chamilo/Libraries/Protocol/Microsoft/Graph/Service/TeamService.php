<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\TeamRepository;

/**
 * Class TeamService
 */
class TeamService
{
    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService
     */
    protected $groupService;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * TeamService constructor.
     * @param GroupService $groupService
     * @param TeamRepository $teamRepository
     */
    public function __construct(
        GroupService $groupService,
        TeamRepository $teamRepository
    )
    {
        $this->groupService = $groupService;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param $groupId
     * @return \Microsoft\Graph\Model\Entity
     */
    public function addTeamToGroup($groupId)
    {
        return $this->teamRepository->createTeam($groupId);
    }

    /**
     * @param User $owner
     * @param string $teamName
     * @return string
     * @throws AzureUserNotExistsException
     */
    public function createTeamByName(User $owner, string $teamName)
    {
        $groupId = $this->groupService->createGroupByName($owner, $teamName);

        $this->addTeamToGroup($groupId);

        return $groupId;
    }
}