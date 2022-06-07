<?php
namespace Chamilo\Core\Help\Table\Item;

use Chamilo\Core\Help\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class HelpItemTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($help_item)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_component()->get_url(
                    array(
                        Application::PARAM_ACTION => Manager::ACTION_UPDATE_HELP_ITEM,
                        Manager::PARAM_HELP_ITEM => $help_item->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }
}
