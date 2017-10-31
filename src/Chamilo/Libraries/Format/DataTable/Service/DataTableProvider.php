<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Format\DataTable\DataTableCellRenderer;
use Chamilo\Libraries\Format\DataTable\DataTableColumnModel;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
abstract class DataTableProvider // implements DataTableProviderInterface
{

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
     * @param \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer $dataTableCellRenderer
     * @param \Chamilo\Libraries\Format\DataTable\DataTableColumnModel $dataTableColumnModel
     */
    public function __construct(DataTableCellRenderer $dataTableCellRenderer, DataTableColumnModel $dataTableColumnModel)
    {
        $this->dataTableCellRenderer = $dataTableCellRenderer;
        $this->dataTableColumnModel = $dataTableColumnModel;
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
}

