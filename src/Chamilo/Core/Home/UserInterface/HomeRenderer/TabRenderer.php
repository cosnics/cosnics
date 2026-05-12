<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabRenderer
{
    public function __construct(protected HomeService $homeService, protected ColumnRenderer $columnRenderer)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(
        Element $tab, int $tabKey, ?int $currentTabIdentifier = null, ?User $user = null
    ): string
    {
        $isActiveTab = $this->homeService->isActiveTab($tabKey, $tab, $currentTabIdentifier);

        $html = [];

        $html[] =
            '<div class="row portal-tab ' . ($isActiveTab ? 'show' : 'hidden') . '" data-element-id="' . $tab->getId() .
            '">';

        $columns = $this->homeService->findElementsByTypeAndParentIdentifier(
            Element::TYPE_COLUMN, $tab->getId()
        );

        foreach ($columns as $column) {
            $html[] = $this->columnRenderer->render($column, $user);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}