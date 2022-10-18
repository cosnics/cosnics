<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a table with the use of a column model, a data provider and a cell renderer Refactoring from
 * ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Table
{
    use ClassContext;

    /**
     * Suffix for checkbox name when using actions on selected learning objects.
     */
    public const CHECKBOX_NAME_SUFFIX = '_id';

    /**
     * The identifier for the table (used for table actions)
     */
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    protected SortableTable $table;

    private Application $component;

    private ?TableFormActions $formActions;

    private ChamiloRequest $request;

    private Security $security;

    private TableCellRenderer $tableCellRenderer;

    private TableColumnModel $tableColumnModel;

    private TableDataProvider $tableDataProvider;

    public function __construct(
        ChamiloRequest $request, Security $security, TableDataProvider $tableDataProvider,
        TableColumnModel $tableColumnModel, TableCellRenderer $tableCellRenderer
    )
    {
        $this->request = $request;
        $this->tableDataProvider = $tableDataProvider;
        $this->tableColumnModel = $tableColumnModel;
        $this->tableCellRenderer = $tableCellRenderer;
    }

    public function render(?Condition $condition = null): string
    {
        return '';

        //        $this->table = new SortableTable($this->get_name(), [$this, 'countData'], [$this, 'getData'],
        //            $this->getColumnModel()->getDefaultOrderColumn() + ($this->hasFormActions() ? 1 : 0),
        //            $this->getDefaultMaximumNumberofResults(), $this->getColumnModel()->getDefaultOrderDirection(), true);
        //
        //        $this->table->setAdditionalParameters($this->get_parameters());
        //
        //        $this->constructTable();
        //        $this->initializeTable();
        //
        //        return $this->table->render();
    }

    protected function constructTable()
    {
        //        $this->table = new SortableTable($this->get_name(), [$this, 'countData'], [$this, 'getData'],
        //            $this->getTableColumnModel()->getDefaultOrderColumnIndex() + ($this->hasFormActions() ? 1 : 0),
        //            $this->getDefaultMaximumNumberofResults(), $this->getTableColumnModel()->getDefaultOrderDirection(), true);
        //
        //        $this->table->setAdditionalParameters($this->get_parameters());
    }

    public function countData(?Condition $condition = null): int
    {
        return $this->getTableDataProvider()->countData($condition);
    }

    public function getData(?Condition $condition = null): ArrayCollection
    {
        return $this->getTableDataProvider()->getData($condition);
    }

    public function getFormActions(): ?TableFormActions
    {
        return null;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getTableCellRenderer(): TableCellRenderer
    {
        return $this->tableCellRenderer;
    }

    public function getTableColumnModel(): TableColumnModel
    {
        return $this->tableColumnModel;
    }

    public function getTableDataProvider(): TableDataProvider
    {
        return $this->tableDataProvider;
    }

    public function get_component(): Application
    {
        return $this->component;
    }

    public function set_component(Application $component)
    {
        $this->component = $component;
    }

    /**
     * Gets the name of the HTML table element
     *
     * @return string
     */
    public static function get_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(static::class, true);
    }

    /**
     * Returns the parameters for this table
     *
     * @return string[]
     */
    protected function get_parameters()
    {
        return $this->get_component()->get_parameters();
    }

    public function hasFormActions(): bool
    {
        return $this->getFormActions() instanceof TableFormActions && $this->getFormActions()->hasFormActions();
    }

    /**
     * Initializes the table
     */
    protected function initializeTable()
    {
        if ($this->hasFormActions())
        {
            $this->table->setTableFormActions($this->getFormActions());
        }

        $columnModel = $this->getTableColumnModel();
        $columnCount = $columnModel->getColumnCount();

        for ($i = 0; $i < $columnCount; $i ++)
        {
            $column = $columnModel->getColumn($i);

            $headerAttributes = $contentAttributes = [];

            $cssClasses = $column->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
            {
                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
            }

            $this->table->setColumnHeader(
                ($this->hasFormActions() ? $i + 1 : $i), $this->getSecurity()->removeXSS($column->get_title()),
                $column instanceof AbstractSortableTableColumn && $column->is_sortable(), $headerAttributes,
                $contentAttributes
            );
        }

        $direction = intval($this->table->getOrderDirection());
        $columnModel->setDefaultOrderDirection($direction);
        $columnModel->setDefaultOrderColumnIndex($this->table->getDefaultOrderColumn());
    }

    public static function package()
    {
        return static::context();
    }
}
