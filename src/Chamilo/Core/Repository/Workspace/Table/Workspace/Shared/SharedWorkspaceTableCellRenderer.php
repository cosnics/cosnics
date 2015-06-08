<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Shared;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableCellRenderer;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedWorkspaceTableCellRenderer extends WorkspaceTableCellRenderer
{

    public function get_actions($workspace)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Favourite', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath(Manager :: context(), 'Action/Favourite'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_FAVOURITE,
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager :: ACTION_CREATE,
                        Manager :: PARAM_WORKSPACE_ID => $workspace->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        return $toolbar->as_html();
    }
}
