<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException;
use Exception;
use Microsoft\Graph\GraphServiceClient;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{
    public function __construct(protected GraphServiceClient $graphServiceClient)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     */
    public function getUser(User $user): ?\Microsoft\Graph\Generated\Models\User
    {
        try {
            $graphUser = $this->graphServiceClient->users()->byUserId($user->getEmail())->get()->wait();

            if ($graphUser instanceof \Microsoft\Graph\Generated\Models\User) {
                return $graphUser;
            }

            throw new NoSuchUserException($user);
        }
        catch (Exception $exception) {
            if ($exception->getCode() == 404) {
                throw new NoSuchUserException($user);
            }

            throw $exception;
        }
    }
}