<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataTableColumnModelFactory
{

    /**
     *
     * @param string $context
     * @param string $type
     * @return \Chamilo\Libraries\Format\DataTable\DataTableColumnModel
     */
    public function getDataTableColumnModel($context, $type)
    {
        $className = $context . '\\' . $type . 'DataTableColumnModel';
        return new $className();
    }
}

