<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceConfigureItem extends Bar
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

        $urlRenderer = new Redirect(array(Application :: PARAM_CONTEXT => Manager :: context()));

        $html[] = '<a ' . $class . 'href="' . $urlRenderer->getUrl() . '">';
        $title = Translation :: get('ConfigureWorkspaces');

        if ($this->get_item()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu',
                'ConfigureWorkspaces');

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = '<div class="label' . ($this->get_item()->show_icon() ? ' label-with-image' : '') . '"><em>' .
                 $title . '</em></div>';
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}