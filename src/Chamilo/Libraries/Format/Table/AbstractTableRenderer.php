<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Storage\DataClass\DataClass;
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
abstract class AbstractTableRenderer
{
    use ClassContext;

    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 0;

    /**
     * The identifier for the table (used for table actions)
     */
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * @var \Chamilo\Libraries\Format\Table\Column\TableColumn[]
     */
    protected array $columns = [];

    protected AbstractHtmlTableRenderer $htmlTableRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, AbstractHtmlTableRenderer $htmlTableRenderer
    )
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->htmlTableRenderer = $htmlTableRenderer;

        $this->initializeColumns();
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function render(
        TableParameterValues $parameterValues, ArrayCollection $tableData, ?string $tableName = null
    ): string
    {
        $tableName = $tableName ?: $this->determineTableName();
        $tableActions = $this instanceof TableActionsSupport ? $this->getTableActions() : null;

        return $this->getHtmlTableRenderer()->render(
            $this->getColumns(), $this->processData($tableData, $parameterValues), $tableName,
            $this->getParameterNames($tableName), $parameterValues, $tableActions
        );
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

    public function determineOrderBy(TableParameterValues $parameterValues): OrderBy
    {
        $orderProperty = $this->getOrderProperty(
            $parameterValues->getOrderColumnIndex(), $parameterValues->getOrderColumnDirection()
        );

        $orderProperties = [];

        if ($orderProperty)
        {
            $orderProperties[] = $orderProperty;
        }

        return new OrderBy($orderProperties);
    }

    protected function determineTableName(): string
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

    /**
     * @return int[]
     */
    public function getDefaultParameterValues(): array
    {
        return [
            TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION => static::DEFAULT_ORDER_COLUMN_DIRECTION,
            TableParameterValues::PARAM_ORDER_COLUMN_INDEX => static::DEFAULT_ORDER_COLUMN_INDEX,
            TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE => static::DEFAULT_NUMBER_OF_ROWS_PER_PAGE,
            TableParameterValues::PARAM_NUMBER_OF_COLUMNS_PER_PAGE => static::DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE,
        ];
    }

    public function getHtmlTableRenderer(): AbstractHtmlTableRenderer
    {
        return $this->htmlTableRenderer;
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

    /**
     * @return string[]
     */
    public function getParameterNames(?string $tableName = null): array
    {
        if (is_null($tableName))
        {
            $tableName = $this->determineTableName();
        }

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

    /**
     * Returns a column by a given column index if it exists and is sortable, otherwise it returns the default column.
     */
    protected function getSortableColumn(int $columnNumber): ?AbstractSortableTableColumn
    {
        $column = $this->getColumn($columnNumber);

        if (!$column instanceof AbstractSortableTableColumn || (!$column->is_sortable()))
        {
            if ($columnNumber != static::DEFAULT_ORDER_COLUMN_INDEX)
            {
                return $this->getSortableColumn(static::DEFAULT_ORDER_COLUMN_INDEX);
            }
        }
        else
        {
            return $column;
        }

        return null;
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

    abstract protected function processData(ArrayCollection $results, TableParameterValues $parameterValues
    ): ArrayCollection;

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    abstract protected function renderIdentifierCell($result): string;

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }
}
