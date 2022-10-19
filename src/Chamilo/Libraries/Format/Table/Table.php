<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\OrderedTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

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

    public const DEFAULT_NUMBER_OF_RESULTS = 20;
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 0;

    /**
     * The identifier for the table (used for table actions)
     */
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    protected array $columns;

    /**
     * The column that is currently ordered
     */
    protected ?OrderedTableColumn $currentOrderedColumn;

    protected int $defaultDataOrderColumnIndex;

    protected int $defaultDataOrderDirection;

    protected Pager $pager;

    protected ChamiloRequest $request;

    protected Security $security;

    protected SortableTable $sortableTable;

    protected ?TableFormActions $tableActions = null;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, Security $security, Translator $translator, UrlGenerator $urlGenerator, Pager $pager,
        SortableTable $sortableTable
    )
    {
        $this->request = $request;
        $this->security = $security;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->pager = $pager;
        $this->sortableTable = $sortableTable;

        $this->initializeColumns();

        if ($this instanceof TableRowActionsSupport)
        {
            $this->addActionColumn();
        }

        $this->defaultDataOrderColumnIndex = static::DEFAULT_ORDER_COLUMN_INDEX;
        $this->defaultDataOrderDirection = static::DEFAULT_ORDER_COLUMN_DIRECTION;
    }

    public function render(?Condition $condition = null): string
    {
        return $this->getSortableTable()->render(
            $this->countData($condition), $this->getData($condition), static::determineTableName(),
            $this->determineDataCount(), $this->getColumnCount(), static::determineTableParameterNames(),
            $this->determinePageNumber(), $this->getTableActions()
        );

        //        $this->table = new SortableTable($this->getTableName(), [$this, 'countData'], [$this, 'getData'],
        //            $this->getColumnModel()->getDefaultOrderColumn() + ($this->hasFormActions() ? 1 : 0),
        //            $this->getDefaultMaximumNumberofResults(), $this->getColumnModel()->getDefaultOrderDirection(), true);
        //
        //        $this->table->setAdditionalParameters($this->get_parameters());
        //
        //        $this->constructTable();
        //        $this->initializeTable();

        //return $this->table->render();
    }

    /**
     * Adds the action column only if the action column is not yet added
     */
    protected function addActionColumn()
    {
        foreach ($this->getColumns() as $column)
        {
            if ($column instanceof ActionsTableColumn)
            {
                return;
            }
        }

        $this->addColumn(new ActionsTableColumn());
    }

    protected function addColumn(TableColumn $column, ?int $index = null)
    {
        if (is_null($index))
        {
            $this->columns[] = $column;
        }
        else
        {
            array_splice($this->columns, $index, 0, [$column]);
        }
    }

    /**
     * Adds a current ordered column to the list
     */
    protected function addCurrentOrderedColumnForColumnIndexAndOrderDirection(
        int $columnIndex, ?int $orderDirection = SORT_ASC
    )
    {
        $this->currentOrderedColumn = new OrderedTableColumn(
            $this->getColumn($columnIndex), $orderDirection
        );
    }

    abstract protected function countData(?Condition $condition = null): int;

    protected function determineDataCount(): int
    {
        return $this->getRequest()->query->get(
            static::determineTableParameterName(HtmlTable::PARAM_NUMBER_OF_ITEMS_PER_PAGE), $this->getDefaultDataCount()
        );
    }

    protected function determineDataOffset(?Condition $condition = null): int
    {
        try
        {
            $numberOfItems = $this->countData($condition);
            $numberOfItemsPerPage = $this->determineDataCount();
            $actualNumberOfItemsPerPage =
                $numberOfItemsPerPage == Pager::DISPLAY_ALL ? $numberOfItems : $numberOfItemsPerPage;

            return $this->getPager()->getCurrentRangeOffset(
                $this->determinePageNumber(), $actualNumberOfItemsPerPage, $this->getColumnCount(), $numberOfItems
            );
        }
        catch (InvalidPageNumberException $exception)
        {
            return 0;
        }
    }

    protected function determineDataOrderBy(bool $hasTableActions = false): OrderBy
    {
        // Calculates the order column on whether or not the table uses form actions (because sortable
        // table uses data arrays)
        $calculatedOrderColumn = $this->determineDataOrderColumnIndex() - ($hasTableActions ? 1 : 0);

        $orderProperty = $this->getOrderProperty(
            $calculatedOrderColumn, $this->determineDataOrderDirection()
        );

        $orderProperties = [];

        if ($orderProperty)
        {
            $orderProperties[] = $orderProperty;
        }

        return new OrderBy($orderProperties);
    }

    protected function determineDataOrderColumnIndex(): int
    {
        return $this->getRequest()->query->get(
            static::determineTableParameterName(HtmlTable::PARAM_ORDER_COLUMN), $this->getDefaultDataOrderColumnIndex()
        );
    }

    protected function determineDataOrderDirection(): int
    {
        return $this->getRequest()->query->get(
            static::determineTableParameterName(HtmlTable::PARAM_ORDER_DIRECTION), $this->getDefaultDataOrderDirection()
        );
    }

    protected function determinePageNumber(): int
    {
        return $this->getRequest()->query->get(static::determineTableParameterName(HtmlTable::PARAM_PAGE_NUMBER), 1);
    }

    protected static function determineTableName(): string
    {
        try
        {
            return ClassnameUtilities::getInstance()->getClassnameFromNamespace(static::class, true);
        }
        catch (Exception $exception)
        {
            return 'table';
        }
    }

    protected static function determineTableParameterName(string $parameterName): string
    {
        return static::determineTableParameterNames()[$parameterName];
    }

    /**
     * @return string[]
     */
    protected static function determineTableParameterNames(): array
    {
        $tableName = static::determineTableName();

        return [
            HtmlTable::PARAM_NUMBER_OF_ITEMS_PER_PAGE => $tableName . '_' . HtmlTable::PARAM_NUMBER_OF_ITEMS_PER_PAGE,
            HtmlTable::PARAM_ORDER_COLUMN => $tableName . '_' . HtmlTable::PARAM_ORDER_COLUMN,
            HtmlTable::PARAM_ORDER_DIRECTION => $tableName . '_' . HtmlTable::PARAM_ORDER_DIRECTION,
            HtmlTable::PARAM_PAGE_NUMBER => $tableName . '_' . HtmlTable::PARAM_PAGE_NUMBER
        ];
    }

    /**
     * Gets the column at the given index in the model.
     */
    public function getColumn(int $index): ?TableColumn
    {
        return $this->columns[$index];
    }

    public function getColumnCount(): int
    {
        return 1;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns the current ordered column
     */
    public function getCurrentOrderedColumn(): ?OrderedTableColumn
    {
        return $this->currentOrderedColumn;
    }

    public function setCurrentOrderedColumn(OrderedTableColumn $orderedTableColumn)
    {
        $this->currentOrderedColumn = $orderedTableColumn;
    }

    protected function getData(?Condition $condition = null, bool $hasTableActions = false): ArrayCollection
    {
        $results = $this->retrieveData(
            $condition, $this->determineDataCount(), $this->determineDataOffset($condition),
            $this->determineDataOrderBy($hasTableActions)
        );

        $tableData = [];

        foreach ($results as $result)
        {
            $tableData[] = $this->processData($result, $hasTableActions);
        }

        return new ArrayCollection($tableData);
    }

    public function getDefaultDataCount(): int
    {
        return static::DEFAULT_NUMBER_OF_RESULTS;
    }

    public function getDefaultDataOrderColumnIndex(): int
    {
        return $this->defaultDataOrderColumnIndex;
    }

    public function setDefaultDataOrderColumnIndex(int $columnIndex)
    {
        $this->defaultDataOrderColumnIndex = $columnIndex;
    }

    public function getDefaultDataOrderDirection(): int
    {
        return $this->defaultDataOrderDirection;
    }

    /**
     * @param int $direction The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function setDefaultDataOrderDirection(int $direction)
    {
        $this->defaultDataOrderDirection = $direction;
    }

    /**
     * Returns an object table order object by a given column number and order direction
     */
    public function getOrderProperty(int $columnNumber, int $orderDirection): ?OrderProperty
    {
        $column = $this->getSortableColumn($columnNumber);

        if ($column instanceof AbstractSortableTableColumn)
        {
            return new OrderProperty($column->getConditionVariable(), $orderDirection);
        }

        return null;
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     */
    protected function getSortableColumn(int $columnNumber): ?AbstractSortableTableColumn
    {
        $column = $this->getColumn($columnNumber);

        if (!$column instanceof AbstractSortableTableColumn || (!$column->is_sortable()))
        {
            if ($columnNumber != $this->getDefaultDataOrderColumnIndex())
            {
                return $this->getSortableColumn($this->getDefaultDataOrderColumnIndex());
            }
        }
        else
        {
            return $column;
        }

        return null;
    }

    public function getSortableTable(): SortableTable
    {
        return $this->sortableTable;
    }

    public function getTableActions(): ?TableFormActions
    {
        return $this->tableActions;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function hasTableActions(): bool
    {
        return $this instanceof TableActionsSupport && $this->getTableActions() instanceof TableFormActions &&
            $this->getTableActions()->hasFormActions();
    }

    /**
     * Initializes the columns for the table
     */
    abstract protected function initializeColumns();

    /**
     * Initializes the table
     */
    protected function initializeTable()
    {
        //        if ($this->hasTableActions())
        //        {
        //            $this->table->setTableFormActions($this->getTableActions());
        //        }
        //
        //        $columnModel = $this->getTableColumnModel();
        //        $columnCount = $columnModel->getColumnCount();
        //
        //        for ($i = 0; $i < $columnCount; $i ++)
        //        {
        //            $column = $columnModel->getColumn($i);
        //
        //            $headerAttributes = $contentAttributes = [];
        //
        //            $cssClasses = $column->getCssClasses();
        //
        //            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
        //            {
        //                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
        //            }
        //
        //            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT]))
        //            {
        //                $contentAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_CONTENT];
        //            }
        //
        //            $this->table->setColumnHeader(
        //                ($this->hasTableActions() ? $i + 1 : $i), $this->getSecurity()->removeXSS($column->get_title()),
        //                $column instanceof AbstractSortableTableColumn && $column->is_sortable(), $headerAttributes,
        //                $contentAttributes
        //            );
        //        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     *
     * @return string[]
     */
    protected function processData($result, bool $hasTableActions = false): array
    {
        $rowData = [];

        if ($hasTableActions)
        {
            $rowData[] = $this->renderIdentifierCell($result);
        }

        foreach ($this->getColumns() as $column)
        {
            $rowData[] = $this->renderCell($column, $result);
        }

        return $rowData;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    protected function renderCell(TableColumn $column, $result): string
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableRowActionsSupport)
        {
            return $this->renderTableRowActions($result);
        }

        return '';
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    abstract protected function renderIdentifierCell($result): string;

    abstract protected function retrieveData(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;
}
