<?php

namespace Chamilo\Core\API\Service\Oauth2;

use Chamilo\Core\API\Storage\Repository\AccessTokenRepository;
use Chamilo\Core\API\Storage\Repository\ClientRepository;
use Chamilo\Core\API\Storage\Repository\ScopeRepository;
use Chamilo\Libraries\File\PathBuilder;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

class AuthorizationServerFactory
{
    protected ClientRepository $clientRepository;
    protected ScopeRepository $scopeRepository;
    protected AccessTokenRepository $accessTokenRepository;
    protected PathBuilder $pathBuilder;

    public function __construct(
        ClientRepository $clientRepository,
        ScopeRepository $scopeRepository,
        AccessTokenRepository $accessTokenRepository,
        PathBuilder $pathBuilder
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->pathBuilder = $pathBuilder;
    }

    public function buildAuthorizationServer(): AuthorizationServer
    {
        $privateKeyPath = $this->pathBuilder->getStoragePath() . 'configuration/api/private.key';
        $encryptionKeyPath = $this->pathBuilder->getStoragePath() . 'configuration/api/secret.php';

        if(!file_exists($privateKeyPath) || !file_exists($encryptionKeyPath))
        {
            throw new \Exception(
                'Private key or encryption key not found, please configure the API before using it. 
                View README.md for more information.'
            );
        }

        $apiSecret = null;
        require_once($encryptionKeyPath);

        $server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $privateKeyPath,
            $apiSecret
        );

        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new \DateInterval('PT1H')
        );

        return $server;
    }
}