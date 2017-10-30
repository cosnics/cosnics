<?php
namespace Chamilo\Libraries\Format\DataTable\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DataTableProviderInterface
{

    /**
     *
     * @return string[][]
     */
    public function getTableRowData();

    /**
     *
     * @return integer
     */
    public function getTableRowCount();
}

