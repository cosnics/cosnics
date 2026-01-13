<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException;
use Exception;
use Microsoft\Graph\GraphServiceClient;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{

    private GraphServiceClient $graphServiceClient;

    public function __construct(GraphServiceClient $graphServiceClient)
    {
        $this->graphServiceClient = $graphServiceClient;
    }

    protected function getGraphServiceClient(): GraphServiceClient
    {
        return $this->graphServiceClient;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     */
    public function getUser(User $user): ?\Microsoft\Graph\Generated\Models\User
    {
        try
        {
            $graphUser = $this->getGraphServiceClient()->users()->byUserId($user->get_email())->get()->wait();

            if ($graphUser instanceof \Microsoft\Graph\Generated\Models\User)
            {
                return $graphUser;
            }

            throw new UserNotFoundException($user);
        }
        catch (Exception $exception)
        {
            if ($exception->getCode() == 404)
            {
                throw new UserNotFoundException($user);
            }

            throw $exception;
        }
    }
}