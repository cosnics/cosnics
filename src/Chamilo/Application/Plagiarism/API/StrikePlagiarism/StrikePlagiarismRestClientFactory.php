<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Decorator\StrikePlagiarismAuthenticationDecorator;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Protocol\REST\Configuration\TokenBasedConfiguration;
use Chamilo\Libraries\Protocol\REST\Decorator\RestRequestDecoratorManager;
use Chamilo\Libraries\Protocol\REST\RestClient;
use Symfony\Component\Serializer\Serializer;

class StrikePlagiarismRestClientFactory
{
    protected ConfigurationConsulter $configurationConsulter;
    protected Serializer $serializer;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function createRestClient(): RestClient
    {
        $apiUrl = $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'strike_plagiarism_api_url']);
        $apiToken = $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'strike_plagiarism_api_token']);

        $configuration = new TokenBasedConfiguration($apiUrl, $apiToken);

        $decoratorManager = new RestRequestDecoratorManager();
        $decoratorManager->addRestRequestDecorator(new StrikePlagiarismAuthenticationDecorator($configuration));

        return new RestClient($configuration, $this->serializer, $decoratorManager);
    }
}