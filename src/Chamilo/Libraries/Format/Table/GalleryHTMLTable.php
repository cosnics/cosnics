<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryHTMLTable extends HtmlTable
{
    public function getActionsButtonToolbar(TableActions $tableActions): ButtonToolBar
    {
        $buttonToolBar = parent::getActionsButtonToolbar($tableActions);

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('SelectAll', null, StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('check-square', [], null, 'far'), '#', Button::DISPLAY_ICON_AND_LABEL, null,
                ['btn-sm select-all']
            )
        );

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('UnselectAll', null, StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('square', [], null, 'far'), '#', Button::DISPLAY_ICON_AND_LABEL, null,
                ['btn-sm select-none']
            )
        );

        return $buttonToolBar;
    }

    public function getFormClasses(): string
    {
        return 'form-gallery-table';
    }

    public function getTableActionsJavascript(): string
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'GalleryTable.js'
        );
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

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertyDirectionSubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $currentOrderDirections = $this->getOrderDirection();
        $currentFirstOrderDirection = $currentOrderDirections[0];
        $subButtons = [];

        if ($this->allowOrderDirection && $propertyModel instanceof GalleryTablePropertyModel &&
            count($propertyModel->get_properties()) > 0)
        {
            $queryParameters = [];
            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_INDEX)] =
                $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] = [SORT_ASC];
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_ASC;

            $subButtons[] = new SubButton(
                Translation::get('ASC'), null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );

            $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] = SORT_DESC;
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_DESC;

            $subButtons[] = new SubButton(
                Translation::get('DESC'), null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );
        }

        return $subButtons;
    }

    /**
     * @return string
     */
    public function renderPropertySorting()
    {
        $propertyModel = $this->getSourceProperties();

        $html = [];

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $buttonToolBar = new ButtonToolBar();
            $dropDownButton = new DropdownButton();
            $properties = $propertyModel->get_properties();

            $currentOrderColumns = $this->getOrderColumn();
            $currentFirstOrderColumn = $currentOrderColumns[0];
            $currentOrderDirections = $this->getOrderDirection();
            $currentFirstOrderDirection = $currentOrderDirections[0];

            $hasFormActions =
                $this->getTableFormActions() instanceof TableActions && $this->getTableFormActions()->hasFormActions();

            $propertyIndex = $currentFirstOrderColumn - ($hasFormActions ? 1 : 0);

            $orderProperty = $properties[$propertyIndex];

            $dropDownButton->addSubButton(new SubButtonHeader(Translation::get('SortingProperty')));
            $dropDownButton->addSubButtons($this->renderPropertySubButtons());
            $dropDownButton->setClasses(['btn-sm']);
            $dropDownButton->setDropdownClasses(['dropdown-menu-right']);

            $orderPropertyName = Translation::get(
                (string) StringUtilities::getInstance()->createString($orderProperty->get_name())->upperCamelize()
            );

            if ($this->allowOrderDirection)
            {
                $dropDownButton->addSubButton(new SubButtonDivider());
                $dropDownButton->addSubButton(new SubButtonHeader(Translation::get('SortingDirection')));
                $dropDownButton->addSubButtons($this->renderPropertyDirectionSubButtons());

                $orderDirection = Translation::get(($currentFirstOrderDirection == SORT_ASC ? 'ASC' : 'DESC'));

                $dropDownButton->setLabel(
                    Translation::get(
                        'GalleryTableOrderPropertyWithDirection',
                        ['PROPERTY' => $orderPropertyName, 'DIRECTION' => $orderDirection]
                    )
                );
            }
            else
            {
                $dropDownButton->setLabel(
                    Translation::get('GalleryTableOrderProperty', ['PROPERTY' => $orderPropertyName])
                );
            }

            $buttonToolBar->addItem($dropDownButton);

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html[] = '<div class="pull-right table-order-property">';
            $html[] = $buttonToolBarRenderer->render();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertySubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $hasFormActions =
            $this->getTableFormActions() instanceof TableActions && $this->getTableFormActions()->hasFormActions();
        $currentOrderColumns = $this->getOrderColumn();
        $currentFirstOrderColumn = $currentOrderColumns[0];
        $subButtons = [];

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $properties = $propertyModel->get_properties();

            foreach ($properties as $index => $property)
            {
                $propertyIndex = $index + ($hasFormActions ? 1 : 0);

                $queryParameters = [];
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_DIRECTION)] =
                    $this->getOrderDirection();
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_PAGE_NUMBER)] =
                    $this->getPageNumber();
                $queryParameters[$this->getParameterName(TableParameterValues::PARAM_ORDER_COLUMN_INDEX)] =
                    [$propertyIndex];
                $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

                $propertyUrl = new Redirect($queryParameters);

                $label = Translation::get(
                    (string) StringUtilities::getInstance()->createString($property->get_name())->upperCamelize()
                );
                $isSelected = $currentFirstOrderColumn == $propertyIndex;

                $subButtons[] = new SubButton(
                    $label, null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null, $isSelected
                );
            }
        }

        return $subButtons;
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function renderTableFilters(
        TableParameterValues $parameterValues, array $parameterNames
    ): string
    {
        $html = [];

        $html[] = parent::renderTableFilters($parameterValues, $parameterNames);

        //$html[] = $this->renderPropertySorting();

        return implode(PHP_EOL, $html);
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

    }
}