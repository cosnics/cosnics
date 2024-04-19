<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\AuthCode;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
    }

    public function revokeAuthCode($codeId)
    {
    }

    public function isAuthCodeRevoked($codeId)
    {
        return false; // The auth code has not been revoked
    }

    public function getNewAuthCode()
    {
        return new AuthCode();
    }
}