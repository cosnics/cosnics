<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\AbstractBaseTableParameters;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractTableRenderer
{
    public const int DEFAULT_ORDER_COLUMN_DIRECTION = SORT_ASC;
    public const int DEFAULT_ORDER_COLUMN_INDEX = 0;
    public const string TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * @var \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[]
     */
    protected array $columns = [];

    public function __construct(
        protected Translator $translator, protected UrlGenerator $urlGenerator,
        protected AbstractHtmlTableRenderer $htmlTableRenderer,
        protected PageNavigationCalculator $pageNavigationCalculator,
        protected DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        protected ClassnameUtilities $classnameUtilities
    )
    {
        $this->initializeColumns();
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(
        TableParameterValues $parameterValues, ArrayCollection $tableData, ?string $tableName = null
    ): string
    {
        $tableName = $tableName ?: $this->determineTableName();
        $tableActions = $this instanceof TableActionsSupport ? $this->getTableActions() : null;

        return $this->htmlTableRenderer->render(
            $this->getColumns(), $this->processData($tableData, $parameterValues), $tableName,
            $this->getParameterNames($tableName), $parameterValues, $tableActions
        );
    }

    protected function addColumn(TableColumn $column, ?int $index = null): static
    {
        if (is_null($index)) {
            $this->columns[] = $column;
        }
        else {
            array_splice($this->columns, $index, 0, [$column]);
        }

        return $this;
    }

    public function determineOrderBy(TableParameterValues $parameterValues): OrderBy
    {
        $orderProperty = $this->getOrderProperty(
            $parameterValues->getOrderColumnIndex(), $parameterValues->getOrderColumnDirection()
        );

        $orderProperties = [];

        if ($orderProperty) {
            $orderProperties[] = $orderProperty;
        }

        return new OrderBy($orderProperties);
    }

    protected function determineTableName(): string
    {
        try {
            return $this->classnameUtilities->getClassnameFromNamespace(static::class, true);
        }
        catch (Exception) {
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

        if ($parameterValues->getSelectAll()) {
            $html[] = ' checked="checked"';
        }

        $html[] = '/>';
        $html[] = '<label></label>';
        $html[] = '</div>';

        return implode('', $html);
    }

    public function getColumn(int $index): ?TableColumn
    {
        return $this->columns[$index];
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn[] $columns
     */
    public function setColumns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getDefaultParameterValues(): array
    {
        return [
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION => static::DEFAULT_ORDER_COLUMN_DIRECTION,
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX => static::DEFAULT_ORDER_COLUMN_INDEX,
            TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE => static::DEFAULT_NUMBER_OF_ROWS_PER_PAGE,
            TableParameterValues::PARAM_NUMBER_OF_COLUMNS_PER_PAGE => static::DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE,
        ];
    }

    public function getOrderProperty(int $columnNumber, int $orderDirection): ?OrderProperty
    {
        $column = $this->getSortableColumn($columnNumber);

        if ($column instanceof AbstractSortableTableColumn) {
            return new OrderProperty($column->getConditionVariable(), $orderDirection);
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getParameterNames(?string $tableName = null): array
    {
        if (is_null($tableName)) {
            $tableName = $this->determineTableName();
        }

        return [
            TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE => $tableName . '_' .
                TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE,
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_ORDER_COLUMN_INDEX,
            AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_ORDER_COLUMN_DIRECTION,
            AbstractBaseTableParameters::PARAM_PAGE_NUMBER => $tableName . '_' .
                AbstractBaseTableParameters::PARAM_PAGE_NUMBER,
            TableParameterValues::PARAM_SELECT_ALL => $tableName . '_' . TableParameterValues::PARAM_SELECT_ALL
        ];
    }

    protected function getSortableColumn(int $columnNumber): ?AbstractSortableTableColumn
    {
        $column = $this->getColumn($columnNumber);

        if (!$column instanceof AbstractSortableTableColumn || (!$column->isSortable())) {
            if ($columnNumber != static::DEFAULT_ORDER_COLUMN_INDEX) {
                return $this->getSortableColumn(static::DEFAULT_ORDER_COLUMN_INDEX);
            }
        }
        else {
            return $column;
        }

        return null;
    }

    protected function getTableResultPosition(int $resultPosition, TableParameterValues $parameterValues
    ): TableResultPosition
    {
        $tableResultPosition = new TableResultPosition();

        $tableResultPosition->setPosition($resultPosition);
        $tableResultPosition->setPageNumber($parameterValues->getPageNumber());
        $tableResultPosition->setNumberOfItemsPerPage($parameterValues->getNumberOfItemsPerPage());
        $tableResultPosition->setTotalNumberOfItems($parameterValues->getTotalNumberOfItems());
        $tableResultPosition->setTotalNumberOfPages(
            $this->pageNavigationCalculator->getNumberOfPages(
                $parameterValues->getNumberOfItemsPerPage(), $parameterValues->getTotalNumberOfItems()
            )
        );
        $tableResultPosition->setOrderColumnIndex($parameterValues->getOrderColumnIndex());
        $tableResultPosition->setOrderColumnDirection($parameterValues->getOrderColumnDirection());

        return $tableResultPosition;
    }

    public function hasTableActions(): bool
    {
        return $this instanceof TableActionsSupport && $this->getTableActions() instanceof TableActions &&
            $this->getTableActions()->hasActions();
    }

    abstract protected function initializeColumns(): void;

    abstract protected function processData(ArrayCollection $results, TableParameterValues $parameterValues
    ): ArrayCollection;

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\DataClass|array $result
     */
    abstract protected function renderIdentifierCell(mixed $result): string;
}
