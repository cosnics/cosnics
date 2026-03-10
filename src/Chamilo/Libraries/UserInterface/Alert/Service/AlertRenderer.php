<?php
namespace Chamilo\Libraries\UserInterface\Alert\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;

/**
 * Renders notification messages
 *
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AlertRenderer
{
    public function render(Alert $alert): string
    {
        $html = [];

        $html[] = '<div class="alert alert-' . $alert->getType()->value . ' alert-dismissible" role="alert">';
        $html[] = $alert->getMessage();
        $html[] = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
