<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Exception;
use Microsoft\Graph\Generated\Models\Team;
use Microsoft\Graph\Generated\Models\TeamMemberSettings;
use Microsoft\Graph\GraphServiceClient;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @TODO Fix class
 */
class TeamRepository
{
    protected GraphServiceClient $graphServiceClient;

    public function __construct(GraphServiceClient $graphServiceClient)
    {
        $this->graphServiceClient = $graphServiceClient;
    }

    /**
     * @throws \Exception
     */
    public function createTeam($groupId): ?Team
    {
        $memberSettings = new TeamMemberSettings();
        $memberSettings->setAllowCreateUpdateChannels(true);

        $team = new Team();
        $team->setMemberSettings($memberSettings);

        return $this->getGraphServiceClient()->groups()->byGroupId($groupId)->team()->put($team)->wait();
    }

    public function getGraphServiceClient(): GraphServiceClient
    {
        return $this->graphServiceClient;
    }

    /**
     * @throws \Exception
     */
    public function getTeam(string $groupId): Team
    {
        try {
            $team = $this->getGraphServiceClient()->teams()->byTeamId($groupId)->get()->wait();

            if (!$team instanceof Team) {
                throw new Exception('Team not found: ' . $groupId);
            }

            return $team;
        }
        catch (Exception) {
            throw new Exception('Team not found: ' . $groupId);
        }
    }

    /**
     * @throws \Exception
     */
    public function getUrl(string $groupId): string
    {
        return $this->getTeam($groupId)->getWebUrl();
    }
}