<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Microsoft\Graph\Graph;

/**
 * Factory class for Microsoft Graph
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphRepositoryFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $chamiloRequest;

    /**
     * MicrosoftGraphRepositoryFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(ConfigurationConsulter $configurationConsulter,
        AccessTokenRepositoryInterface $accessTokenRepository, ChamiloRequest $request)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->chamiloRequest = $request;
    }

    /**
     * Builds the Graph repository
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    public function buildGraphRepository()
    {
        $clientId = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'client_id']);

        $clientSecret = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'client_secret']);

        $tenantId = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'tenant_id']);

        $redirect = new Redirect(['application' => 'Chamilo\Libraries\Protocol\Microsoft\Graph']);

        $currentParameters = $this->chamiloRequest->query->all();
        $landingPageParameters = [
            'application' => 'Chamilo\Libraries\Protocol\Microsoft\Graph'
        ];

        $state = base64_encode(
            json_encode(
                [
                    'landingPageParameters' => $landingPageParameters,
                    'currentUrlParameters' => $currentParameters
                ]
                )
            );

        $oauthClient = $provider = new \League\OAuth2\Client\Provider\GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'urlAuthorize' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/authorize',
                'urlAccessToken' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token',
                'redirectUri' => $redirect->getUrl(),
                'urlResourceOwnerDetails' => new \stdClass(),
                'state' => $state
            ]
            );

        $graph = new Graph();

        return new GraphRepository($oauthClient, $graph, $this->accessTokenRepository, $this->chamiloRequest->getUri());
    }
}