<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Core\Menu\Storage\DataClass\Item;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItem extends Bar
{

    /**
     *
     * @var boolean
     */
    private $isItemSelected;

    /**
     *
     * @var \Chamilo\Core\Menu\Renderer\Item\Renderer[]
     */
    private $itemRenderers = array();

    /**
     * @see \Chamilo\Core\Menu\Renderer\Item\Bar\Bar::isItemSelected()
     */
    public function isItemSelected()
    {
        if (! isset($this->isItemSelected))
        {
            $this->isItemSelected = false;

            foreach ($this->getItem()->get_children() as $child)
            {
                if ($this->getItemRenderer($this->getMenuRenderer(), $child, $this)->isItemSelected())
                {
                    $this->isItemSelected = true;
                    break;
                }
            }
        }

        return $this->isItemSelected;
    }

    public function render()
    {
        $html = array();

        $sub_html = array();
        $selected = $this->isSelected();

        if ($this->getItem()->has_children())
        {
            $sub_html[] = '<ul>';

            $entities = array();
            $entities[] = new UserEntity();
            $entities[] = new PlatformGroupEntity();

            foreach ($this->getItem()->get_children() as $child)
            {
                if (($child->get_id() && Rights :: get_instance()->is_allowed(
                    Rights :: VIEW_RIGHT,
                    __NAMESPACE__,
                    null,
                    $entities,
                    $child->get_id(),
                    Rights :: TYPE_ITEM)) || ! $child->get_id())
                {
                    if (! $child->is_hidden())
                    {
                        $sub_html[] = $this->getItemRenderer($this->getMenuRenderer(), $child, $this)->render();
                    }
                }
            }

            $sub_html[] = '</ul>';
            $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
        }

        $title = $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());

        $html[] = '<ul>';

        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = '<a ' . ($selected ? ' class="current"' : '') . 'href="#">';

        if ($this->getItem()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath(
                Manager :: context(),
                'Menu/Folder' . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="label' . ($this->getItem()->show_icon() ? ' label-with-image' : '') . '">' . $title .
                 '</div>';
        }

        $html[] = '<!--[if IE 7]><!--></a><!--<![endif]-->';
        $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Renderer\Menu\Renderer $menuRenderer
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @return \Chamilo\Core\Menu\Renderer\Item\Bar\Bar
     */
    public function getItemRenderer(\Chamilo\Core\Menu\Renderer\Menu\Renderer $menuRenderer, Item $item,
        \Chamilo\Core\Menu\Renderer\Item\Bar\Bar $parentRenderer)
    {
        if (! isset($this->itemRenderers[$item->get_id()]))
        {
            $this->itemRenderers[$item->get_id()] = Renderer :: factory($menuRenderer, $item, $parentRenderer);
        }

        return $this->itemRenderers[$item->get_id()];
    }
}
