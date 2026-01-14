<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnectorCollection extends ArrayCollection
{

    public function addSettingsConnector(SettingsConnectorInterface $settingsConnector): void
    {
        $this->set($settingsConnector->getContext(), $settingsConnector);
    }

    public function existsForContext(string $context): bool
    {
        return $this->containsKey($context);
    }

    public function getSettingsConnectorForContext(string $context): ?SettingsConnectorInterface
    {
        return $this->get($context);
    }
}