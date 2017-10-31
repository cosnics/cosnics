<?php
namespace Chamilo\Libraries\Format\DataTable\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
interface DataTableProviderInterface
{

    /**
     *
     * @return string[][]
     */
    public function getDataTableRowData();

    /**
     *
     * @return integer
     */
    public function getDataTableRowCount();
}

