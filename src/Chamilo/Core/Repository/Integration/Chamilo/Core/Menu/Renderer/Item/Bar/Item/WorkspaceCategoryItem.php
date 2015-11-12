<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\CategoryItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Repository\Workspace\Manager;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItem extends CategoryItem
{

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application :: PARAM_CONTEXT);
        return ($currentContext == Manager :: package());
    }

    public function render()
    {
        $html = array();
        $sub_html = array();

        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $entityService = new EntityService();
        $workspaces = $workspaceService->getWorkspaceFavouritesByUser(
            $entityService,
            $this->getMenuRenderer()->get_user());

        $sub_html[] = '<ul>';

        if ($workspaces->size())
        {
            while ($workspace = $workspaces->next_result())
            {
                $workspaceItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem();
                $workspaceItem->setWorkspaceId($workspace->getId());
                $workspaceItem->setName($workspace->getName());
                $workspaceItem->set_parent($this->getItem()->get_id());
                $workspaceItem->set_display($this->getItem()->get_display());

                $sub_html[] = Renderer :: toHtml($this->getMenuRenderer(), $workspaceItem, $this);
            }
        }

        $configurationItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceConfigureItem();
        $configurationItem->set_parent($this->getItem()->get_id());
        $configurationItem->set_display($this->getItem()->get_display());

        $sub_html[] = Renderer :: toHtml($this->getMenuRenderer(), $configurationItem, $this);

        $sub_html[] = '</ul>';
        $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';

        $html[] = '<ul>';

        $selected = $this->isSelected();
        $class = $selected ? 'class="current" ' : '';

        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = '<a ' . $class . 'href="#">';

        $title = Translation :: get('Workspaces');

        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'WorkspaceCategory' . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="label' . ($this->getItem()->show_icon() ? ' label-with-image' : '') . '">' . $title .
                 '</div>';
        }

        $html[] = '<!--[if IE 7]><!--></a><!--<![endif]-->';
        $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
