<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class DataTableColumnModelFactory
{

    /**
     *
     * @param string $dataTableContext
     * @param string $dataTableType
     * @return \Chamilo\Libraries\Format\DataTable\DataTableColumnModel
     */
    public function getDataTableColumnModel($dataTableContext, $dataTableType)
    {
        $className = $dataTableContext . '\Ajax\DataTable\Type\\' . $dataTableType . '\\' . $dataTableType .
             'DataTableColumnModel';
        return new $className();
    }
}

