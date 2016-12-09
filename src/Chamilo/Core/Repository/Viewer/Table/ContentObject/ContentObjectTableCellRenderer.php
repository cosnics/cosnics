<?php
namespace Chamilo\Core\Repository\Viewer\Table\ContentObject;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class is a cell renderer for a publication candidate table
 */
class ContentObjectTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                return $content_object->get_icon_image(Theme::ICON_MINI);
            case ContentObject::PROPERTY_TITLE :
                return StringUtilities::getInstance()->truncate($content_object->get_title(), 50);
            case ContentObject::PROPERTY_DESCRIPTION :
                return StringUtilities::getInstance()->truncate($content_object->get_description(), 50);
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities::format_locale_date(null, $content_object->get_modification_date());
        }

        return parent::render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        return $this->get_component()->get_default_browser_actions($content_object)->as_html();
    }
}
