<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkApplicationItemRenderer extends ItemRenderer
{

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\LinkApplicationItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $html = array();

        $html[] = '<li>';
        $html[] = '<a href="' . $item->getUrl() . '" target="' . $item->getTargetString() . '">';

        $html[] =
            '<div class="chamilo-menu-item-label">' . $this->getItemService()->getItemTitleForCurrentLanguage($item) .
            '</div>';

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}