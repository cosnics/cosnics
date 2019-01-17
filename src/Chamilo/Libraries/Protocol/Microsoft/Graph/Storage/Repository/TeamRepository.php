<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Microsoft\Graph\Model\Team;

/**
 * Class TeamRepository
 */
class TeamRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    private $graphRepository;

    /**
     * GroupRepository constructor.
     * @param GraphRepository $graphRepository
     */
    public function __construct(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }


    /**
     * @param $groupId
     * @return \Microsoft\Graph\Model\Entity | Team
     */
    public function addTeamToGroup(string $groupId): \Microsoft\Graph\Model\Entity
    {
        return $this->graphRepository->executePutWithAccessTokenExpirationRetry(
            '/groups/' . $groupId . '/team',
            [
                "memberSettings" =>
                    [
                        "allowCreateUpdateChannels" => true
                    ]
            ],
            Team::class
        );
    }

    /**
     * @param string $teamId
     * @return \Microsoft\Graph\Model\Entity | Team
     */
    public function getTeam(string $teamId): \Microsoft\Graph\Model\Entity
    {
        return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
            '/teams/' . $teamId,
            Team::class
        );
    }

    /**
     * @param string $teamId
     * @return string
     */
    public function getUrl(string $teamId): string
    {
        /**
         * @var Team $team
         */
        $team = $this->getTeam($teamId);

        return $team->getWebUrl();
    }

}