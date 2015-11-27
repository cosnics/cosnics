<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar;

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

    public function isItemSelected()
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $html = array();

        $selected = $this->isSelected();

        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->get_url() . '">';

        $title = $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());

        if ($this->getItem()->show_icon())
        {
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                $this->getItem()->get_type());
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities :: getInstance()->getClassnameFromNamespace($this->getItem()->get_type());
            $imagePath = Theme :: getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="label' . ($this->getItem()->show_icon() ? ' label-with-image' : '') . '">' . $title .
                 '</div>';
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
