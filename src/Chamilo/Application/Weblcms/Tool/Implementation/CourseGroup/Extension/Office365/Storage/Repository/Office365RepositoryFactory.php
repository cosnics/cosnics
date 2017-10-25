<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
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
     * Office365RepositoryFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter, AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->accessTokenRepository = $accessTokenRepository;
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

        $oauthClient = $provider = new \League\OAuth2\Client\Provider\GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'urlAuthorize' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/authorize',
                'urlAccessToken' => 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token',
                'urlResourceOwnerDetails' => new \stdClass()
            ]
        );

        $graph = new Graph();

        return new Office365Repository($oauthClient, $graph, $this->accessTokenRepository);
    }

}