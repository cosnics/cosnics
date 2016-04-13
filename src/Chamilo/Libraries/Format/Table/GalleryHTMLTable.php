<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
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
     * @var string
     */
    private $tableName;

    /**
     *
     * @var integer
     */
    private $pageNumber;

    /**
     *
     * @var integer
     */
    private $orderColumn;

    /**
     * SORT_ASC or SORT_DESC
     *
     * @var integer
     */
    private $orderDirection;

    /**
     * Number of items to display per page
     */
    private $numberOfItemsPerPage;

    /**
     * The pager object to split the data in several pages
     *
     * @var \Chamilo\Libraries\Format\Table\Pager
     */
    private $pager;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    private $pagerRenderer;

    /**
     * The total number of items in the table
     *
     * @var integer
     */
    private $sourceDataCount;

    /**
     *
     * @var string[]
     */
    private $sourceData;

    /**
     * The function to get the total number of items
     *
     * @var string[]
     */
    private $sourceCountFunction;

    /**
     * The function to the the data to display
     *
     * @var string[]
     */
    private $sourceDataFunction;

    /**
     * A list of actions which will be available through a select list
     *
     * @var \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    private $tableFormActions;

    /**
     * Additional parameters to pass in the URL
     *
     * @var string[]
     */
    private $additionalParameters;

    /**
     * Additional attributes for the th-tags
     *
     * @var string[]
     */
    private $headerAttributes;

    /**
     * Additional attributes for the td-tags
     *
     * @var string[]
     */
    private $cellAttributes;

    /**
     *
     * @var boolean
     */
    private $allowPageSelection = true;

    /**
     *
     * @var boolean
     */
    private $allowPageNavigation = true;

    private $allowOrderDirection;

    private $sourcePropertiesFunction;

    private $sourceProperties;

    private $contentCellAttributes;

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
    public function __construct($tableName = 'table', $sourceCountFunction = null, $sourceDataFunction = null,
        $sourcePropertiesFunction = null, $defaultOrderColumn = 0, $defaultNumberOfItemsPerPage = 20,
        $defaultOrderDirection = SORT_ASC, $allowOrderDirection = true, $allowPageSelection = true, $allowPageNavigation = true)
    {
        parent :: __construct(array('class' => 'table-gallery', 'id' => $tableName), 0, true);

        $this->allowOrderDirection = $allowOrderDirection;
        $this->sourcePropertiesFunction = $sourcePropertiesFunction;
    }

    /**
     *
     * @return string
     */
    public function getTableClasses()
    {
        return 'table-gallery';
    }

    /**
     *
     * @return integer
     */
    public function getColumnCount()
    {
        return self :: DEFAULT_COLUMN_COUNT;
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
     * @return string
     */
    public function renderTableFilters()
    {
        $html = array();

        $html[] = parent :: renderTableFilters();
        $html[] = $this->renderPropertySorting();

        return implode(PHP_EOL, $html);
    }

    public function renderPropertySorting()
    {
        $propertyModel = $this->getSourceProperties();

        $html = array();

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $buttonToolBar = new ButtonToolBar();
            $dropDownButton = new DropdownButton();
            $properties = $propertyModel->get_properties();
            $orderProperty = $properties[$this->getOrderColumn()];

            $dropDownButton->addSubButton(new SubButtonHeader(Translation :: get('SortingProperty')));
            $dropDownButton->addSubButtons($this->renderPropertySubButtons());
            $dropDownButton->setClasses('btn-sm');
            $dropDownButton->setDropdownClasses('dropdown-menu-right');

            $orderPropertyName = Translation :: get(
                (string) StringUtilities :: getInstance()->createString($orderProperty->get_name())->upperCamelize());

            if ($this->allowOrderDirection)
            {
                $dropDownButton->addSubButton(new SubButtonDivider());
                $dropDownButton->addSubButton(new SubButtonHeader(Translation :: get('SortingDirection')));
                $dropDownButton->addSubButtons($this->renderPropertyDirectionSubButtons());

                $orderDirection = Translation :: get(($this->getOrderDirection() == SORT_ASC ? 'ASC' : 'DESC'));

                $dropDownButton->setLabel(
                    Translation :: get(
                        'GalleryTableOrderPropertyWithDirection',
                        array('PROPERTY' => $orderPropertyName, 'DIRECTION' => $orderDirection)));
            }
            else
            {
                $dropDownButton->setLabel(
                    Translation :: get('GalleryTableOrderProperty', array('PROPERTY' => $orderPropertyName)));
            }

            $buttonToolBar->addItem($dropDownButton);

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $html[] = '<div class="pull-right table-order-property">';
            $html[] = $buttonToolBarRenderer->render();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function renderPropertySubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $subButtons = array();

        if ($propertyModel instanceof GalleryTablePropertyModel && count($propertyModel->get_properties()) > 0)
        {
            $properties = $propertyModel->get_properties();

            foreach ($properties as $index => $property)
            {
                $queryParameters = array();
                $queryParameters[$this->getParameterName('direction')] = $this->getOrderDirection();
                $queryParameters[$this->getParameterName('page_nr')] = $this->getPageNumber();
                $queryParameters[$this->getParameterName('column')] = $index;
                $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

                $propertyUrl = new Redirect($queryParameters);

                $label = Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($property->get_name())->upperCamelize());
                $isSelected = $this->getOrderColumn() == $index;
                $classes = ($isSelected ? 'selected' : 'not-selected');

                $subButtons[] = new SubButton(
                    $label,
                    null,
                    $propertyUrl->getUrl(),
                    SubButton :: DISPLAY_LABEL,
                    false,
                    $classes);
            }
        }

        return $subButtons;
    }

    /**
     * Get the HTML-code wich represents a form to select the order direction.
     */
    public function renderPropertyDirectionSubButtons()
    {
        $propertyModel = $this->getSourceProperties();
        $subButtons = array();

        if ($this->allowOrderDirection && $propertyModel instanceof GalleryTablePropertyModel &&
             count($propertyModel->get_properties()) > 0)
        {
            $queryParameters = array();
            $queryParameters[$this->getParameterName('page_nr')] = $this->getPageNumber();
            $queryParameters[$this->getParameterName('column')] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $queryParameters[$this->getParameterName('direction')] = SORT_ASC;
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $this->getOrderDirection() == SORT_ASC;
            $classes = ($isSelected ? 'selected' : 'not-selected');

            $subButtons[] = new SubButton(
                Translation :: get('ASC'),
                null,
                $propertyUrl->getUrl(),
                SubButton :: DISPLAY_LABEL,
                false,
                $classes);

            $queryParameters[$this->getParameterName('direction')] = SORT_DESC;
            $propertyUrl = new Redirect($queryParameters);
            $isSelected = $this->getOrderDirection() == SORT_DESC;
            $classes = ($isSelected ? 'selected' : 'not-selected');

            $subButtons[] = new SubButton(
                Translation :: get('DESC'),
                null,
                $propertyUrl->getUrl(),
                SubButton :: DISPLAY_LABEL,
                false,
                $classes);
        }

        return $subButtons;
    }

    public function getActionsButtonToolbar()
    {
        $buttonToolBar = parent :: getActionsButtonToolbar();

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES),
                new BootstrapGlyph('check'),
                '#',
                Button :: DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-all'));

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('UnselectAll', null, Utilities :: COMMON_LIBRARIES),
                new BootstrapGlyph('unchecked'),
                '#',
                Button :: DISPLAY_ICON_AND_LABEL,
                false,
                'btn-sm select-none'));

        return $buttonToolBar;
    }

    public function getTableActionsJavascript()
    {
        return ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) . 'GalleryTable.js');
    }

    public function getTableContainerClasses()
    {
        return 'table-gallery-container';
    }

    /**
     * Get the HTML-code with the data-table.
     *
     * @return string
     */
    public function getBodyHtml()
    {
        $pager = $this->getPager();
        $offset = $pager->getCurrentRangeOffset();
        $table_data = $this->getSourceData($offset);

        foreach ($table_data as $index => $row)
        {
            $row = $this->filterData($row);
            $this->addRow($row);
        }

        $this->altRowAttributes(0, array('class' => 'row'), array('class' => 'row'), true);
        $this->altColAttributes(0, array('class' => 'col-xs-6 col-lg-3'), array('class' => 'col-xs-6 col-lg-3'), true);

        foreach ($this->headerAttributes as $column => & $attributes)
        {
            $this->setCellAttributes(0, $column, $attributes);
        }

        foreach ($this->contentCellAttributes as $column => & $attributes)
        {
            $this->setColAttributes($column, $attributes);
        }

        return \HTML_Table :: toHTML();
    }

    /**
     * Alternates the col attributes starting at $start
     *
     * @param int $start Col index of col in which alternating begins
     * @param mixed $attributes1 Associative array or string of table col attributes
     * @param mixed $attributes2 Associative array or string of table col attributes
     * @param bool $inTR false if attributes are to be applied in TD tags; true if attributes are to be applied in TR
     *        tag
     * @param int $firstAttributes (optional) Which attributes should be applied to the first col, 1 or 2.
     * @param int $body (optional) The index of the body to set. Pass null to set for all bodies.
     * @access public
     * @throws PEAR_Error
     */
    function altColAttributes($start, $attributes1, $attributes2, $inTR = false, $firstAttributes = 1, $body = null)
    {
        if (! is_null($body))
        {
            $ret = $this->_adjustTbodyCount($body, 'altColAttributes');
            if (\PEAR :: isError($ret))
            {
                return $ret;
            }
            $this->_tbodies[$body]->altColAttributes($start, $attributes1, $attributes2, $inTR, $firstAttributes);
        }
        else
        {
            for ($i = 0; $i < $this->_tbodyCount; $i ++)
            {
                $this->bodyAltColAttributes(
                    $this->_tbodies[$i],
                    $start,
                    $attributes1,
                    $attributes2,
                    $inTR,
                    $firstAttributes);
                // if the tbody's row count is odd, toggle $firstAttributes to
                // prevent the next tbody's first row from having the same
                // attributes as this tbody's last row.
                if ($this->_tbodies[$i]->getColCount() % 2)
                {
                    $firstAttributes ^= 3;
                }
            }
        }
    }

    /**
     * Alternates the col attributes starting at $start
     *
     * @param int $start Col index of col in which alternating begins
     * @param mixed $attributes1 Associative array or string of table col attributes
     * @param mixed $attributes2 Associative array or string of table col attributes
     * @param bool $inTR false if attributes are to be applied in TD tags; true if attributes are to be applied in TR
     *        tag
     * @param int $firstAttributes (optional) Which attributes should be applied to the first row, 1 or 2.
     * @access public
     */
    function bodyAltColAttributes(&$body, $start, $attributes1, $attributes2, $inTR = false, $firstAttributes = 1)
    {
        for ($col = $start; $col < $body->_cols; $col ++)
        {
            if (($col + $start + ($firstAttributes - 1)) % 2 == 0)
            {
                $attributes = $attributes1;
            }
            else
            {
                $attributes = $attributes2;
            }
            $body->updateColAttributes($col, $attributes, $inTR);
        }
    }

    /**
     * Transform all data in a table-row, using the filters defined by the function set_column_filter(...) defined
     * elsewhere in this class.
     * If you've defined actions, the first element of the given row will be converted into a
     * checkbox
     *
     * @param string[]
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

                $checkboxHtml = array();

                if ($hasActions)
                {
                    $checkboxHtml[] = '<div class="checkbox checkbox-primary">';
                    $checkboxHtml[] = '<input class="styled styled-primary" type="checkbox" name="' .
                         $this->getTableFormActions()->getIdentifierName() . '[]" value="' . $value[0] . '"';

                    if (Request :: get($this->getParameterName('selectall')))
                    {
                        $checkboxHtml[] = ' checked="checked"';
                    }

                    $checkboxHtml[] = '/>';
                    $checkboxHtml[] = '<label></label>';
                    $checkboxHtml[] = '</div>';
                }

                $row[$index] = str_replace('__CHECKBOX_PLACEHOLDER__', implode('', $checkboxHtml), $row[$index]);
            }
        }

        return $row;
    }

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

    public function getSourcePropertiesFunction()
    {
        return $this->sourcePropertiesFunction;
    }
}