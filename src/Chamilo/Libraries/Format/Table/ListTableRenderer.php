<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ListTableRenderer extends AbstractTableRenderer
{
    public const DEFAULT_NUMBER_OF_COLUMNS_PER_PAGE = 1;
    public const DEFAULT_NUMBER_OF_ROWS_PER_PAGE = 20;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        if ($this instanceof TableRowActionsSupport)
        {
            $this->addActionColumn();
        }
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

    protected function processData(ArrayCollection $results, TableParameterValues $parameterValues): ArrayCollection
    {
        $tableActions = $this instanceof TableActionsSupport ? $this->getTableActions() : null;

        $tableData = [];

        foreach ($results as $result)
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

            $tableResultPosition = $this->getTableResultPosition($results->indexOf($result), $parameterValues);

            foreach ($this->getColumns() as $column)
            {
                if ($this instanceof TableRowActionsSupport && $column instanceof ActionsTableColumn)
                {
                    $rowData[] = $this->renderTableRowActions($tableResultPosition, $result);
                }
                else
                {
                    $rowData[] = $this->renderCell($column, $tableResultPosition, $result);
                }
            }

            $tableData[] = $rowData;
        }

        return new ArrayCollection($tableData);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|array $result
     */
    abstract protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $result): string;
}
