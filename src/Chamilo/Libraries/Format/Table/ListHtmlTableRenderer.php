<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\AbstractSortableTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ListHtmlTableRenderer extends AbstractHtmlTableRenderer
{

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function render(
        array $tableColumns, ArrayCollection $tableRows, string $tableName, array $parameterNames,
        TableParameterValues $parameterValues, ?TableActions $tableActions = null
    ): string
    {
        $htmlTable = new HTML_Table(['class' => $this->getTableClasses()], 0, true);

        if ($parameterValues->getTotalNumberOfItems() == 0)
        {
            return $this->getEmptyTable($htmlTable);
        }

        $this->processTableColumns(
            $htmlTable, $tableColumns, $parameterNames, $parameterValues, $tableActions
        );

        return $this->renderTable(
            $htmlTable, $tableColumns, $tableRows, $tableName, $parameterNames, $parameterValues, $tableActions
        );
    }

    public function getFormClasses(): string
    {
        return 'form-table';
    }

    public function getTableActionsJavascriptPath(): string
    {
        return $this->getPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'SortableTable.js';
    }

    public function getTableClasses(): string
    {
        return 'table table-striped table-bordered table-hover table-data';
    }

    public function getTableContainerClasses(): string
    {
        return 'table-responsive';
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
     */
    public function prepareTableData(
        HTML_Table $htmlTable, array $tableColumns, ArrayCollection $tableRows, ?TableActions $tableActions = null
    )
    {
        parent::prepareTableData($htmlTable, $tableColumns, $tableRows, $tableActions);

        $this->processCellAttributes($htmlTable, $tableColumns, $tableActions);
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     * @param string[] $parameterNames
     *
     * @throws \TableException
     */
    protected function processTableColumns(
        HTML_Table $htmlTable, array $tableColumns, array $parameterNames, TableParameterValues $parameterValues,
        ?TableActions $tableActions = null
    )
    {
        if ($tableActions instanceof TableActions && $tableActions->hasActions())
        {
            $columnHeaderHtml =
                '<div class="checkbox checkbox-primary"><input class="styled styled-primary sortableTableSelectToggle" type="checkbox" name="sortableTableSelectToggle" /><label></label></div>';
            $this->setColumnHeader($htmlTable, $parameterNames, $parameterValues, 0, $columnHeaderHtml, false);
        }

        foreach ($tableColumns as $key => $tableColumn)
        {
            $headerAttributes = [];

            $cssClasses = $tableColumn->getCssClasses();

            if (!empty($cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER]))
            {
                $headerAttributes['class'] = $cssClasses[TableColumn::CSS_CLASSES_COLUMN_HEADER];
            }

            $this->setColumnHeader(
                $htmlTable, $parameterNames, $parameterValues,
                ($tableActions instanceof TableActions && $tableActions->hasActions() ? $key + 1 : $key),
                $this->getSecurity()->removeXSS($tableColumn->get_title()),
                $tableColumn instanceof AbstractSortableTableColumn && $tableColumn->is_sortable(), $headerAttributes
            );
        }
    }

    /**
     * @param string[] $parameterNames
     * @param string[] $headerAttributes
     *
     * @throws \TableException
     */
    public function setColumnHeader(
        HTML_Table $htmlTable, array $parameterNames, TableParameterValues $parameterValues, int $columnIndex,
        string $label, bool $isSortable = true, ?array $headerAttributes = null
    )
    {
        $header = $htmlTable->getHeader();

        if ($isSortable)
        {
            $currentOrderColumnIndex = $parameterValues->getOrderColumnIndex();
            $currentOrderColumnDirection = $parameterValues->getOrderColumnDirection();

            if ($columnIndex != $currentOrderColumnIndex)
            {
                $currentOrderColumnIndex = $columnIndex;
                $currentOrderColumnDirection = SORT_ASC;
                $glyph = '';
            }
            else
            {
                if ($currentOrderColumnDirection == SORT_ASC)
                {
                    $currentOrderColumnDirection = SORT_DESC;
                    $glyphType = 'arrow-down-long';
                }
                else
                {
                    $currentOrderColumnDirection = SORT_ASC;
                    $glyphType = 'arrow-up-long';
                }

                $glyph = new FontAwesomeGlyph($glyphType);
                $glyph = $glyph->render();
            }

            $queryParameters = [
                $parameterNames[TableParameterValues::PARAM_PAGE_NUMBER] => $parameterValues->getPageNumber(),
                $parameterNames[TableParameterValues::PARAM_NUMBER_OF_ROWS_PER_PAGE] => $parameterValues->getNumberOfRowsPerPage(
                ),
                $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_INDEX] => $currentOrderColumnIndex,
                $parameterNames[TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION] => $currentOrderColumnDirection
            ];

            $content = '<a href="' . $this->getUrlGenerator()->fromRequest($queryParameters) . '">' . $label . '</a> ' .
                $glyph;
        }
        else
        {
            $content = $label;
        }

        $header->setHeaderContents(0, $columnIndex, $content);
        $header->setColAttributes($columnIndex, $headerAttributes);
    }
}
