<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Architecture\Exceptions\ValueNotInArrayException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
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
     *
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
     *
     * @return string
     * @throws GraphException
     */
    public function createClassTeam(string $title, string $description, string $ownerAzureId)
    {
        return $this->createTeam($title, $description, $ownerAzureId, 'educationClass');
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $ownerAzureId
     *
     * @return string
     * @throws GraphException
     */
    public function createStandardTeam(string $title, string $description, string $ownerAzureId)
    {
        return $this->createTeam($title, $description, $ownerAzureId);
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $ownerAzureId
     *
     * @param string $template
     *
     * @return string
     * @throws GraphException
     * @throws \Exception
     */
    protected function createTeam(
        string $title, string $description, string $ownerAzureId, string $template = 'standard'
    )
    {
        $allowedTemplates = ['standard', 'educationClass'];
        if (!in_array($template, $allowedTemplates))
        {
            throw new ValueNotInArrayException('template', $template, $allowedTemplates);
        }

        $parameters =  [
            "template@odata.bind" => "https://graph.microsoft.com/beta/teamsTemplates('" . $template . "')",
            "displayName" => $title,
            "description" => $description,
            "owners@odata.bind" => [
                "https://graph.microsoft.com/beta/users('" . $ownerAzureId . "')"
            ]
        ];

        if($template == 'standard')
        {
            $parameters['visibility'] = 'Private';
        }

        $response = $this->graphRepository->executePostWithAccessTokenExpirationRetry(
            '/teams/', $parameters, null, GraphRepository::API_VERSION_BETA
        );

        //Content-Location: /teams/{teamId}/operation/{operationId}
        $locationHeader = $response->getHeaders()['Location'];
        if (!$locationHeader)
        {
            throw new \Exception("No location header");
        }

        $locationString = $locationHeader[0];
        if (!$locationString)
        {
            throw new \Exception("Location header does not contain Team ID: " . $locationHeader);
        }

        $matches = [];
        preg_match('/\/teams\(\'(.*?)\'\).*/', $locationString, $matches);

        if (!$matches[1])
        {
            throw new \Exception("Invalid Team Id");
        }

        return $matches[1];
    }

    /**
     * @param string $groupId
     *
     * @return \Microsoft\Graph\Model\Entity | Team
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @deprecated
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
        try
        {
            return $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/teams/' . $teamId,
                Team::class
            );
        }
        catch (GraphException $exception)
        {
            $messageParts = explode("API response: ", $exception->getMessage());
            $bodyContents = json_decode($messageParts[1], true);

            if (
                $exception->getCode() == 404 && is_array($bodyContents) && array_key_exists('error', $bodyContents) &&
                $bodyContents['error']['message'] == 'No team found with Group Id ' . $teamId
            )
            {
                return null;
            }
            else
            {
                throw new GraphException("Could not retrieve team with id" . $teamId, 0, $exception);
            }
        }
    }

}
