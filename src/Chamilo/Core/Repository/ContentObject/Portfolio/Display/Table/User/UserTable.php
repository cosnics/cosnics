<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\User;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;

/**
 * Table representing a set of users which can be emulated
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_VIRTUAL_USER_ID;
}