<?php
namespace Chamilo\Core\Repository\Share\Table\Group;

use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * Table to display the content object share rights.
 * 
 * @author Pieterjan Broekaert
 */
class GroupRightsTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_TARGET_GROUPS;
}
