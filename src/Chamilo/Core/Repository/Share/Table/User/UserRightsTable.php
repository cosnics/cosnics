<?php
namespace Chamilo\Core\Repository\Share\Table\User;

use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * Table to display the content object share rights.
 * 
 * @author Pieterjan Broekaert
 */
class UserRightsTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_TARGET_USERS;
}
