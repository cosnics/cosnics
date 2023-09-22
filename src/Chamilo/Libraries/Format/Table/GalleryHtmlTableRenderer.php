<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryHtmlTableRenderer extends AbstractHtmlTableRenderer
{

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn[] $tableColumns
     *
     * @throws \TableException
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

        return $this->renderTable(
            $htmlTable, $tableColumns, $tableRows, $tableName, $parameterNames, $parameterValues, $tableActions
        );
    }

    public function getActionsButtonToolbar(TableActions $tableActions): ButtonToolBar
    {
        $buttonToolBar = parent:: getActionsButtonToolbar($tableActions);

        $translator = $this->getTranslator();

        $buttonToolBar->prependItem(
            new Button(
                $translator->trans('SelectAll', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('check-square', [], null, 'far'), '#', AbstractButton::DISPLAY_ICON_AND_LABEL,
                null, ['btn-sm select-all']
            )
        );

        $buttonToolBar->prependItem(
            new Button(
                $translator->trans('UnselectAll', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('square', [], null, 'far'), '#', AbstractButton::DISPLAY_ICON_AND_LABEL, null,
                ['btn-sm select-none']
            )
        );

        return $buttonToolBar;
    }

    public function getFormClasses(): string
    {
        return 'form-gallery-table';
    }

    public function getTableActionsJavascriptPath(): string
    {
        return $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'GalleryTable.js';
    }

    public function getTableClasses(): string
    {
        return 'table-gallery col-xs-12';
    }

    public function getTableContainerClasses(): string
    {
        return 'table-gallery-container';
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

        $htmlTable->setAllAttributes(['class' => 'col-xs-6 col-lg-3']);
    }

}