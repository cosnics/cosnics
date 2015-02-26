<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItem extends Bar
{

    public function get_item_url()
    {
        $application = $this->get_item()->get_application();

        if (! Application :: is_active($application))
        {
            return;
        }

        if ($application == 'root')
        {
            $url = 'index.php';
        }
        else
        {
            $url = 'index.php?application=' . $this->get_item()->get_application();
        }

        $selected = $this->get_item()->is_selected();

        if ($selected && $this->get_item()->get_parent() == 0)
        {
            $class = 'class="current" ';
        }
        else
        {
            $class = '';
        }

        $html = array();
        if ($this->get_item()->get_use_translation())
        {
            $title = Translation :: get('TypeName', null, $this->get_item()->get_application());
        }
        else
        {
            $title = $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language());
        }

        $html[] = '<a ' . $class . 'href="' . $url . '">';

        if ($this->get_item()->show_icon())
        {
            $integrationNamespace = $this->get_item()->get_application() . '\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath($integrationNamespace) . 'menu' .
                 ($selected ? '_selected' : '') . '.png';

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '</a>';

        return implode("\n", $html);
    }
}
