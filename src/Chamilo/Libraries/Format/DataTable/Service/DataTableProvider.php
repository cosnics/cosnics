<?php

namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Format\DataAction\DataActions;
use Chamilo\Libraries\Format\DataTable\Column\DataTableColumn;
use Chamilo\Libraries\Format\DataTable\DataTableActionsProvider;
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
    const ROW_IDENTIFIER = 'id';
    const ROW_ACTIONS = 'actions';

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
     * @var \Chamilo\Libraries\Format\DataTable\DataTableActionsProvider
     */
    protected $dataTableActionsProvider;

    /**
     * @var \Chamilo\Libraries\Format\DataTable\Service\DataTableProviderFactory
     */
    protected $dataTableProviderFactory;

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableProviderFactory $dataTableProviderFactory
     */
    public function __construct(DataTableProviderFactory $dataTableProviderFactory
    )
    {
        $this->dataTableProviderFactory = $dataTableProviderFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer
     */
    public function getDataTableCellRenderer()
    {
        if (!$this->dataTableCellRenderer instanceof DataTableCellRenderer)
        {
            $this->dataTableCellRenderer =
                $this->dataTableProviderFactory->getDataTableCellRendererFactory()->getDataTableCellRenderer($this);
        }

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
        if (!$this->dataTableColumnModel instanceof DataTableColumnModel)
        {
            $this->dataTableColumnModel =
                $this->dataTableProviderFactory->getDataTableColumnModelFactory()->getDataTableColumnModel($this);
        }

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
     * @return \Chamilo\Libraries\Format\DataTable\DataTableActionsProvider
     */
    public function getDataTableActionsProvider()
    {
        return $this->dataTableActionsProvider;
    }

    /**
     * @param \Chamilo\Libraries\Format\DataTable\DataTableActionsProvider $dataTableActionsProvider
     *
     * @return DataTableProvider
     */
    public function setDataTableActionsProvider(DataTableActionsProvider $dataTableActionsProvider)
    {
        $this->dataTableActionsProvider = $dataTableActionsProvider;

        return $this;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return string[]
     */
    public function handleDataClass(DataClass $dataClass)
    {
        $rowData = array();

        $rowData[self::ROW_IDENTIFIER] = $this->getDataTableCellRenderer()->renderDataIdentifier($dataClass);

        foreach ($this->getColumns() as $column)
        {
            $rowData[$column->getName()] = $this->renderCell($column, $dataClass);
        }

//        $rowData[self::ROW_ACTIONS] = $this->getActions($dataClass)->toArray();

        return $rowData;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Column\DataTableColumn[]
     */
    protected function getColumns()
    {
        return $this->getDataTableColumnModel()->getColumns();
    }

    protected function renderCell(DataTableColumn $column, DataClass $dataClass)
    {
        return $this->getDataTableCellRenderer()->renderCell($column, $dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return DataActions
     */
    protected function getActions(DataClass $dataClass)
    {
        return $this->getDataTableActionsProvider()->getDataClassActions($dataClass);
    }
}

