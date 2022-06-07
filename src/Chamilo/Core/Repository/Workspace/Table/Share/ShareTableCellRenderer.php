<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class ShareTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($workspace)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Share', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                $this->get_component()->get_url(array(Manager::PARAM_SELECTED_WORKSPACE_ID => $workspace->get_id())),
                ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    public function render_cell($column, $workspace)
    {
        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID :
                return $workspace->getCreator()->get_fullname();
            case Workspace::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES),
                    $workspace->getCreationDate()
                );
        }

        return parent::render_cell($column, $workspace);
    }
}
