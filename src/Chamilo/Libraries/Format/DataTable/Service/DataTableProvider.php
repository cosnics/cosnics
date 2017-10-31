<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Format\DataTable\DataTableCellRenderer;
use Chamilo\Libraries\Format\DataTable\DataTableColumnModel;
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
     * @var \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer
     */
    private $dataTableCellRenderer;

    /**
     *
     * @var \Chamilo\Libraries\Format\DataTable\DataTableColumnModel
     */
    private $dataTableColumnModel;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @param \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer $dataTableCellRenderer
     * @param \Chamilo\Libraries\Format\DataTable\DataTableColumnModel $dataTableColumnModel
     */
    public function __construct(DataClassRetrievesParameters $dataClassRetrievesParameters,
        DataTableCellRenderer $dataTableCellRenderer, DataTableColumnModel $dataTableColumnModel)
    {
        $this->dataClassRetrievesParameters = $dataClassRetrievesParameters;
        $this->dataTableCellRenderer = $dataTableCellRenderer;
        $this->dataTableColumnModel = $dataTableColumnModel;
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
     * @return \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer
     */
    public function getDataTableCellRenderer()
    {
        return $this->dataTableCellRenderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer $dataTableCellRenderer
     */
    public function setDataTableCellRenderer(DataTableCellRenderer $dataTableCellRenderer)
    {
        $this->dataTableCellRenderer = $dataTableCellRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\DataTableColumnModel
     */
    public function getDataTableColumnModel()
    {
        return $this->dataTableColumnModel;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\DataTableColumnModel $dataTableColumnModel
     */
    public function setDataTableColumnModel(DataTableColumnModel $dataTableColumnModel)
    {
        $this->dataTableColumnModel = $dataTableColumnModel;
    }

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

