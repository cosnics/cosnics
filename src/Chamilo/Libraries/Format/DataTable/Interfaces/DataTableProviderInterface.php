<?php
namespace Chamilo\Libraries\Format\DataTable\Interfaces;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @return string[][]
     */
    public function getDataTableRowData(DataClassRetrievesParameters $dataClassRetrievesParameters);

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @return integer
     */
    public function getDataTableRowCount(DataClassRetrievesParameters $dataClassRetrievesParameters);
}

