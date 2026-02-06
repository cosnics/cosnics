<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Factory;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphServiceClientFactory
{
    public function buildGraphServiceClient(string $tenantId, string $clientId, string $clientSecret
    ): GraphServiceClient
    {
        return new GraphServiceClient(
            new ClientCredentialContext($tenantId, $clientId, $clientSecret)
        );
    }
}