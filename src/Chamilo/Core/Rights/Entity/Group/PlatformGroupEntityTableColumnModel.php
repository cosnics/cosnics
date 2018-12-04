<?php
namespace Chamilo\Core\Rights\Entity\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Cell renderer for the platform group entity browser
 * 
 * @author Sven Vanpoucke
 * @deprecated Should not be needed anymore
 */
class PlatformGroupEntityTableColumnModel extends LocationEntityTableColumnModel
{
    const COLUMN_USERS = 'users';
    const COLUMN_SUBGROUPS = 'subgroups';

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_DESCRIPTION));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_CODE));
        $this->add_column(new StaticTableColumn(self::COLUMN_USERS));
        $this->add_column(new StaticTableColumn(self::COLUMN_SUBGROUPS));
        parent::initialize_columns();
    }
}
