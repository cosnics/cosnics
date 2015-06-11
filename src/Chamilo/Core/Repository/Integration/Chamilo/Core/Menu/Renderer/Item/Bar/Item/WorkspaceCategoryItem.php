<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\CategoryItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItem extends CategoryItem
{

    public function render()
    {
        $html = array();
        $sub_html = array();

        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $entityService = new EntityService();
        $workspaces = $workspaceService->getWorkspaceFavouritesByUser(
            $entityService,
            $this->get_menu_renderer()->get_user());

        $sub_html[] = '<ul>';

        if ($workspaces->size())
        {
            while ($workspace = $workspaces->next_result())
            {
                $workspaceItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem();
                $workspaceItem->setWorkspaceId($workspace->getId());
                $workspaceItem->setName($workspace->getName());
                $workspaceItem->set_parent($this->get_item()->get_id());
                $workspaceItem->set_display($this->get_item()->get_display());

                $sub_html[] = Renderer :: as_html($this->get_menu_renderer(), $workspaceItem);
            }
        }

        $configurationItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceConfigureItem();
        $configurationItem->set_parent($this->get_item()->get_id());
        $configurationItem->set_display($this->get_item()->get_display());

        $sub_html[] = Renderer :: as_html($this->get_menu_renderer(), $configurationItem);

        $sub_html[] = '</ul>';
        $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';

        $html[] = '<ul>';

        $html[] = '<li>';
        $html[] = '<a href="#">';
        $selected = $this->get_item()->is_selected();
        $title = Translation :: get('Workspaces');

        if ($this->get_item()->show_icon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'WorkspaceCategory' . ($selected ? 'Selected' : ''));

            $html[] = '<img class="item-icon" src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '<!--[if IE 7]><!--></a><!--<![endif]-->';
        $html[] = '<!--[if lte IE 6]><table><tr><td><![endif]-->';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
