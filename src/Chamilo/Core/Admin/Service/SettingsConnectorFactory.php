<?php
namespace Chamilo\Core\Admin\Service;

/**
 * @package Chamilo\Core\Admin\Service
 */
class SettingsConnectorFactory
{
    /**
     * @var \Chamilo\Core\Admin\Service\SettingsConnectorInterface[]
     */
    protected array $settingsConnectors;

    public function __construct()
    {
        $this->settingsConnectors = [];
    }

    public function addSettingsConnector(SettingsConnectorInterface $settingsConnector): void
    {
        $this->settingsConnectors[$settingsConnector->getContext()] = $settingsConnector;
    }

    public function existsForContext(string $context): bool
    {
        return array_key_exists($context, $this->settingsConnectors);
    }

    public function getSettingsConnectorForContext(string $context): ?SettingsConnectorInterface
    {
        return $this->settingsConnectors[$context];
    }
}