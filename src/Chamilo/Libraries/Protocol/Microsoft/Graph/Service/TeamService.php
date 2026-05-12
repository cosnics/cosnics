<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\Entity\User;
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
    public function __construct(
        protected GroupService $groupService, protected TeamRepository $teamRepository
    )
    {
    }

    public function addTeamToGroup(string $groupId, int $retryCounter = 0): void
    {
        try {
            $this->teamRepository->createTeam($groupId);
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     */
    public function createTeamByName(User $owner, string $teamName): string
    {
        $groupId = $this->groupService->createGroupByName($owner, $teamName);

        $this->addTeamToGroup($groupId);

        return $groupId;
    }

    /**
     * @throws \Exception
     */
    public function getTeam(string $groupId): Team
    {
        return $this->teamRepository->getTeam($groupId);
    }

    /**
     * @throws \Exception
     */
    public function getTeamUrl(string $groupId): string
    {
        return $this->teamRepository->getUrl($groupId);
    }
}