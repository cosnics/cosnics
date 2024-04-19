<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface
    {
        var_dump(__FUNCTION__);

        if ($username === 'alex' && $password === 'whisky') {
            return new User();
        }

        return null;
    }
}