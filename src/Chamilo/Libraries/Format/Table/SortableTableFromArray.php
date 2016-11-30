<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Platform\Security;

/**
 * Sortable table which can be used for data available in an array
 */
class SortableTableFromArray extends SortableTable
{

    /**
     * The array containing all data for this table
     * 
     * @var string[]
     */
    private $tableData;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    private $tableColumns;

    /**
     *
     * @var string[]
     */
    private $additionalParameters;

    /**
     *
     * @var integer
     */
    private $defaultOrderColumn;

    /**
     *
     * @var integer
     */
    private $defaultPerPage;

    /**
     *
     * @var string
     */
    private $tableName;

    /**
     *
     * @var integer
     */
    private $defaultOrderDirection;

    /**
     *
     * @var boolean
     */
    private $allowPageSelection;

    /**
     *
     * @var boolean
     */
    private $enableSorting;

    /**
     *
     * @var boolean
     */
    private $allowPageNavigation;

    /**
     *
     * @param string[] $tableData
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $additionalParameters
     * @param integer $defaultOrderColumn
     * @param integer $defaultPerPage
     * @param integer $defaultOrderDirection
     * @param string $tableName
     * @param boolean $allowPageSelection
     * @param boolean $enableSorting
     * @param boolean $allowPageNavigation
     */
    public function __construct($tableData, $tableColumns, $additionalParameters = array(), $defaultOrderColumn = 1, 
        $defaultPerPage = 20, $defaultOrderDirection = SORT_ASC, $tableName = 'array_table', $allowPageSelection = true, $enableSorting = true, 
        $allowPageNavigation = true)
    {
        $this->tableName = $tableName;
        
        parent::__construct(
            $tableName, 
            array($this, 'countData'), 
            array($this, 'getData'), 
            $defaultOrderColumn, 
            $defaultPerPage, 
            $defaultOrderDirection, 
            $allowPageSelection, 
            $allowPageNavigation);
        
        $this->tableData = $tableData;
        $this->tableColumns = $tableColumns;
        $this->additionalParameters = $additionalParameters;
        $this->defaultOrderColumn = $defaultOrderColumn;
        $this->defaultPerPage = $defaultPerPage;
        $this->allowPageSelection = $allowPageSelection;
        $this->defaultOrderDirection = $defaultOrderDirection;
        $this->allowPageSelection = $allowPageSelection;
        $this->enableSorting = $enableSorting;
        $this->allowPageNavigation = $allowPageNavigation;
        
        if (! $allowPageSelection)
        {
            $this->defaultPerPage = count($tableData);
        }
    }

    /**
     *
     * @return string
     */
    public function toHtml()
    {
        $this->initializeTable();
        
        return parent::toHtml();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\SortableTable
     */
    protected function initializeTable()
    {
        $this->setAdditionalParameters($this->getAdditionalParameters());
        
        foreach ($this->getTableColumns() as $key => $tableColumn)
        {
            $headerAttributes = $contentAttributes = array();
            
            $cssClasses = $tableColumn->getCssClasses();
            
            if (! empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }
            
            if (! empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
            }
            
            $this->setColumnHeader(
                $key, 
                Security::remove_XSS($tableColumn->get_title()), 
                $tableColumn->is_sortable(), 
                $headerAttributes, 
                $contentAttributes);
        }
    }

    /**
     *
     * @param integer $offset
     * @param integer $count
     * @param integer $orderColumn
     * @param integer $orderDirection
     *
     * @return string[]
     */
    public function getData($offset, $count, $orderColumn, $orderDirection)
    {
        $content = $this->getTableData();
        
        if ($this->getEnableSorting())
        {
            $content = TableSort::sort_table($content, $orderColumn, $orderDirection);
        }
        
        if ($this->getAllowPageSelection())
        {
            $content = array_slice($content, $offset, $count);
        }
        
        return $content;
    }

    /**
     *
     * @return integer
     */
    public function countData()
    {
        return count($this->getTableData());
    }

    /**
     *
     * @return string[]
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     *
     * @param string[] $tableData
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;
    }

    /**
     *
     * @param string[] $dataRow
     */
    public function addTableData($dataRow)
    {
        $this->tableData[] = $dataRow;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getTableColumns()
    {
        return $this->tableColumns;
    }

    /**
     *
     * @param string[] $tableColumns
     */
    public function setTableColumns($tableColumns)
    {
        $this->tableColumns = $tableColumns;
    }

    /**
     *
     * @return multitype:\Chamilo\Libraries\Format\Table\string
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

    /**
     *
     * @param multitype :\Chamilo\Libraries\Format\Table\string $additionalParameters
     */
    public function setAdditionalParameters($additionalParameters)
    {
        $this->additionalParameters = $additionalParameters;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultOrderColumn()
    {
        return $this->defaultOrderColumn;
    }

    /**
     *
     * @param integer $defaultOrderColumn
     */
    public function setDefaultOrderColumn($defaultOrderColumn)
    {
        $this->defaultOrderColumn = $defaultOrderColumn;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultPerPage()
    {
        return $this->defaultPerPage;
    }

    /**
     *
     * @param integer $defaultPerPage
     */
    public function setDefaultPerPage($defaultPerPage)
    {
        $this->defaultPerPage = $defaultPerPage;
    }

    /**
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultOrderDirection()
    {
        return $this->defaultOrderDirection;
    }

    /**
     *
     * @param integer $defaultOrderDirection
     */
    public function setDefaultOrderDirection($defaultOrderDirection)
    {
        $this->defaultOrderDirection = $defaultOrderDirection;
    }

    /**
     *
     * @return boolean
     */
    public function getAllowPageSelection()
    {
        return $this->allowPageSelection;
    }

    /**
     *
     * @param boolean $allowPageSelection
     */
    public function setAllowPageSelection($allowPageSelection)
    {
        $this->allowPageSelection = $allowPageSelection;
    }

    /**
     *
     * @return boolean
     */
    public function getEnableSorting()
    {
        return $this->enableSorting;
    }

    /**
     *
     * @param boolean $enableSorting
     */
    public function setEnableSorting($enableSorting)
    {
        $this->enableSorting = $enableSorting;
    }

    /**
     *
     * @return boolean
     */
    public function getAllowPageNavigation()
    {
        return $this->allowPageNavigation;
    }

    /**
     *
     * @param boolean $allowPageNavigation
     */
    public function setAllowPageNavigation($allowPageNavigation)
    {
        $this->allowPageNavigation = $allowPageNavigation;
    }
}
