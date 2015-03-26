<?php
namespace Chamilo\Core\Repository\Viewer\Table\Import;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImportTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return $object->get_icon_image(Theme :: ICON_MINI);
            case ContentObject :: PROPERTY_TITLE :
                return StringUtilities :: getInstance()->truncate($object->get_title(), 50);
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(StringUtilities :: getInstance()->truncate($object->get_description(), 50));
        }
        return parent :: render_cell($column, $object);
    }

    public function get_actions($object)
    {
        return $this->get_component()->get_default_browser_actions($object)->as_html();
    }
}
