<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * A table which represents all users which have portfolios published
 * 
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTable extends DataClassTable
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Portfolio\Manager :: PARAM_USER_ID;
}