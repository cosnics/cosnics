<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnectorRegistry
{
    public function __construct(protected ArrayCollection $settingsConnectors = new ArrayCollection())
    {
    }

    public function addSettingsConnector(SettingsConnectorInterface $settingsConnector): void
    {
        $this->settingsConnectors->set($settingsConnector->getContext(), $settingsConnector);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getSettingsConnectorForContext(string $context): ?SettingsConnectorInterface
    {
        if (!$this->settingsConnectors->containsKey($context)) {
            throw new NoSuchClassException($context, SettingsConnectorInterface::class);
        }

        return $this->settingsConnectors->get($context);
    }
}