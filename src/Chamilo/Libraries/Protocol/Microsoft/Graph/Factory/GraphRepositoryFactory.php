<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Factory;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use stdClass;

/**
 * Factory class for Microsoft Graph
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphRepositoryFactory
{

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $chamiloRequest;

    /**
     * @var \Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter
     */
    protected $configurationConsulter;

    protected UrlGenerator $urlGenerator;

    /**
     * MicrosoftGraphRepositoryFactory constructor.
     *
     * @param \Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Interface\AccessTokenRepositoryInterface $accessTokenRepository
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, AccessTokenRepositoryInterface $accessTokenRepository,
        ChamiloRequest $request, UrlGenerator $urlGenerator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->chamiloRequest = $request;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Builds the Graph repository
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    public function buildGraphRepository()
    {
        $clientId = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_client_id']
        );

        $clientSecret = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_client_secret']
        );

        $tenantId = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_tenant_id']
        );

        if (empty($tenantId))
        {
            $tenantId = 'common';
        }

        $currentParameters = $this->chamiloRequest->query->all();
        $landingPageParameters = ['application' => 'Chamilo\Libraries\Protocol\Microsoft\Graph'];

        $state = base64_encode(
            json_encode(
                ['landingPageParameters' => $landingPageParameters, 'currentUrlParameters' => $currentParameters]
            )
        );

        $oauthClient = new GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'urlAuthorize' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/authorize',
                'urlAccessToken' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token',
                'redirectUri' => $this->urlGenerator->fromParameters(),
                'urlResourceOwnerDetails' => new stdClass(),
                'state' => $state
            ]
        );

        $tokenRequestContext = new ClientCredentialContext(
            $tenantId, $clientId, $clientSecret
        );

        return new GraphRepository(
            $oauthClient, new GraphServiceClient($tokenRequestContext), $this->accessTokenRepository
        );
    }
}