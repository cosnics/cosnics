<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItem extends Renderer
{

    public function render()
    {
        $html = array();
        $current_section = Header :: get_instance()->get_section();

        $sub_html = array();
        $selected = $this->get_item()->is_selected();

        if ($this->get_item()->has_children())
        {
            $sub_html[] = '<ul>';

            $entities = array();
            $entities[] = new UserEntity();
            $entities[] = new PlatformGroupEntity();

            foreach ($this->get_item()->get_children() as $child)
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
                        $sub_html[] = Renderer :: as_html($this->get_menu_renderer(), $child);
                    }
                }
            }

            $sub_html[] = '</ul>';
            $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
        }

        $title = $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language());

        $html[] = '<ul>';

        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = '<a ' . ($selected ? ' class="current"' : '') . 'href="#">';

        if ($this->get_item()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath(Manager :: context()) . 'Menu/folder' .
                 ($selected ? '_selected' : '') . '.png';

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '<!--[if IE 7]><!--></a><!--<![endif]-->';
        $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
