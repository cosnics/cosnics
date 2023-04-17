<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\Repository\ConfigurationRepository;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationService
{

    private ConfigurationRepository $configurationRepository;

    public function __construct(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function findSettingByContextAndVariableName(string $context, string $variable): ?Setting
    {
        return $this->getConfigurationRepository()->findSettingByContextAndVariableName($context, $variable);
    }

    protected function getConfigurationRepository(): ConfigurationRepository
    {
        return $this->configurationRepository;
    }
}