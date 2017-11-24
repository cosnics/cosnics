<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Microsoft\Graph\Graph;

/**
 * Factory class for Office365
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365RepositoryFactory
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $chamiloRequest;

    /**
     * Office365RepositoryFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface $accessTokenRepository
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, AccessTokenRepositoryInterface $accessTokenRepository,
        ChamiloRequest $request
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->chamiloRequest = $request;
    }

    /**
     * Builds the office365 repository
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository
     */
    public function buildOffice365Repository()
    {
        $clientId = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'client_id']
        );

        $clientSecret = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'client_secret']
        );

        $tenantId = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'tenant_id']
        );

        $cosnicsPrefix = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'cosnics_prefix']
        );

        $redirect = new Redirect();

        $currentParameters = $this->chamiloRequest->query->all();
        $landingPageParameters = [
            'application' => 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        ];

        $state = base64_encode(
            json_encode(
                [
                    'landingPageParameters' => $landingPageParameters,
                    'currentUrlParameters' => $currentParameters
                ]
            )
        );

        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider(
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

        return new Office365Repository(
            $oauthClient, $graph, $this->accessTokenRepository, $cosnicsPrefix
        );
    }

}