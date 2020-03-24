<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Http\GraphResponse;
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
     * @param string $title
     * @param string $description
     * @param string $ownerAzureId
     * @return string
     * @throws GraphException
     * @throws \Exception
     */
    public function createTeam(string $title, string $description, string $ownerAzureId)
    {
        $response = $this->graphRepository->executePostWithAccessTokenExpirationRetry(
              '/teams/',
            [
                "template@odata.bind" => "https://graph.microsoft.com/beta/teamsTemplates('educationClass')",
                "displayName" => $title,
                "description" => $description,
                "owners@odata.bind" => [
                    "https://graph.microsoft.com/beta/users('" . $ownerAzureId . "')"
                ]
            ],
            null,
            GraphRepository::API_VERSION_BETA
        );

        //Content-Location: /teams/{teamId}/operation/{operationId}
        $locationHeader = $response->getHeaders()['Location'];
        if(!$locationHeader)
            throw new \Exception("No location header");

        $locationString = $locationHeader[0];
        if(!$locationString)
            throw new \Exception("Location header does not contain Team ID: " . $locationHeader);

        $matches = [];
        preg_match('/\/teams\(\'(.*?)\'\).*/', $locationString, $matches);

        if(!$matches[1])
            throw new \Exception("Invalid Team Id");

        return $matches[1];
    }


    /**
     * @deprecated
     * @param $groupId
     * @return \Microsoft\Graph\Model\Entity | Team
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
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
     *
     * @return \Microsoft\Graph\Model\Entity | Team
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function getTeam(string $teamId): ?\Microsoft\Graph\Model\Entity
    {
        try {
            return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/teams/' . $teamId,
                Team::class
            );
        } catch ( \GuzzleHttp\Exception\ClientException $exception){
            if($exception->getCode() == 404) {
                return null;
            }
            else {
                throw new GraphException("Could not retrieve team with id" . $teamId, 0, $exception);
            }
        }
    }

    /**
     * @param string $teamId
     * @return string
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
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
