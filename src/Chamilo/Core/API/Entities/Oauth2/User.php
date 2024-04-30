<?php

namespace Chamilo\Core\API\Entities\Oauth2;

use League\OAuth2\Server\Entities\UserEntityInterface;

class User implements UserEntityInterface
{
    /**
     * Return the user's identifier.
     */
    public function getIdentifier(): mixed
    {
        return 1;
    }
}