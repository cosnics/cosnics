<?php
namespace Chamilo\Libraries\UserInterface\Alert\Service;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AlertsRenderer
{
    protected AlertRenderer $alertRenderer;

    public function __construct(AlertRenderer $alertRenderer)
    {
        $this->alertRenderer = $alertRenderer;
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
            $html[] = $this->getAlertRenderer()->render($alert);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->alertRenderer;
    }
}
