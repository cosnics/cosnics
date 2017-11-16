<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class DataTableProviderFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Format\DataTable\Service\DataTableCellRendererFactory
     */
    private $dataTableCellRendererFactory;

    /**
     *
     * @var \Chamilo\Libraries\Format\DataTable\Service\DataTableColumnModelFactory
     */
    private $dataTableColumnModelFactory;

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableCellRendererFactory $dataTableCellRendererFactory
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableColumnModelFactory $dataTableColumnModelFactory
     */
    public function __construct(DataTableCellRendererFactory $dataTableCellRendererFactory,
        DataTableColumnModelFactory $dataTableColumnModelFactory)
    {
        $this->dataTableCellRendererFactory = $dataTableCellRendererFactory;
        $this->dataTableColumnModelFactory = $dataTableColumnModelFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Service\DataTableCellRendererFactory
     */
    public function getDataTableCellRendererFactory()
    {
        return $this->dataTableCellRendererFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableCellRendererFactory $dataTableCellRendererFactory
     */
    public function setDataTableCellRendererFactory(DataTableCellRendererFactory $dataTableCellRendererFactory)
    {
        $this->dataTableCellRendererFactory = $dataTableCellRendererFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\DataTable\Service\DataTableColumnModelFactory
     */
    public function getDataTableColumnModelFactory()
    {
        return $this->dataTableColumnModelFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableColumnModelFactory $dataTableColumnModelFactory
     */
    public function setDataTableColumnModelFactory(DataTableColumnModelFactory $dataTableColumnModelFactory)
    {
        $this->dataTableColumnModelFactory = $dataTableColumnModelFactory;
    }
}

