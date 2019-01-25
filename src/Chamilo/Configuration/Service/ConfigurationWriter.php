<?php

namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Setting;

/**
 * @package Chamilo\Configuration\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ConfigurationWriter
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Configuration\Storage\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * ConfigurationWriter constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Storage\Repository\ConfigurationRepository $configurationRepository
     */
    public function __construct(
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter,
        \Chamilo\Configuration\Storage\Repository\ConfigurationRepository $configurationRepository
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @param string $context
     * @param string $variable
     * @param string $value
     */
    public function writeSetting(string $context, string $variable, string $value)
    {
        $setting = $this->configurationRepository->findSettingByContextAndVariable($context, $variable);

        if (!$setting instanceof Setting)
        {
            $this->createSetting($context, $variable, $value);

            return;
        }

        $setting->set_value($value);

        if (!$this->configurationRepository->updateSetting($setting))
        {
            throw new \RuntimeException(
                sprintf('The given setting could not be updated in the database (%s - %s)', $context, $variable)
            );
        }

        $this->reloadConfigurationCache();
    }

    /**
     * @param string $context
     * @param string $variable
     * @param string $value
     */
    public function createSetting(string $context, string $variable, string $value)
    {
        $setting = new Setting();

        $setting->set_context($context);
        $setting->set_variable($variable);
        $setting->set_value($value);

        if (!$this->configurationRepository->createSetting($setting))
        {
            throw new \RuntimeException(
                sprintf('The given setting could not be created in the database (%s - %s)', $context, $variable)
            );
        }

        $this->reloadConfigurationCache();
    }

    /**
     * Reloads the configuration cache after an important update
     */
    public function reloadConfigurationCache()
    {
        $this->configurationConsulter->clearData();
        $this->configurationConsulter->getData();
    }

}