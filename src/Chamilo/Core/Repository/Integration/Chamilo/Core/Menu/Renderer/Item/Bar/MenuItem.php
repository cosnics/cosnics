<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class MenuItem extends Bar
{

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $html = array();

        $selected = $this->get_item()->is_selected() ||
             ($this->get_item()->get_parent() != 0 && $this->get_item()->get_parent_object()->is_selected());

        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->get_url() . '">';

        $title = $this->get_item()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());

        if ($this->get_item()->show_icon())
        {
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                $this->get_item()->get_type());
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities :: getInstance()->getClassnameFromNamespace($this->get_item()->get_type());
            $imagePath = Theme :: getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    abstract public function get_url();
}
