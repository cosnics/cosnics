<?php
namespace Chamilo\Core\Repository\Instance\Table\Instance;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

/**
 * Table for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstanceTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_INSTANCE_ID;
}