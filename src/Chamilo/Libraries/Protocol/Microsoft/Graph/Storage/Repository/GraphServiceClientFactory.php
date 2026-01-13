<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphServiceClientFactory
{

    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function buildGraphServiceClient(): GraphServiceClient
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $clientId = $configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_client_id']
        );

        $clientSecret = $configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_client_secret']
        );

        $tenantId = $configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_tenant_id']
        );

        $tokenRequestContext = new ClientCredentialContext($tenantId, $clientId, $clientSecret);

        return new GraphServiceClient($tokenRequestContext);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }
}