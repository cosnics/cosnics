<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;

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
        $this->setGraphRepository($graphRepository);
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected function getGraphRepository()
    {
        return $this->graphRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     */
    protected function setGraphRepository(GraphRepository $graphRepository)
    {
        $this->graphRepository = $graphRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Microsoft\Graph\Model\User | \Microsoft\Graph\Model\Entity
     * @throws GraphException
     */
    public function getAzureUser(User $user)
    {
        try
        {
            return $this->getGraphRepository()->executeGetWithAccessTokenExpirationRetry(
                '/users/' . $user->get_email(),
                \Microsoft\Graph\Model\User::class);
        }
        catch (GraphException $exception)
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
