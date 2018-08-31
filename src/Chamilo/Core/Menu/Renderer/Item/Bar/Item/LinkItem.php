<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkItem extends Bar
{

    public function isItemSelected()
    {
        return false;
    }

	public function getContent()
	{
		$html = array();

		$html[] = '<a href="' . $this->getItem()->get_url() . '" target="' . $this->getItem()->get_target_string() . '">';

		$title = $this->getItem()->get_titles()->get_translation( Translation::getInstance()->getLanguageIsocode() );

		$itemNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname( $this->getItem()->get_type() );
		$itemNamespace = ClassnameUtilities::getInstance()->getNamespaceParent( $itemNamespace, 2 );
		$itemType      = ClassnameUtilities::getInstance()->getClassnameFromNamespace( $this->getItem()->get_type() );
		$imagePath     = Theme::getInstance()->getImagePath( $itemNamespace, $itemType );

        if ($this->getItem()->show_icon())
        {
            if(!empty($this->getItem()->getIconClass()))
            {
                $html[]= $this->renderCssIcon();
            }
            else
            {
                $html[] = '<img class="chamilo-menu-item-icon' .
                    ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '
                        " src="' . $imagePath . '" alt="' . $title . '" />';
            }
        }

        if($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' .
                $title . '</div>';
        }

		$html[] = '<div class="clearfix"></div>';

		$html[] = '</a>';

		return implode( PHP_EOL, $html );
	}
}
