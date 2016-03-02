<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Platform\Translation;

/**
 * * *************************************************************************** Table column model for a course
 * subgroup browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class SubSubscribedPlatformGroupTableColumnModel extends DataClassTableColumnModel
{
    const USERS = 'Users';
    const SUBGROUPS = 'Subgroups';

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new StaticTableColumn(Translation :: get(self :: USERS, null, \Chamilo\Core\User\Manager :: context())));
        $this->add_column(new StaticTableColumn(Translation :: get(self :: SUBGROUPS)));
    }
}
