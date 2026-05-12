<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\Menu\UserInterface\MenuRenderer\MenuRenderer;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbTrailRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DefaultHeaderRenderer
{
    public function __construct(
        protected BaseHeaderRenderer $baseHeaderRenderer, protected BreadcrumbTrail $breadcrumbTrail,
        protected AlertsManager $notificationMessageManager, protected BreadcrumbTrailRenderer $breadcrumbTrailRenderer,
        protected MenuRenderer $menuRenderer
    )
    {
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $html[] = $this->baseHeaderRenderer->renderHeader();

        $html[] = '<header>';

        $html[] = $this->menuRenderer->render($user);

        if ($this->breadcrumbTrail->count() > 0) {
            $html[] = $this->breadcrumbTrailRenderer->render($this->breadcrumbTrail);
        }

        $html[] = '</header>';

        $html[] = '<main class="container-xxl">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-12 clearfix">';
        $html[] = $this->renderPageTitle();

        $html[] = $this->notificationMessageManager->render();

        return implode(PHP_EOL, $html);
    }

    protected function renderPageTitle(): string
    {
        if ($this->breadcrumbTrail->count() > 0) {
            $pageTitle = $this->breadcrumbTrail->last()->getName();

            return '<h3 title="' . htmlentities(strip_tags($pageTitle)) . '">' . $pageTitle . '</h3>';
        }

        return '';
    }
}