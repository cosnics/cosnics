<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageManager;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DefaultHeaderRenderer
{
    protected BaseHeaderRenderer $baseHeaderRenderer;

    protected BreadcrumbTrail $breadcrumbTrail;

    protected NotificationMessageManager $notificationMessageManager;

    private BannerRenderer $bannerRenderer;

    public function __construct(
        BaseHeaderRenderer $baseHeaderRenderer, BreadcrumbTrail $breadcrumbTrail,
        NotificationMessageManager $notificationMessageManager, BannerRenderer $bannerRenderer
    )
    {
        $this->baseHeaderRenderer = $baseHeaderRenderer;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->notificationMessageManager = $notificationMessageManager;
        $this->bannerRenderer = $bannerRenderer;
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $html[] = $this->getBaseHeaderRenderer()->renderHeader();
        $html[] = $this->getBannerRenderer()->render($user);

        $html[] = '<main class="container-xxl">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-12 clearfix">';
        $html[] = $this->renderPageTitle();

        $html[] = $this->getNotificationMessageManager()->renderMessages();

        return implode(PHP_EOL, $html);
    }

    public function getBannerRenderer(): BannerRenderer
    {
        return $this->bannerRenderer;
    }

    public function getBaseHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->baseHeaderRenderer;
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
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