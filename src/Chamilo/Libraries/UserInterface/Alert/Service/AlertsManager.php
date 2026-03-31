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
    public function __construct(
        protected AlertStorageInterface $alertStorage, protected AlertsRenderer $alertsRenderer
    )
    {
    }

    public function render(): string
    {
        $alerts = $this->alertStorage->retrieve();

        $this->alertStorage->clear();

        return $this->alertsRenderer->render($alerts);
    }

    public function addAlert(Alert $alert): static
    {
        $alerts = $this->alertStorage->retrieve();

        $alerts[] = $alert;

        $this->alertStorage->store($alerts);

        return $this;
    }
}