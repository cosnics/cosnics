<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceItemRenderer extends ItemRenderer
{
    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $item->getName();
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $selected = $this->isSelected($item);

        if ($selected)
        {
            $class = 'class="active" ';
        }
        else
        {
            $class = '';
        }

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::package(), Manager::PARAM_WORKSPACE_ID => $item->getWorkspaceId()
            )
        );

        $html = array();

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a ' . $class . 'href="' . $redirect->getUrl() . '">';
        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $imagePath = $this->getThemeUtilities()->getImagePath(Manager::package(), 'Logo/48');

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     *
     * @return boolean
     */
    public function isSelected(Item $item)
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentWorkspace = $request->query->get(Manager::PARAM_WORKSPACE_ID);

        return $currentContext == Manager::package() && $currentWorkspace == $item->getWorkspaceId();
    }
}