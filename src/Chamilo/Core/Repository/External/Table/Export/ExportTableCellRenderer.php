<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\External\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExportTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($object)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Export', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_EXPORT_EXTERNAL_REPOSITORY,
                        Manager::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    public function renderCell(TableColumn $column, $object): string
    {
        switch ($column->get_name())
        {
            case ExportTableColumnModel::PROPERTY_TYPE :
                return $object->get_icon_image(IdentGlyph::SIZE_MINI);
            case ContentObject::PROPERTY_TITLE :
                return StringUtilities::getInstance()->truncate($object->get_title(), 50);
            case ContentObject::PROPERTY_DESCRIPTION :
                return htmlentities(
                    StringUtilities::getInstance()->truncate($object->get_description(), 50)
                );
        }

        return parent::renderCell($column, $object);
    }
}
