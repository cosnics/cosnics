<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
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

    /**
     * The identifier for the table (used for table actions)
     */
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    protected ChamiloRequest $request;

    protected Security $security;

    protected SortableTable $table;

    protected ?TableFormActions $tableActions = null;

    protected TableColumnModel $tableColumnModel;

    protected TableDataProvider $tableDataProvider;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, Security $security, Translator $translator, UrlGenerator $urlGenerator,
        TableDataProvider $tableDataProvider, TableColumnModel $tableColumnModel
    )
    {
        $this->request = $request;
        $this->security = $security;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->tableDataProvider = $tableDataProvider;
        $this->tableColumnModel = $tableColumnModel;
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

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getTableActions(): ?TableFormActions
    {
        return $this->tableActions;
    }

    public function getTableColumnModel(): TableColumnModel
    {
        return $this->tableColumnModel;
    }

    public function getTableDataProvider(): TableDataProvider
    {
        return $this->tableDataProvider;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \ReflectionException
     */
    public static function get_name(): string
    {
        return ClassnameUtilities::getInstance()->getClassnameFromNamespace(static::class, true);
    }

    public function hasTableActions(): bool
    {
        return $this->getTableActions() instanceof TableFormActions && $this->getTableActions()->hasFormActions();
    }

    /**
     * Initializes the table
     */
    protected function initializeTable()
    {
        if ($this->hasTableActions())
        {
            $this->table->setTableFormActions($this->getTableActions());
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
                ($this->hasTableActions() ? $i + 1 : $i), $this->getSecurity()->removeXSS($column->get_title()),
                $column instanceof AbstractSortableTableColumn && $column->is_sortable(), $headerAttributes,
                $contentAttributes
            );
        }
    }
}
