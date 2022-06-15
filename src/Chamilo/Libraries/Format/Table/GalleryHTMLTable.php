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
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryHTMLTable extends HtmlTable
{
    const DEFAULT_COLUMN_COUNT = 4;

    /**
     *
     * @var boolean
     */
    private $allowOrderDirection;

    /**
     *
     * @var string[]
     */
    private $sourcePropertiesFunction;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel
     */
    private $sourceProperties;

    /**
     *
     * @param string $tableName
     * @param string[] $sourceCountFunction
     * @param string[] $sourceDataFunction
     * @param string[] $sourcePropertiesFunction
     * @param integer $defaultOrderColumn
     * @param integer $defaultNumberOfItemsPerPage
     * @param integer $defaultOrderDirection
     * @param boolean $allowOrderDirection
     * @param boolean $allowPageSelection
     * @param boolean $allowPageNavigation
     */
    public function __construct(
        $tableName = 'gallery_table', $sourceCountFunction = null, $sourceDataFunction = null,
        $sourcePropertiesFunction = null, $defaultOrderColumn = 1, $defaultNumberOfItemsPerPage = 20,
        $defaultOrderDirection = SORT_ASC, $allowOrderDirection = true, $allowPageSelection = true,
        $allowPageNavigation = true
    )
    {
        parent::__construct(
            $tableName, $sourceCountFunction, $sourceDataFunction, $defaultOrderColumn, $defaultNumberOfItemsPerPage,
            $defaultOrderDirection, $allowPageSelection, $allowPageNavigation
        );

        $this->allowOrderDirection = $allowOrderDirection;
        $this->sourcePropertiesFunction = $sourcePropertiesFunction;
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param string[] $row
     *
     * @return string[]
     */
    public function filterData($row)
    {
        foreach ($row as $index => $value)
        {
            if (strlen($value[0]) == 0)
            {
                $row[$index] = '';
            }
            else
            {
                $row[$index] = $value[1];
                $hasActions = $this->getTableFormActions() instanceof TableFormActions &&
                    $this->getTableFormActions()->has_form_actions();

                if ($hasActions)
                {
                    $row[$index] = str_replace(
                        '__CHECKBOX_PLACEHOLDER__', $this->getCheckboxHtml($value[0]), $row[$index]
                    );
                }
            }
        }

        return $row;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function getActionsButtonToolbar()
    {
        $buttonToolBar = parent::getActionsButtonToolbar();

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

    /**
     *
     * @return integer
     */
    public function getColumnCount()
    {
        return self::DEFAULT_COLUMN_COUNT;
    }

    /**
     *
     * @return string
     */
    public function getFormClasses()
    {
        return 'form-gallery-table';
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel
     */
    public function getSourceProperties()
    {
        if (!is_null($this->getSourcePropertiesFunction()))
        {
            if (is_null($this->sourceProperties))
            {
                $this->sourceProperties = call_user_func($this->getSourcePropertiesFunction());
            }

            return $this->sourceProperties;
        }

        return null;
    }

    /**
     *
     * @return string[]
     */
    public function getSourcePropertiesFunction()
    {
        return $this->sourcePropertiesFunction;
    }

    /**
     *
     * @return string
     */
    public function getTableActionsJavascript()
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(StringUtilities::LIBRARIES, true) . 'GalleryTable.js'
        );
    }

    /**
     *
     * @return string
     */
    public function getTableClasses()
    {
        return 'table-gallery col-xs-12';
    }

    /**
     *
     * @return string
     */
    public function getTableContainerClasses()
    {
        return 'table-gallery-container';
    }

    public function prepareTableData()
    {
        $this->processSourceData();

        $this->altRowAttributes(0, array('class' => 'row'), array('class' => 'row'), true);
        $this->setAllAttributes(array('class' => 'col-xs-6 col-lg-3'));

        $this->processCellAttributes();
    }

    /**
     *
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
            $queryParameters[$this->getParameterName(self::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
            $queryParameters[$this->getParameterName(self::PARAM_ORDER_COLUMN)] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = array(SORT_ASC);
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_ASC;

            $subButtons[] = new SubButton(
                Translation::get('ASC'), null, $propertyUrl->getUrl(), SubButton::DISPLAY_LABEL, null, [], null,
                $isSelected
            );

            $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = SORT_DESC;
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
     *
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

            $hasFormActions = $this->getTableFormActions() instanceof TableFormActions &&
                $this->getTableFormActions()->has_form_actions();

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
                        array('PROPERTY' => $orderPropertyName, 'DIRECTION' => $orderDirection)
                    )
                );
            }
            else
            {
                $dropDownButton->setLabel(
                    Translation::get('GalleryTableOrderProperty', array('PROPERTY' => $orderPropertyName))
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
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function renderPropertySubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $hasFormActions = $this->getTableFormActions() instanceof TableFormActions &&
            $this->getTableFormActions()->has_form_actions();
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
                $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = $this->getOrderDirection();
                $queryParameters[$this->getParameterName(self::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
                $queryParameters[$this->getParameterName(self::PARAM_ORDER_COLUMN)] = array($propertyIndex);
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
     *
     * @return string
     */
    public function renderTableFilters()
    {
        $html = [];

        $html[] = parent::renderTableFilters();
        $html[] = $this->renderPropertySorting();

        return implode(PHP_EOL, $html);
    }
}