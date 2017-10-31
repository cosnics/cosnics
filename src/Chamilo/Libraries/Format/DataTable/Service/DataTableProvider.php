<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
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
     * @return \Chamilo\Core\Repository\Ajax\Tables\ContentObjectDataTableColumnModel
     */
    abstract public function getDataTableColumnModel();

    /**
     *
     * @return \Chamilo\Core\Repository\Ajax\Tables\ContentObjectDataTableCellRenderer
     */
    abstract public function getDataTableCellRenderer();

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return string[]
     */
    public function handleDataClass(DataClass $dataClass)
    {
        $rowData = array();

        foreach ($this->getDataTableColumnModel()->getColumns() as $column)
        {
            $rowData[$column->getName()] = $this->getDataTableCellRenderer()->renderCell($column, $dataClass);
        }

        return $rowData;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    abstract public function getDataTableDataClasses();

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface::getDataTableRowData()
     */
    public function getDataTableRowData()
    {
        $dataTableRowData = array();

        foreach ($this->getDataTableDataClasses() as $dataClass)
        {
            $dataTableRowData[] = $this->handleDataClass($dataClass);
        }

        return $dataTableRowData;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Interfaces\DataTableProviderInterface::getTableRowCount()
     */
    abstract public function getDataTableRowCount();
}

