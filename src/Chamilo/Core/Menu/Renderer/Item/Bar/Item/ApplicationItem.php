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

        if ($this->isSelected())
        {
            $class = 'class="current" ';
        }
        else
        {
            $class = '';
        }

        $html = array();

        if ($this->getItem()->get_use_translation())
        {
            $title = Translation :: get('TypeName', null, $this->getItem()->get_application());
        }
        else
        {
            $title = $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());
        }

        $html[] = '<a ' . $class . 'href="' . $url . '">';

        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = $this->getItem()->get_application() . '\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'Menu' . ($this->isSelected() ? 'Selected' : ''));

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
}
