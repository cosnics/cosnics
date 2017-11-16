<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

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
        
        $html[] = '<a' . ($selected ? ' class="chamilo-menu-item-current"' : '') . ' href="' . $this->get_url() . '">';
        
        $title = htmlentities(
            $this->getItem()->get_titles()->get_translation(Translation::getInstance()->getLanguageIsocode()));
        
        if ($this->getItem()->show_icon())
        {
            $itemNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($this->getItem()->get_type());
            $itemNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities::getInstance()->getClassnameFromNamespace($this->getItem()->get_type());
            $imagePath = Theme::getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));
            
            $html[] = '<img class="chamilo-menu-item-icon' .
                 ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title .
                 '" alt="' . $title . '" />';
        }
        
        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }
        
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    abstract public function get_url();
}
