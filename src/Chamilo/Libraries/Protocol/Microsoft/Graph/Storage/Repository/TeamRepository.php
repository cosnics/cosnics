<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Model\Team;

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
     * @return \Microsoft\Graph\Model\Entity
     */
    public function createTeam($groupId)
    {
        $teamData = [

        ];

        return $this->graphRepository->executePostWithAccessTokenExpirationRetry(
            '/groups/' . $groupId . '/team',
            $teamData,
            Team::class
        );
    }

    /**
     *
     */
    public function updateTeam()
    {

    }

    /**
     * @param $teamIdentifier
     */
    public function getTeam($teamIdentifier)
    {

    }
}