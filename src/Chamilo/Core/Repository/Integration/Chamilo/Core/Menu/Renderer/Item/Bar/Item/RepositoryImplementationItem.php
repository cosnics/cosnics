<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
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
            $class = 'class="current" ';
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
        $title = $this->getItem()->get_name();

        if ($this->getItem()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath($this->getItem()->get_implementation(), 'Menu');

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