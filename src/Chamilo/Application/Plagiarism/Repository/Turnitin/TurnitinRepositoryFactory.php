<?php

namespace Chamilo\Application\Plagiarism\Repository\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinConfig;

/**
 * @package Chamilo\Application\Plagiarism\Repository\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinRepositoryFactory
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * TurnitinRepositoryFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(\Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    public function createTurnitinRepository()
    {
        $apiURL = $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'turnitin_api_url']);

        $secretKey =
            $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'turnitin_secret_key']);

        $config = new TurnitinConfig($apiURL, $secretKey);

        return new TurnitinRepository($config);
    }

}