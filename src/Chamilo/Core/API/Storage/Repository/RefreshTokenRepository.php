<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\RefreshToken;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
    }

    public function revokeRefreshToken($tokenId): void
    {
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        return false; // The refresh token has not been revoked
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }
}