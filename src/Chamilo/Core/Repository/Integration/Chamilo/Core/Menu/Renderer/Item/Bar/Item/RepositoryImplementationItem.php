<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationItem extends Bar
{

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application :: PARAM_CONTEXT);
        $currentInstance = $this->getMenuRenderer()->getRequest()->get(
            \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY);
        return ($currentContext == $this->getItem()->get_implementation() &&
             $currentInstance == $this->getItem()->get_instance_id());
    }

    public function getContent()
    {
        $selected = $this->isSelected();

        if ($selected)
        {
            $class = 'class="chamilo-menu-item-current" ';
        }
        else
        {
            $class = '';
        }

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => $this->getItem()->get_implementation(),
                \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY => $this->getItem()->get_instance_id()));

        $html[] = '<a ' . $class . 'href="' . $redirect->getUrl() . '">';
        $title = htmlentities($this->getItem()->get_name());

        if ($this->getItem()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath($this->getItem()->get_implementation(), 'Menu');

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title . '" alt="' .
                 $title . '" />';
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
}