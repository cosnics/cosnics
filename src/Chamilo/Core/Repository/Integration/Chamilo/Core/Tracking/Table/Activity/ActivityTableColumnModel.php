<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table column model for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableColumnModel extends DataClassTableColumnModel
{
    const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    const DEFAULT_ORDER_COLUMN_INDEX = 4;

    const PROPERTY_TYPE_ICON = 'type_icon';
    const PROPERTY_USER = 'user';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $glyph = new FontAwesomeGlyph('mouse', [], Translation::get('ActivityType'), 'fas');

        $this->add_column(new StaticTableColumn(self::PROPERTY_TYPE_ICON, $glyph->render()));
        $this->add_column(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_TYPE));
        $this->add_column(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_CONTENT));
        $this->add_column(new StaticTableColumn(self::PROPERTY_USER, Translation::get('User')));
        $this->add_column(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_DATE));
    }
}