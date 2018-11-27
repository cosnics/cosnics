<?php
namespace Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;

/**
 * @package Chamilo\Core\Menu\Renderer\NavigationBarRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkItemRenderer extends NavigationBarItemRenderer
{

    /**
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(ItemService $itemService, ClassnameUtilities $classnameUtilities, Theme $themeUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): void
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\LinkItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $classnameUtilities = $this->getClassnameUtilities();

        $itemNamespace = $classnameUtilities->getNamespaceFromClassname($item->getType());
        $itemNamespace = $classnameUtilities->getNamespaceParent($itemNamespace, 2);
        $itemType = $classnameUtilities->getClassnameFromNamespace($item->getType());
        $imagePath = $this->getThemeUtilities()->getImagePath($itemNamespace, $itemType);

        $title = $this->getItemService()->getItemTitleForCurrentLanguage($item);

        $html = array();

        $html[] = '<li>';
        $html[] = '<a href="' . $item->getUrl() . '" target="' . $item->getTargetString() . '">';

        if ($item->showIcon())
        {
            if (!empty($item->getIconClass()))
            {
                $html[] = $this->renderCssIcon($item);
            }
            else
            {
                $html[] = '<img class="chamilo-menu-item-icon' .
                    ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                        " src="' . $imagePath . '" alt="' . $title . '" />';
            }
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}