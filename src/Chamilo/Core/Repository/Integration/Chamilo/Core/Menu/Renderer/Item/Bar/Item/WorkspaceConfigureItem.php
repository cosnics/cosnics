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

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application :: PARAM_CONTEXT);
        return $currentContext == Manager :: package();
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

        $urlRenderer = new Redirect(array(Application :: PARAM_CONTEXT => Manager :: context()));

        $html[] = '<a ' . $class . 'href="' . $urlRenderer->getUrl() . '">';
        $title = Translation :: get('ConfigureWorkspaces', array(), 'Chamilo\Core\Repository\Workspace');

        if ($this->getItem()->show_icon())
        {
            $imagePath = Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu',
                'ConfigureWorkspaces' . ($this->isSelected() ? 'Selected' : ''));

            $html[] = '<img class="chamilo-menu-item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' .
                 $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '"><em>' . $title .
                 '</em></div>';
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}