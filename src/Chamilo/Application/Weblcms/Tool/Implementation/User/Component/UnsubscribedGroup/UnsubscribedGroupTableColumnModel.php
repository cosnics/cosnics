<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * * *************************************************************************** Table column model for an unsubscribed
 * course user browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedGroupTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{
    const USERS = 'Users';
    const SUBGROUPS = 'Subgroups';

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_DESCRIPTION));
        $this->add_column(
            new StaticTableColumn(Translation::get(self::USERS, null, \Chamilo\Core\User\Manager::context())));
        $this->add_column(new StaticTableColumn(Translation::get(self::SUBGROUPS)));
    }
}
