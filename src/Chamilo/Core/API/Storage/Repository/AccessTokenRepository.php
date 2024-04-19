<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\AccessToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository extends \Doctrine\ORM\EntityRepository implements AccessTokenRepositoryInterface
{
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $this->_em->persist($accessTokenEntity);
        $this->_em->flush();
    }

    public function revokeAccessToken($tokenId): void
    {
        $accessToken = $this->findOneBy(['identifier' => $tokenId]);
        if($accessToken instanceof AccessToken)
        {
           $this->_em->remove($accessToken);
           $this->_em->flush();
        }
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        $accessToken = $this->findOneBy(['identifier' => $tokenId]);
        return !$accessToken instanceof AccessToken;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();

        $accessToken->setClient($clientEntity);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        if ($userIdentifier !== null) {
            $accessToken->setUserIdentifier((string) $userIdentifier);
        }

        return $accessToken;
    }
}