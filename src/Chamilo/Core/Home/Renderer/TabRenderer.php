<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabRenderer
{

    protected ColumnRenderer $columnRenderer;

    protected HomeService $homeService;

    public function __construct(HomeService $homeService, ColumnRenderer $columnRenderer)
    {
        $this->homeService = $homeService;
        $this->columnRenderer = $columnRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function render(
        Element $tab, int $tabKey, ?int $currentTabIdentifier = null, bool $isGeneralMode = false, ?User $user = null
    ): string
    {
        $columnRenderer = $this->getColumnRenderer();
        $isActiveTab = $this->getHomeService()->isActiveTab($tabKey, $tab, $currentTabIdentifier);

        $html = [];

        $html[] =
            '<div class="row portal-tab ' . ($isActiveTab ? 'show' : 'hidden') . '" data-element-id="' . $tab->getId() .
            '">';

        $columns = $this->getHomeService()->findElementsByTypeUserAndParentIdentifier(
            Element::TYPE_COLUMN, $user, $tab->getId()
        );

        foreach ($columns as $column)
        {
            $html[] = $columnRenderer->render($column, $isGeneralMode, $user);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getColumnRenderer(): ColumnRenderer
    {
        return $this->columnRenderer;
    }

    public function getHomeService(): HomeService
    {
        return $this->homeService;
    }
}