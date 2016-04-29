<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableCellRenderer extends DataClassTableCellRenderer
{

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param DataClass $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case ActivityTableColumnModel :: PROPERTY_TYPE_ICON :
                return Theme :: getInstance()->getImage(
                    'Type/' . $result->get_type(),
                    'png',
                    null,
                    null,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    'Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\\');
                break;
            case Activity :: PROPERTY_TYPE :
                return $result->get_type_string();
                break;
            case Activity :: PROPERTY_DATE :
                $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
                return DatetimeUtilities :: format_locale_date($date_format, $result->get_date());
                break;
            case ActivityTableColumnModel :: PROPERTY_USER :
                return $result->get_user()->get_fullname();
                break;
        }

        return parent :: render_cell($column, $result);
    }
}