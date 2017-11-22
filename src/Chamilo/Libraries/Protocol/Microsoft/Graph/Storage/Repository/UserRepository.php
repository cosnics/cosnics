<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    private $graphRepository;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    public function __construct(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    public function getGraphRepository()
    {
        return $this->graphRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    public function setGraphRepository(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Microsoft\Graph\Model\User
     */
    public function getAzureUser(User $user)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/users/' . $user->get_email(),
                \Microsoft\Graph\Model\User::class);
        }
        catch (\GuzzleHttp\Exception\ClientException $exception)
        {
            if ($exception->getCode() == GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)
            {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->getGraphRepository()->authorizeUserByAuthorizationCode($authorizationCode);
    }
}