<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorPageRenderer
{
    protected ApplicationHeaderRenderer $applicationHeaderRenderer;

    protected DefaultFooterRenderer $defaultFooterRenderer;

    protected NotificationMessageRenderer $notificationMessageRenderer;

    public function __construct(
        ApplicationHeaderRenderer $applicationHeaderRenderer, DefaultFooterRenderer $defaultFooterRenderer,
        NotificationMessageRenderer $notificationMessageRenderer
    )
    {
        $this->applicationHeaderRenderer = $applicationHeaderRenderer;
        $this->defaultFooterRenderer = $defaultFooterRenderer;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
    }

    public function render(ApplicationInterface $application, string $message, ?User $user = null): string
    {
        $html = [];

        $html[] = $this->getApplicationHeaderRenderer()->render($application, $user);
        $html[] = $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::error($message));
        $html[] = $this->getDefaultFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    public function getApplicationHeaderRenderer(): ApplicationHeaderRenderer
    {
        return $this->applicationHeaderRenderer;
    }

    public function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->defaultFooterRenderer;
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->notificationMessageRenderer;
    }
}