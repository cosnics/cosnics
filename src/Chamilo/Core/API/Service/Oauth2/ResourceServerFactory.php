<?php

namespace Chamilo\Core\API\Service\Oauth2;

use Chamilo\Core\API\Storage\Repository\AccessTokenRepository;
use Chamilo\Libraries\File\PathBuilder;
use League\OAuth2\Server\ResourceServer;

class ResourceServerFactory
{
    protected AccessTokenRepository $accessTokenRepository;
    protected PathBuilder $pathBuilder;

    public function __construct(
        AccessTokenRepository $accessTokenRepository,
        PathBuilder $pathBuilder
    )
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->pathBuilder = $pathBuilder;
    }

    public function buildResourceServer(): ResourceServer
    {
        $publicKeyPath = $this->pathBuilder->getStoragePath() . 'configuration/api/public.key';

        if(!file_exists($publicKeyPath))
        {
            throw new \Exception(
                'Public key not found, please configure the API before using it. 
                View README.md for more information.'
            );
        }

        return new ResourceServer(
            $this->accessTokenRepository,
            $publicKeyPath
        );
    }




}