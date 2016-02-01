<?php
namespace Chamilo\Core\Menu\Renderer\Item\BootstrapBar\Item;

use Chamilo\Core\Menu\Renderer\Item\BootstrapBar\BootstrapBar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItem extends BootstrapBar
{

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application :: PARAM_CONTEXT);
        return ($currentContext == $this->getItem()->get_application());
    }

    public function getContent()
    {
        $application = $this->getItem()->get_application();

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
            $url = 'index.php?application=' . $this->getItem()->get_application();
        }

//        if ($this->isSelected())
//        {
//            $class = 'class="chamilo-menu-item-current" ';
//        }
//        else
//        {
//            $class = '';
//        }

        $html = array();

        if ($this->getItem()->get_use_translation())
        {
            $title = Translation :: get('TypeName', null, $this->getItem()->get_application());
        }
        else
        {
            $title = $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());
        }

        $html[] = '<a href="' . $url . '">';

        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = $this->getItem()->get_application() . '\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'Menu' . ($this->isSelected() ? 'Selected' : ''));

            $html[] = '<img class="chamilo-menu-item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' .
                 $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
