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

    public function getContent()
    {
        $selected = $this->get_item()->is_selected();

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
                Application :: PARAM_CONTEXT => $this->get_item()->get_implementation(),
                \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY => $this->get_item()->get_instance_id()));

        $html[] = '<a ' . $class . 'href="' . $redirect->getUrl() . '">';
        $title = $this->get_item()->get_name();

        if ($this->get_item()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath($this->get_item()->get_implementation(), 'Logo/16');

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}