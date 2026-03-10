<?php
namespace Chamilo\Libraries\UserInterface\Alert\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Interface\AlertStorageInterface;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AlertsManager
{
    protected AlertStorageInterface $alertStorage;

    protected AlertsRenderer $alertsRenderer;

    public function __construct(
        AlertStorageInterface $alertStorage, AlertsRenderer $alertsRenderer
    )
    {
        $this->alertStorage = $alertStorage;
        $this->alertsRenderer = $alertsRenderer;
    }

    public function render(): string
    {
        $alerts = $this->getAlertStorage()->retrieve();

        $this->getAlertStorage()->clear();

        return $this->getAlertsRenderer()->render($alerts);
    }

    public function addAlert(Alert $alert): static
    {
        $alerts = $this->getAlertStorage()->retrieve();

        $alerts[] = $alert;

        $this->getAlertStorage()->store($alerts);

        return $this;
    }

    public function getAlertStorage(): AlertStorageInterface
    {
        return $this->alertStorage;
    }

    public function getAlertsRenderer(): AlertsRenderer
    {
        return $this->alertsRenderer;
    }
}