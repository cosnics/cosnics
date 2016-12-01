<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * * *************************************************************************** Table column model for a direct
 * subscribed course user browser table, or users in a direct subscribed group.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class PlatformGroupRelUserTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID));
    }
}
