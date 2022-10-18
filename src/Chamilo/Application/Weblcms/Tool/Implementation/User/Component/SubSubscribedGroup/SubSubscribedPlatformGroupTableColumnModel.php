<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Translation\Translation;

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

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_DESCRIPTION));
        $this->addColumn(
            new StaticTableColumn(Translation::get(self::USERS, null, Manager::context())));
        $this->addColumn(new StaticTableColumn(Translation::get(self::SUBGROUPS)));
    }
}
