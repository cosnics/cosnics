<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\Menu\UserInterface\MenuRenderer\MenuRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbTrailRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageManager;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DefaultHeaderRenderer
{
    protected BaseHeaderRenderer $baseHeaderRenderer;

    protected BreadcrumbTrail $breadcrumbTrail;

    protected BreadcrumbTrailRenderer $breadcrumbTrailRenderer;

    protected MenuRenderer $menuRenderer;

    protected NotificationMessageManager $notificationMessageManager;

    public function __construct(
        BaseHeaderRenderer $baseHeaderRenderer, BreadcrumbTrail $breadcrumbTrail,
        NotificationMessageManager $notificationMessageManager, BreadcrumbTrailRenderer $breadcrumbTrailRenderer,
        MenuRenderer $menuRenderer
    )
    {
        $this->baseHeaderRenderer = $baseHeaderRenderer;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->notificationMessageManager = $notificationMessageManager;
        $this->breadcrumbTrailRenderer = $breadcrumbTrailRenderer;
        $this->menuRenderer = $menuRenderer;
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $html[] = $this->getBaseHeaderRenderer()->renderHeader();

        $html[] = '<header>';

        $html[] = $this->getMenuRenderer()->render($user);

        $breadcrumbtrail = $this->getBreadcrumbTrail();

        if ($breadcrumbtrail->count() > 0) {
            $html[] = $this->getBreadcrumbTrailRenderer()->render($breadcrumbtrail);
        }

        $html[] = '</header>';

        $html[] = '<main class="container-xxl">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-12 clearfix">';
        $html[] = $this->renderPageTitle();

        $html[] = $this->getNotificationMessageManager()->renderMessages();

        return implode(PHP_EOL, $html);
    }

    public function getBaseHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->baseHeaderRenderer;
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function getBreadcrumbTrailRenderer(): BreadcrumbTrailRenderer
    {
        return $this->breadcrumbTrailRenderer;
    }

    public function getMenuRenderer(): MenuRenderer
    {
        return $this->menuRenderer;
    }

    public function getNotificationMessageManager(): NotificationMessageManager
    {
        return $this->notificationMessageManager;
    }

    protected function renderPageTitle(): string
    {
        $breadcrumbTrail = $this->getBreadcrumbTrail();

        if ($breadcrumbTrail->count() > 0) {
            $pageTitle = $breadcrumbTrail->last()->getName();

            return '<h3 title="' . htmlentities(strip_tags($pageTitle)) . '">' . $pageTitle . '</h3>';
        }

        return '';
    }
}