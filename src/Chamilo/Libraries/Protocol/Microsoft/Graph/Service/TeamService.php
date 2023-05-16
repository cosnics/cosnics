<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\TeamRepository;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Model\Group;

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
     *
     * @param GroupService $groupService
     * @param TeamRepository $teamRepository
     */
    public function __construct(
        GroupService $groupService, TeamRepository $teamRepository
    )
    {
        $this->groupService = $groupService;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param string $groupId
     * @param int $retryCounter
     */
    public function addTeamToGroup(string $groupId, int $retryCounter = 0)
    {
        // TODO queue implementation
        try
        {
            $this->teamRepository->createTeam($groupId);
        }
        catch (ClientException $exception)
        {
            if ($exception->getCode() == 404 && $retryCounter < 3)
            {
                //group maybe not created due to replication delay
                $retryCounter ++;
                sleep(10);
                $this->addTeamToGroup($groupId, $retryCounter);
            }
            else
            {
                throw $exception;
            }
        }
    }

    /**
     * @param User $owner
     * @param string $teamName
     *
     * @return string
     * @throws AzureUserNotExistsException
     */
    public function createTeamByName(User $owner, string $teamName)
    {
        $groupId = $this->groupService->createGroupByName($owner, $teamName);

        $this->addTeamToGroup($groupId);

        return $groupId;
    }

    public function getTeam(string $groupId)
    {
        try
        {
            return $this->teamRepository->getTeam($groupId);
        }
        catch (Exception $exception)
        {
            return null;
        }
    }

    /**
     * @param Group $group
     *
     * @return string
     */
    public function getTeamUrl(Group $group)
    {
        return $this->teamRepository->getUrl($group->getId());
    }
}