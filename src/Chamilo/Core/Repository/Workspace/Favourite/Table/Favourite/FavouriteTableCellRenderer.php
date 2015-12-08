<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableCellRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Workspace\Favourite\Manager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteTableCellRenderer extends WorkspaceTableCellRenderer
{

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableCellRenderer::get_actions()
     */
    public function get_actions($workspace)
    {
        $toolbar = $this->getToolbar($workspace);

        $unfavouriteItem = new ToolbarItem(
            Translation :: get('Unfavourite', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Delete'),
            $this->get_component()->get_url(
                array(
                    Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                    \Chamilo\Core\Repository\Workspace\Manager :: PARAM_WORKSPACE_ID => $workspace->get_id())),
            ToolbarItem :: DISPLAY_ICON,
            true);

        $toolbar->replace_item($unfavouriteItem, 0);

        return $toolbar->as_html();
    }
}
