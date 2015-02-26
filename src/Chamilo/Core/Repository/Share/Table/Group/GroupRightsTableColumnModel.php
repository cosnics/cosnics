<?php
namespace Chamilo\Core\Repository\Share\Table\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Share\Table\ShareRightColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table column model for the content object share rights browser table
 * 
 * @author Pieterjan Broekaert
 */
class GroupRightsTableColumnModel extends DataClassTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_NAME));
        $this->add_rights_columns();
    }

    /**
     * adds a column for every right available to a share
     */
    private function add_rights_columns()
    {
        $rights = RepositoryRights :: get_share_rights();
        $rights[] = RepositoryRights :: get_copy_right();
        
        foreach ($rights as $right_id => $right_name)
        {
            $column_name = (string) StringUtilities :: getInstance()->createString(strtolower($right_name))->upperCamelize();
            $column = new ShareRightColumn($column_name, $right_id);
            $this->add_column($column);
        }
    }
}
