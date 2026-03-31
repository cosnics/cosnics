<?php
namespace Chamilo\Libraries\UserInterface\Alert\Service;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AlertsRenderer
{
    public function __construct(protected AlertRenderer $alertRenderer)
    {
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[] $alerts
     */
    public function render(array $alerts = []): string
    {
        if (empty($alerts)) {
            return '';
        }

        $html = [];

        $html[] = '<div class="alerts position-fixed top-0 end-0 m-3">';

        foreach ($alerts as $alert) {
            $html[] = $this->alertRenderer->render($alert);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
