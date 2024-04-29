<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        return null;
    }

    public function finalizeScopes(
        array $scopes,
              $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null,
        $authCodeId = null
    ): array
    {
        return $scopes;
    }
}