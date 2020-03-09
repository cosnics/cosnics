<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableCellRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
            Translation::get('Unfavourite', null, Utilities::COMMON_LIBRARIES),
            new FontAwesomeGlyph('times', array(), null, 'fas'), $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                \Chamilo\Core\Repository\Workspace\Manager::PARAM_WORKSPACE_ID => $workspace->get_id()
            )
        ), ToolbarItem::DISPLAY_ICON, true
        );

        $toolbar->replace_item($unfavouriteItem, 0);

        return $toolbar->as_html();
    }
}
