<?php

namespace Chamilo\Libraries\Format\DataTable;

use Chamilo\Libraries\Format\DataAction\DataActions;
use Chamilo\Libraries\Format\DataAction\DataActionsProvider;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Format\DataTable
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataTableActionsProvider extends DataActionsProvider
{
    /**
     * @param string|int $identifier
     *
     * @return \Chamilo\Libraries\Format\DataAction\DataActions
     */
    public function getDataActions($identifier)
    {

    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return \Chamilo\Libraries\Format\DataAction\DataActions
     */
    public function getDataClassActions(DataClass $dataClass)
    {
        return new DataActions();
    }
}