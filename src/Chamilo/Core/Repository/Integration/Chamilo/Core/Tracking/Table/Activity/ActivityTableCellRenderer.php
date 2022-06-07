<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param Activity $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case ActivityTableColumnModel::PROPERTY_TYPE_ICON :
                return Activity::type_image($result->getType());
                break;
            case Activity::PROPERTY_TYPE :
                return $result->get_type_string();
                break;
            case Activity::PROPERTY_DATE :
                $date_format = Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES);

                return DatetimeUtilities::getInstance()->formatLocaleDate($date_format, $result->get_date());
                break;
            case ActivityTableColumnModel::PROPERTY_USER :
                return $result->get_user()->get_fullname();
                break;
        }

        return parent::render_cell($column, $result);
    }
}