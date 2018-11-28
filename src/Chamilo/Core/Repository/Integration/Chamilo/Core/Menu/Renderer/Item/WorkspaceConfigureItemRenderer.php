<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceConfigureItemRenderer extends ItemRenderer
{

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $selected = $this->isSelected($item);

        if ($selected)
        {
            $class = 'class="chamilo-menu-item-current" ';
        }
        else
        {
            $class = '';
        }

        $urlRenderer = new Redirect(array(Application::PARAM_CONTEXT => Manager::context()));

        $html[] = '<a ' . $class . 'href="' . $urlRenderer->getUrl() . '">';

        $title = $this->getTranslator()->trans('ConfigureWorkspaces', [], 'Chamilo\Core\Repository\Workspace');

        if ($item->showIcon())
        {
            $imagePath = $this->getThemeUtilities()->getImagePath(
                'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu',
                'ConfigureWorkspaces' . ($selected ? 'Selected' : '')
            );

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '"><em>' . $title . '</em></div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    public function isSelected(Item $item)
    {
        $currentContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        return $currentContext == Manager::package();
    }
}