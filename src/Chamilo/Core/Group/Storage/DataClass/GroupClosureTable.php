<?php

namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupClosureTable extends ClosureTable
{
    public static function get_table_name()
    {
        return 'group_closure_table';
    }
}