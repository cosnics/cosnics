<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabHeaderRenderer
{
    protected HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function render(Element $tab, int $tabKey, ?int $currentTabIdentifier = null): string
    {
        $isActiveTab = $this->getHomeService()->isActiveTab($tabKey, $tab, $currentTabIdentifier);

        $html = [];

        $listItem = [];

        $listItem[] = '<li';

        if ($isActiveTab)
        {
            $listItem[] = 'class="portal-nav-tab active"';
        }
        else
        {
            $listItem[] = 'class="portal-nav-tab"';
        }

        $listItem[] = ' data-tab-id="' . $tab->getId() . '"';
        $listItem[] = ' data-tab-title="' . $tab->getTitle() . '"';
        $listItem[] = '>';

        $html[] = implode(' ', $listItem);

        $html[] = '<a class="portal-action-tab-title" href="#">';
        $html[] = '<span class="portal-nav-tab-title">' . htmlspecialchars($tab->getTitle()) . '</span>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getHomeService(): HomeService
    {
        return $this->homeService;
    }
}