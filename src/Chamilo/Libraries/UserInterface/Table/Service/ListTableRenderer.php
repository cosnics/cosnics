<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\ActionsTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableParameterValues;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableRowActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
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
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, ClassnameUtilities $classnameUtilities
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );

        if ($this instanceof TableRowActionsSupport)
        {
            $this->addActionColumn();
        }
    }

    protected function addActionColumn(): static
    {
        foreach ($this->getColumns() as $column)
        {
            if ($column instanceof ActionsTableColumn)
            {
                return $this;
            }
        }

        $this->addColumn(new ActionsTableColumn());

        return $this;
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
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\DataClass|array $result
     */
    abstract protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result
    ): string;
}
