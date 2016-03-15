<?php
namespace Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;

use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class CoursePlatformGroupEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group :: PROPERTY_CODE));
         parent :: initialize_columns();

    }
}
