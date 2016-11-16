<?php
namespace Chamilo\Application\Survey\Table\Share;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ShareTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case Publication::PROPERTY_PUBLISHER_ID :
                return $publication->getCreator()->get_fullname();
            case Publication::PROPERTY_PUBLISHED :
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
                    $publication->getCreationDate());
        }
        
        return parent::render_cell($column, $publication);
    }

    public function get_actions($publication)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Share', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Share'), 
                $this->get_component()->get_url(
                    array(Manager::PARAM_SELECTED_PUBLICATION_ID => $publication->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Details', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Details'), 
                null, 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
