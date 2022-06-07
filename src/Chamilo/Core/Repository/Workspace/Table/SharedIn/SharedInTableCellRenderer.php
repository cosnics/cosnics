<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Table\Share\ShareTableCellRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class SharedInTableCellRenderer extends ShareTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($workspace)
    {
        $toolbar = new Toolbar();

        $contentObjectId = $this->get_component()->get_parameter(Manager::PARAM_CONTENT_OBJECT_ID);

        $parameters = array(
            Manager::PARAM_CONTEXT => Manager::context(), Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE,
            \Chamilo\Core\Repository\Workspace\Manager::PARAM_SELECTED_WORKSPACE_ID => $workspace->getId(),
            \Chamilo\Core\Repository\Workspace\Manager::PARAM_BROWSER_SOURCE => Manager::ACTION_VIEW_CONTENT_OBJECTS,
            Manager::PARAM_CONTENT_OBJECT_ID => $contentObjectId
        );

        $redirect = new Redirect($parameters);
        $url = $redirect->getUrl();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Unshare', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('unlock'), $url,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
