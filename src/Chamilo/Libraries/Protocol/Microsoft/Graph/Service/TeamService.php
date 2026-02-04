<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\UserNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\TeamRepository;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Generated\Models\Team;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TeamService
{
    protected GroupService $groupService;

    protected TeamRepository $teamRepository;

    public function __construct(
        GroupService $groupService, TeamRepository $teamRepository
    )
    {
        $this->groupService = $groupService;
        $this->teamRepository = $teamRepository;
    }

    public function addTeamToGroup(string $groupId, int $retryCounter = 0): void
    {
        try {
            $this->getTeamRepository()->createTeam($groupId);
        }
        catch (Exception|ClientException $exception) {
            if ($exception->getCode() == 404 && $retryCounter < 3) {
                $retryCounter ++;
                sleep(10);
                $this->addTeamToGroup($groupId, $retryCounter);
            }
            else {
                throw $exception;
            }
        }
    }

    /**
     * @throws UserNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function createTeamByName(User $owner, string $teamName): string
    {
        $groupId = $this->getGroupService()->createGroupByName($owner, $teamName);

        $this->addTeamToGroup($groupId);

        return $groupId;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @throws \Exception
     */
    public function getTeam(string $groupId): Team
    {
        return $this->getTeamRepository()->getTeam($groupId);
    }

    public function getTeamRepository(): TeamRepository
    {
        return $this->teamRepository;
    }

    /**
     * @throws \Exception
     */
    public function getTeamUrl(string $groupId): string
    {
        return $this->teamRepository->getUrl($groupId);
    }
}