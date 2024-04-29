<?php
namespace Chamilo\Core\API\Component;

use Chamilo\Core\API\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
#[OA\Info(
    version: '1.0.0', description: '<h3>Welcome to the API for Chamilo</h3><p>To authenticate to the API we use the OAuth2 protocol with the client_credentials flow.</p><ol><li>Retrieve an authorization token with a post request to /api/token. You must provide your client_id and client_secret in the body of the request. The server will return an access token which is valid for a limited amount of time.<p><code>client_id={{my_id}}&client_secret={{my_secret}}&grant_type=client_credentials</code></p></li><li>Use the retrieved access token from step 1 in the authorization header of all subsequent calls to our API.<p><code>Authorization: Bearer access_token</code></p></li><p>More information: <a href="https://www.oauth.com/oauth2-servers/access-tokens/client-credentials/" target="_blank">https://www.oauth.com/oauth2-servers/access-tokens/client-credentials/</a>',
    title: 'CHAMILO API'

)]
#[OA\Server(url: '/api', description: 'default')]
#[OA\Server(url: '/chamilo/web/api', description: 'localhost')]
#[OA\Server(url: '/php8/current/web/api', description: 'staging')]

#[OA\SecurityScheme(
    securityScheme: 'oauth', type: 'oauth2',
    flows: [new OA\Flow(tokenUrl: 'token', flow: 'clientCredentials', scopes: [])]
)
]
class TestComponent extends Manager
{
    const PARAM_ID = 'id';

    /*#[OA\Get(path: '/test/{id}', operationId: 'test', description: 'Test', summary: 'Test', security: [['oauth' => []]], tags: ['Test'])]
    #[OA\Parameter(
        name: 'id',
        description: 'ID',
        in: 'path',
        required: true,
    )]
    #[OA\Response(
        response: 200,
        description: 'Test',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'result',
                    summary: 'The result of the call',
                    value: ['id' => 1]
                )
            ]
        )
    )]*/
    function run(): JsonResponse
    {
        if($this->getRequest()->attributes->get('oauth_client_id') != 'sven')
            throw new NotAllowedException();

        return new JsonResponse(['id' => $this->get_parameter(self::PARAM_ID)]);
    }


}
