<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Table
{
    use ClassContext;

    public const DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE = 1;
    public const DEFAULT_NUMBER_OF_ROWS_PER_PAGE = 20;
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

    protected Pager $pager;

    protected ChamiloRequest $request;

    protected SortableTable $sortableTable;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, Translator $translator, UrlGenerator $urlGenerator, Pager $pager,
        SortableTable $sortableTable
    )
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->pager = $pager;
        $this->sortableTable = $sortableTable;

        $this->initializeColumns();

        if ($this instanceof TableRowActionsSupport)
        {
            $this->addActionColumn();
        }
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function render(?Condition $condition = null): string
    {
        $parameterValues = $this->determineParameterValues($condition);
        $tableActions = $this instanceof TableActionsSupport ? $this->getTableActions() : null;

        return $this->getSortableTable()->render(
            $this->getColumns(), $this->getData($parameterValues, $condition), static::determineName(),
            static::determineParameterNames(), $parameterValues, $tableActions
        );
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

    abstract protected function countData(?Condition $condition = null): int;

    protected static function determineName(): string
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

    protected function determineNumberOfRowsPerPage(): int
    {
        return $this->getRequest()->query->get(
            static::determineParameterName(TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE),
            $this->getDefaultNumberOfRowsPerPage()
        );
    }

    protected function determineOffset(TableParameterValues $parameterValues): int
    {
        try
        {
            return $this->getPager()->getCurrentRangeOffset($parameterValues);
        }
        catch (InvalidPageNumberException $exception)
        {
            return 0;
        }
    }

    protected function determineOrderBy(TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): OrderBy
    {
		$hasTableActions = $tableActions instanceof TableActions && $tableActions->hasActions();
        // Calculates the order column on whether or not the table uses form actions (because sortable
        // table uses data arrays)
        $calculatedOrderColumn = $parameterValues->getOrderColumnIndex() - ($hasTableActions ? 1 : 0);

        $orderProperty = $this->getOrderProperty($calculatedOrderColumn, $parameterValues->getOrderColumnDirection());

        $orderProperties = [];

        if ($orderProperty)
        {
            $orderProperties[] = $orderProperty;
        }

        return new OrderBy($orderProperties);
    }

    protected function determineOrderColumnDirection(): int
    {
        return $this->getRequest()->query->get(
            static::determineParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION),
            $this->getDefaultOrderDirection()
        );
    }

    protected function determineOrderColumnIndex(): int
    {
        return $this->getRequest()->query->get(
            static::determineParameterName(TableParameterValues::PARAM_ORDER_COLUMN_INDEX),
            $this->getDefaultOrderColumnIndex()
        );
    }

    protected function determinePageNumber(): int
    {
        return $this->getRequest()->query->get(
            static::determineParameterName(TableParameterValues::PARAM_PAGE_NUMBER), 1
        );
    }

    protected static function determineParameterName(string $parameterName): string
    {
        return static::determineParameterNames()[$parameterName];
    }

    /**
     * @return string[]
     */
    protected static function determineParameterNames(): array
    {
        $tableName = static::determineName();

        return [
            TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE => $tableName . '_' .
                TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE,
            TableParameterValues::PARAM_ORDER_COLUMN_INDEX => $tableName . '_' .
                TableParameterValues::PARAM_ORDER_COLUMN_INDEX,
            TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION => $tableName . '_' .
                TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION,
            TableParameterValues::PARAM_PAGE_NUMBER => $tableName . '_' . TableParameterValues::PARAM_PAGE_NUMBER,
            TableParameterValues::PARAM_SELECT_ALL => $tableName . '_' . TableParameterValues::PARAM_SELECT_ALL
        ];
    }

    protected function determineParameterValues(?Condition $condition = null): TableParameterValues
    {
        $numberOfRowsPerPage = $this->determineNumberOfRowsPerPage();
        $totalNumberOfItems = $this->countData($condition);

        $tableParameterValues = new TableParameterValues();

        $tableParameterValues->setTotalNumberOfItems($totalNumberOfItems);
        $tableParameterValues->setNumberOfRowsPerPage(
            $numberOfRowsPerPage == Pager::DISPLAY_ALL ? $totalNumberOfItems : $numberOfRowsPerPage
        );
        $tableParameterValues->setNumberOfColumnsPerPage($this->getDefaultNumberOfColumnsPerPage());
        $tableParameterValues->setPageNumber($this->determinePageNumber());
        $tableParameterValues->setSelectAll(
            $this->getRequest()->query->get(
                static::determineParameterName(TableParameterValues::PARAM_SELECT_ALL), 0
            )
        );
        $tableParameterValues->setOrderColumnIndex($this->determineOrderColumnIndex());
        $tableParameterValues->setOrderColumnDirection($this->determineOrderColumnDirection());

        return $tableParameterValues;
    }

    public function getCheckboxHtml(
        TableActions $tableActions, TableParameterValues $parameterValues, string $value
    ): string
    {
        $html = [];

        $html[] = '<div class="checkbox checkbox-primary">';
        $html[] = '<input class="styled styled-primary" type="checkbox" name="' . $tableActions->getIdentifierName() .
            '[]" value="' . $value . '"';

        if ($parameterValues->getSelectAll())
        {
            $html[] = ' checked="checked"';
        }

        $html[] = '/>';
        $html[] = '<label></label>';
        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Gets the column at the given index in the model.
     */
    public function getColumn(int $index): ?TableColumn
    {
        return $this->columns[$index];
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    protected function getData(
        TableParameterValues $parameterValues, ?Condition $condition = null, ?TableActions $tableActions = null
    ): ArrayCollection
    {
        $results = $this->retrieveData(
            $condition, $parameterValues->getNumberOfRowsPerPage(), $this->determineOffset($parameterValues),
            $this->determineOrderBy($parameterValues, $tableActions)
        );

        $tableData = [];

        foreach ($results as $result)
        {
            $tableData[] = $this->processData($result, $parameterValues, $tableActions);
        }

        return new ArrayCollection($tableData);
    }

    public function getDefaultNumberOfColumnsPerPage(): int
    {
        return static::DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE;
    }

    public function getDefaultNumberOfRowsPerPage(): int
    {
        return static::DEFAULT_NUMBER_OF_ROWS_PER_PAGE;
    }

    public function getDefaultOrderColumnIndex(): int
    {
        return static::DEFAULT_ORDER_COLUMN_INDEX;
    }

    public function getDefaultOrderDirection(): int
    {
        return static::DEFAULT_ORDER_COLUMN_DIRECTION;
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

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     */
    protected function getSortableColumn(int $columnNumber): ?AbstractSortableTableColumn
    {
        $column = $this->getColumn($columnNumber);

        if (!$column instanceof AbstractSortableTableColumn || (!$column->is_sortable()))
        {
            if ($columnNumber != $this->getDefaultOrderColumnIndex())
            {
                return $this->getSortableColumn($this->getDefaultOrderColumnIndex());
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
        return $this instanceof TableActionsSupport && $this->getTableActions() instanceof TableActions &&
            $this->getTableActions()->hasActions();
    }

    abstract protected function initializeColumns();

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     *
     * @return string[]
     */
    protected function processData($result, TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): array
    {
        $rowData = [];

        if ($tableActions instanceof TableActions && $tableActions->hasActions())
        {
            $identifierCellContent = $this->renderIdentifierCell($result);

            if (strlen($identifierCellContent) > 0)
            {
                $identifierCellContent =
                    $this->getCheckboxHtml($tableActions, $parameterValues, $identifierCellContent);
            }

            $rowData[] = $identifierCellContent;
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
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    abstract protected function renderIdentifierCell($result): string;

    abstract protected function retrieveData(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }
}
