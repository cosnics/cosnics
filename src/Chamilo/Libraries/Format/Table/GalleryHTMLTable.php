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
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
     * @param string $defaultOrderDirection
     * @param string $allowOrderDirection
     * @param string $allowPageSelection
     * @param string $allowPageNavigation
     */
    public function __construct($tableName = 'gallery_table', $sourceCountFunction = null, $sourceDataFunction = null,
        $sourcePropertiesFunction = null, $defaultOrderColumn = 1, $defaultNumberOfItemsPerPage = 20,
        $defaultOrderDirection = SORT_ASC, $allowOrderDirection = true, $allowPageSelection = true, $allowPageNavigation = true)
    {
        parent::__construct(
            $tableName,
            $sourceCountFunction,
            $sourceDataFunction,
            $defaultOrderColumn,
            $defaultNumberOfItemsPerPage,
            $defaultOrderDirection,
            $allowPageSelection,
            $allowPageNavigation);

        $this->allowOrderDirection = $allowOrderDirection;
        $this->sourcePropertiesFunction = $sourcePropertiesFunction;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel
     */
    public function getSourceProperties()
    {
        if (! is_null($this->getSourcePropertiesFunction()))
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
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableClasses()
     */
    public function getTableClasses()
    {
        return 'table-gallery';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getColumnCount()
     */
    public function getColumnCount()
    {
        return self::DEFAULT_COLUMN_COUNT;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getFormClasses()
     */
    public function getFormClasses()
    {
        return 'form-gallery-table';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::renderTableFilters()
     */
    public function renderTableFilters()
    {
        $html = array();

        $html[] = parent::renderTableFilters();
        $html[] = $this->renderPropertySorting();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderPropertySorting()
    {
        $propertyModel = $this->getSourceProperties();

        $html = array();

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
            $dropDownButton->setClasses('btn-sm');
            $dropDownButton->setDropdownClasses('dropdown-menu-right');

            $orderPropertyName = Translation::get(
                (string) StringUtilities::getInstance()->createString($orderProperty->get_name())->upperCamelize());

            if ($this->allowOrderDirection)
            {
                $dropDownButton->addSubButton(new SubButtonDivider());
                $dropDownButton->addSubButton(new SubButtonHeader(Translation::get('SortingDirection')));
                $dropDownButton->addSubButtons($this->renderPropertyDirectionSubButtons());

                $orderDirection = Translation::get(($currentFirstOrderDirection == SORT_ASC ? 'ASC' : 'DESC'));

                $dropDownButton->setLabel(
                    Translation::get(
                        'GalleryTableOrderPropertyWithDirection',
                        array('PROPERTY' => $orderPropertyName, 'DIRECTION' => $orderDirection)));
            }
            else
            {
                $dropDownButton->setLabel(
                    Translation::get('GalleryTableOrderProperty', array('PROPERTY' => $orderPropertyName)));
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
        $subButtons = array();

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $properties = $propertyModel->get_properties();

            foreach ($properties as $index => $property)
            {
                $propertyIndex = $index + ($hasFormActions ? 1 : 0);

                $queryParameters = array();
                $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = $this->getOrderDirection();
                $queryParameters[$this->getParameterName(self::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
                $queryParameters[$this->getParameterName(self::PARAM_ORDER_COLUMN)] = array($propertyIndex);
                $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

                $propertyUrl = new Redirect($queryParameters);

                $label = Translation::get(
                    (string) StringUtilities::getInstance()->createString($property->get_name())->upperCamelize());
                $isSelected = $currentFirstOrderColumn == $propertyIndex;
                $classes = ($isSelected ? 'selected' : 'not-selected');

                $subButtons[] = new SubButton(
                    $label,
                    null,
                    $propertyUrl->getUrl(),
                    SubButton::DISPLAY_LABEL,
                    false,
                    $classes);
            }
        }

        return $subButtons;
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
        $subButtons = array();

        if ($this->allowOrderDirection && $propertyModel instanceof GalleryTablePropertyModel &&
             count($propertyModel->get_properties()) > 0)
        {
            $queryParameters = array();
            $queryParameters[$this->getParameterName(self::PARAM_PAGE_NUMBER)] = $this->getPageNumber();
            $queryParameters[$this->getParameterName(self::PARAM_ORDER_COLUMN)] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = array(SORT_ASC);
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_ASC;
            $classes = ($isSelected ? 'selected' : 'not-selected');

            $subButtons[] = new SubButton(
                Translation::get('ASC'),
                null,
                $propertyUrl->getUrl(),
                SubButton::DISPLAY_LABEL,
                false,
                $classes);

            $queryParameters[$this->getParameterName(self::PARAM_ORDER_DIRECTION)] = SORT_DESC;
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $currentFirstOrderDirection == SORT_DESC;
            $classes = ($isSelected ? 'selected' : 'not-selected');

            $subButtons[] = new SubButton(
                Translation::get('DESC'),
                null,
                $propertyUrl->getUrl(),
                SubButton::DISPLAY_LABEL,
                false,
                $classes);
        }

        return $subButtons;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getActionsButtonToolbar()
     */
    public function getActionsButtonToolbar()
    {
        $buttonToolBar = parent::getActionsButtonToolbar();

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('SelectAll', null, Utilities::COMMON_LIBRARIES),
                new BootstrapGlyph('check'),
                '#',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-all'));

        $buttonToolBar->prependItem(
            new Button(
                Translation::get('UnselectAll', null, Utilities::COMMON_LIBRARIES),
                new BootstrapGlyph('unchecked'),
                '#',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-none'));

        return $buttonToolBar;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableActionsJavascript()
     */
    public function getTableActionsJavascript()
    {
        return ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) . 'GalleryTable.js');
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::getTableContainerClasses()
     */
    public function getTableContainerClasses()
    {
        return 'table-gallery-container';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::processRowAttributes()
     */
    public function processRowAttributes($rowIdentifier, $currentRow)
    {
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::processContentAttributes()
     */
    public function processContentAttributes()
    {
        $this->altRowAttributes(0, array('class' => 'row'), array('class' => 'row'), true);
        $this->setAllAttributes(array('class' => 'col-xs-6 col-lg-3'));
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\HtmlTable::filterData()
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
                        '__CHECKBOX_PLACEHOLDER__',
                        $this->getCheckboxHtml($value[0]),
                        $row[$index]);
                }
            }
        }

        return $row;
    }
}