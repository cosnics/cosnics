<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorPageRenderer
{
    protected AlertRenderer $alertRenderer;

    protected ApplicationHeaderRenderer $applicationHeaderRenderer;

    protected DefaultFooterRenderer $defaultFooterRenderer;

    public function __construct(
        ApplicationHeaderRenderer $applicationHeaderRenderer, DefaultFooterRenderer $defaultFooterRenderer,
        AlertRenderer $alertRenderer
    )
    {
        $this->applicationHeaderRenderer = $applicationHeaderRenderer;
        $this->defaultFooterRenderer = $defaultFooterRenderer;
        $this->alertRenderer = $alertRenderer;
    }

    public function render(ApplicationInterface $application, string $message, ?User $user = null): string
    {
        $html = [];

        $html[] = $this->getApplicationHeaderRenderer()->render($application, $user);
        $html[] = $this->getAlertRenderer()->render(Alert::error($message));
        $html[] = $this->getDefaultFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->alertRenderer;
    }

    public function getApplicationHeaderRenderer(): ApplicationHeaderRenderer
    {
        return $this->applicationHeaderRenderer;
    }

    public function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->defaultFooterRenderer;
    }
}