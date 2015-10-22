<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\CategoryItem;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationCategoryItem extends CategoryItem
{

    public function render()
    {
        $html = array();
        $sub_html = array();
        $instances = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
            Instance :: class_name(),
            new DataClassRetrievesParameters());

        if ($instances->size())
        {
            $sub_html[] = '<ul>';

            while ($instance = $instances->next_result())
            {

                $instanceItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationItem();
                $instanceItem->set_implementation($instance->get_implementation());
                $instanceItem->set_instance_id($instance->get_id());
                $instanceItem->set_name($instance->get_title());
                $instanceItem->set_parent($this->get_item()->get_id());
                $instanceItem->set_display();

                $sub_html[] = Renderer :: as_html($this->get_menu_renderer(), $instanceItem);
            }

            $sub_html[] = '</ul>';
            $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
        }

        $html[] = '<ul>';

        $selected = $this->get_item()->is_selected();
        $class = $selected ? 'class="current" ' : '';

        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = '<a ' . $class . 'href="#">';

        $title = Translation :: get('Instance');

        if ($this->get_item()->show_icon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'RepositoryImplementationCategory' . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = '<div class="label' . ($this->get_item()->show_icon() ? ' label-with-image' : '') . '">' . $title .
                 '</div>';
        }

        $html[] = '<!--[if IE 7]><!--></a><!--<![endif]-->';
        $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
