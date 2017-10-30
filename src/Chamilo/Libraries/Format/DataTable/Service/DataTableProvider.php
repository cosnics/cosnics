<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class DataTableProvider implements DataTableProviderInterface
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    private $dataClassRetrievesParameters;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     */
    public function __construct(DataClassRetrievesParameters $dataClassRetrievesParameters)
    {
        $this->dataClassRetrievesParameters = $dataClassRetrievesParameters;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function getDataClassRetrievesParameters()
    {
        return $this->dataClassRetrievesParameters;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     */
    public function setDataClassRetrievesParameters(DataClassRetrievesParameters $dataClassRetrievesParameters)
    {
        $this->dataClassRetrievesParameters = $dataClassRetrievesParameters;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface::getTableRowData()
     */
    abstract public function getTableRowData();

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface::getTableRowCount()
     */
    abstract public function getTableRowCount();
}

