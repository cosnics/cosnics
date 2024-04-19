<?php
namespace Chamilo\Core\API\Component;

use Chamilo\Core\API\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TokenComponent extends Manager implements NoAuthenticationSupport
{
    function run()
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        try
        {
            $accessTokenResponse = $this->getAuthorizationServer()->respondToAccessTokenRequest(
                $psrHttpFactory->createRequest($this->getRequest()), $psr17Factory->createResponse()
            );
        }
        catch(OAuthServerException $ex)
        {
            return new JsonResponse(['error' => $ex->getMessage()], 500);
        }

        $httpFoundationFactory = new HttpFoundationFactory();
        return $httpFoundationFactory->createResponse($accessTokenResponse);
    }

    protected function getAuthorizationServer(): AuthorizationServer
    {
        return $this->getService(AuthorizationServer::class);
    }
}
