<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTablePropertyModel;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryHTMLTable extends HTML_Table
{
    const DEFAULT_COLUMN_COUNT = 4;

    private $tableName;

    private $additionalParameters;

    private $pageNumber;

    private $orderColumn;

    private $orderDirection;

    private $numberOfItemsPerPage;

    private $allowOrderDirection;

    private $allowPageSelection;

    private $allowPageNavigation;

    private $sourceCountFunction;

    private $sourceDataFunction;

    private $sourcePropertiesFunction;

    private $sourceData;

    private $sourceCount;

    private $sourceProperties;

    private $pager;

    private $sourceDataCount;

    private $tableFormActions;

    private $contentCellAttributes;

    private $headerAttributes;

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

        $this->tableName = $tableName;
        $this->additionalParameters = array();

        $this->pageNumber = $this->determinePageNumber();
        $this->orderColumn = $this->determineOrderColumn($defaultOrderColumn);
        $this->orderDirection = $this->determineOrderDirection($defaultOrderDirection);
        $this->numberOfItemsPerPage = $this->determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage);

        $this->allowOrderDirection = $allowOrderDirection;
        $this->allowPageSelection = $allowPageSelection;
        $this->allowPageNavigation = $allowPageNavigation;

        $this->sourceCountFunction = $sourceCountFunction;
        $this->sourceDataFunction = $sourceDataFunction;
        $this->sourcePropertiesFunction = $sourcePropertiesFunction;

        $this->pager = null;
        $this->sourceDataCount = null;

        $this->tableFormActions = null;
        $this->contentCellAttributes = array();
        $this->headerAttributes = array();
    }

    /**
     *
     * @return string
     */
    private function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @param string $parameter
     * @return string
     */
    private function getParameterName($parameter)
    {
        return $this->getTableName() . '_' . $parameter;
    }

    /**
     *
     * @return integer
     */
    private function determinePageNumber()
    {
        $variableName = $this->getParameterName('page_nr');
        $requestedPageNumber = Request :: get($variableName);

        return $requestedPageNumber ? $requestedPageNumber : 1;
    }

    /**
     *
     * @param integer $defaultOrderColumn
     * @return integer
     */
    private function determineOrderColumn($defaultOrderColumn)
    {
        $variableName = $this->getParameterName('column');
        $requestedOrderColumn = Request :: get($variableName);

        return ! is_null($requestedOrderColumn) ? $requestedOrderColumn : $defaultOrderColumn;
    }

    /**
     *
     * @param integer $defaultOrderDirection
     * @return integer
     */
    private function determineOrderDirection($defaultOrderDirection)
    {
        $variableName = $this->getParameterName('direction');
        $requestedOrderDirection = Request :: get($variableName);

        return $requestedOrderDirection ? $requestedOrderDirection : $defaultOrderDirection;
    }

    /**
     *
     * @param integer $defaultNumberOfItemsPerPage
     * @return integer
     */
    private function determineNumberOfItemsPerPage($defaultNumberOfItemsPerPage)
    {
        $variableName = $this->getParameterName('per_page');
        $requestedNumberOfItemsPerPage = Request :: get($variableName);

        if ($requestedNumberOfItemsPerPage == Pager :: DISPLAY_ALL)
        {
            return $this->countSourceData();
        }
        else
        {
            return $requestedNumberOfItemsPerPage ? $requestedNumberOfItemsPerPage : $defaultNumberOfItemsPerPage;
        }
    }

    /**
     * Get the Pager object to split the shown data into several pages
     *
     * @return \Chamilo\Libraries\Format\Table\Pager
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $this->pager = new Pager(
                $this->getNumberOfItemsPerPage(),
                self :: DEFAULT_COLUMN_COUNT,
                $this->countSourceData(),
                $this->getPageNumber());
        }

        return $this->pager;
    }

    /**
     *
     * @return string
     * @deprecated Use toHtml() now
     */
    public function as_html()
    {
        return $this->toHtml();
    }

    /**
     *
     * @return string
     */
    public function getEmptyTable()
    {
        $cols = $this->getHeader()->getColCount();

        $this->setCellAttributes(0, 0, 'style="font-style: italic;text-align:center;" colspan=' . $cols);
        $this->setCellContents(0, 0, Translation :: get('NoSearchResults', null, Utilities :: COMMON_LIBRARIES));

        $html = array();

        $html[] = '<div class="table-responsive">';
        $html[] = parent :: toHTML();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableHeader()
    {
        $html = array();

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $tableFormActions = $this->getTableFormActions()->get_form_actions();
            $firstFormAction = array_shift($tableFormActions);

            $html[] = '<form class="form-gallery-table" method="post" action="' . $firstFormAction->get_action() .
                 '" name="form_' . $this->tableName . '">';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = $this->renderActions();
        }

        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-md-6 table-navigation-search">';

        $html[] = $this->renderNumberOfItemsPerPageSelector();
        $html[] = $this->renderPropertySorting();

        $html[] = '</div>';
        $html[] = '</div>';

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

    public function renderActions()
    {
        $formActions = $this->getTableFormActions()->get_form_actions();
        $firstAction = array_shift($formActions);

        $buttonToolBar = new ButtonToolBar();

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

        $button = new SplitDropdownButton(
            $firstAction->get_title(),
            null,
            $firstAction->get_action(),
            Button :: DISPLAY_LABEL,
            $firstAction->getConfirmation(),
            'btn-sm btn-table-action');
        $button->setDropdownClasses('btn-table-action');

        foreach ($formActions as $formAction)
        {
            $button->addSubButton(
                new SubButton(
                    $formAction->get_title(),
                    null,
                    $formAction->get_action(),
                    Button :: DISPLAY_LABEL,
                    $formAction->getConfirmation()));
        }

        $buttonToolBar->addItem($button);

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $html = array();

        $html[] = $buttonToolBarRenderer->render();
        $html[] = '<input type="hidden" name="' . $this->tableName . '_namespace" value="' .
             $this->getTableFormActions()->get_namespace() . '"/>';
        $html[] = '<input type="hidden" name="table_name" value="' . $this->tableName . '"/>';

        return implode(PHP_EOL, $html);
    }

    public function renderTableFooter()
    {
        $html = array();

        if ($this->allowPageSelection || $this->allowPageNavigation)
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-actions">';

            if ($this->getTableFormActions() instanceof TableFormActions &&
                 $this->getTableFormActions()->has_form_actions())
            {
                $html[] = $this->renderActions();
            }

            $html[] = '</div>';

            $queryParameters = array();
            $queryParameters[$this->getParameterName('direction')] = $this->getOrderDirection();
            $queryParameters[$this->getParameterName('per_page')] = $this->getNumberOfItemsPerPage();
            $queryParameters[$this->getParameterName('column')] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            $html[] = '<div class="col-xs-12 col-md-6 table-navigation-pagination">';
            $html[] = $this->getPagerRenderer()->renderPaginationWithPageLimit(
                $queryParameters,
                $this->getParameterName('page_nr'));
            $html[] = '</div>';

            $html[] = '</div>';
        }

        if ($this->getTableFormActions() instanceof TableFormActions && $this->getTableFormActions()->has_form_actions())
        {
            $html[] = '<input type="submit" name="Submit" value="Submit" style="display:none;" />';
            $html[] = '</form>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) . 'GalleryTable.js');
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the complete table HTML.
     *
     * @return string
     */
    public function toHtml($empty_table = false)
    {
        if ($this->countSourceData() == 0)
        {
            return $this->getEmptyTable();
        }

        $html = array();

        if (! $empty_table)
        {
            $html[] = $this->renderTableHeader();
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="table-gallery-container">';
        $html[] = $this->getBodyHtml();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        if (! $empty_table)
        {
            $html[] = $this->renderTableFooter();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     *
     * @return string
     */
    public function getNavigationHtml()
    {
        if ($this->allowPageNavigation)
        {
            $pager = $this->getPager();
            $pagerRenderer = new PagerRenderer($pager);

            if ($pager->getNumberOfPages() > 1)
            {
                return $pagerRenderer->renderPaginationWithPageLimit();
            }
        }
    }

    public function getPagerRenderer()
    {
        if (! isset($this->pagerRenderer))
        {
            $this->pagerRenderer = new PagerRenderer($this->getPager());
        }

        return $this->pagerRenderer;
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

        return parent :: toHTML();
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
     * Get the HTML-code wich represents a form to select how many items a page should contain.
     *
     * @return string
     */
    public function renderNumberOfItemsPerPageSelector()
    {
        if ($this->allowPageSelection)
        {
            $sourceDataCount = $this->countSourceData();

            if ($sourceDataCount <= Pager :: DISPLAY_PER_INCREMENT)
            {
                return '';
            }

            $queryParameters = array();
            $queryParameters[$this->getParameterName('direction')] = $this->getOrderDirection();
            $queryParameters[$this->getParameterName('page_nr')] = $this->getPageNumber();
            $queryParameters[$this->getParameterName('column')] = $this->getOrderColumn();
            $queryParameters = array_merge($queryParameters, $this->getAdditionalParameters());

            return $this->getPagerRenderer()->renderItemsPerPageSelector(
                $queryParameters,
                $this->getParameterName('per_page'));
        }

        return '';
    }

    /**
     *
     * @param integer $orderColumn
     * @param string $label
     * @param boolean $sortable
     * @param string[] $headerAttributes
     * @param string[] $cellAttributes
     * @return string
     */
    public function setColumnHeader($orderColumn, $label, $sortable = true, $headerAttributes = null, $cellAttributes = null)
    {
        $header = $this->getHeader();

        for ($i = 0; $i < count($headerAttributes); $i ++)
        {
            $header->setColAttributes($i, $headerAttributes[$i]);
        }

        $param['direction'] = SORT_ASC;

        if ($this->getOrderColumn() == $orderColumn && $this->getOrderDirection() == SORT_ASC)
        {
            $param['direction'] = SORT_DESC;
        }

        $param['page_nr'] = $this->getPageNumber();
        $param['per_page'] = $this->getNumberOfItemsPerPage();
        $param['column'] = $orderColumn;

        if ($sortable)
        {
            $link = '<a href="' . $_SERVER['PHP_SELF'] . '?';

            foreach ($param as $key => & $value)
            {
                $link .= $this->getParameterName($key) . '=' . urlencode($value) . '&amp;';
            }

            $link .= http_build_query($this->getAdditionalParameters(), '', Redirect :: ARGUMENT_SEPARATOR);
            $link .= '">' . $label . '</a>';

            if ($this->getOrderColumn() == $orderColumn)
            {
                $link .= $this->getOrderDirection() == SORT_ASC ? ' &#8595;' : ' &#8593;';
            }
        }
        else
        {
            $link = $label;
        }

        $header->setHeaderContents(0, $orderColumn, $link);

        if (! is_null($cellAttributes))
        {
            $this->contentCellAttributes[$orderColumn] = $cellAttributes;
        }

        if (! is_null($headerAttributes))
        {
            $this->headerAttributes[$orderColumn] = $headerAttributes;
        }

        return $link;
    }

    /**
     *
     * @return string
     */
    public function getParameterString()
    {
        $param = array();

        $param[$this->getParameterName('direction')] = $this->getOrderDirection();
        $param[$this->getParameterName('page_nr')] = $this->getPageNumber();
        $param[$this->getParameterName('per_page')] = $this->getNumberOfItemsPerPage();
        $param[$this->getParameterName('column')] = $this->getOrderColumn();

        $param_string_parts = array();

        foreach ($param as $key => & $value)
        {
            $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
        }

        return implode('&amp;', $param_string_parts);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\FormAction\TableFormActions $actions
     */
    public function setTableFormActions(TableFormActions $actions = null)
    {
        $this->tableFormActions = $actions;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    private function getTableFormActions()
    {
        return $this->tableFormActions;
    }

    /**
     *
     * @param string[]
     */
    public function setAdditionalParameters($parameters)
    {
        $this->additionalParameters = $parameters;
    }

    /**
     *
     * @return string[]
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
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

    /**
     *
     * @return integer
     */
    public function countSourceData()
    {
        if (is_null($this->sourceDataCount))
        {
            $this->sourceDataCount = call_user_func($this->getSourceCountFunction());
        }

        return $this->sourceDataCount;
    }

    /**
     * Get the data to display.
     * This function calls the function given as 2nd argument in the constructor of a
     * SortableTable. Make sure your function has the same parameters as defined here.
     *
     * @param integer
     * @return string[]
     */
    public function getSourceData($offset = null)
    {
        if (! is_null($this->getSourceDataFunction()))
        {
            if (is_null($this->sourceData))
            {
                $this->sourceData = call_user_func(
                    $this->getSourceDataFunction(),
                    $offset,
                    $this->getPager()->getNumberOfItemsPerPage(),
                    $this->getOrderColumn(),
                    $this->getOrderDirection());
            }

            return $this->sourceData;
        }

        return array();
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

    /**
     * Serializes a URL parameter passed as an array into a query string or hidden inputs.
     *
     * @param string[] The parameter's value.
     * @param string $key The parameter's name.
     * @param boolean $as_query_string True to format the result as a query string, false for hidden inputs.
     * @return string[] The query string parts (to be joined by ampersands or another separator), or the hidden inputs
     *         as HTML, each array element containing a single input.
     */
    private function serializeArray($params, $key, $as_query_string = false)
    {
        $out = array();

        foreach ($params as $k => & $v)
        {
            if (is_array($v))
            {
                $ser = $this->serializeArray($v, $key . '[' . $k . ']', $as_query_string);
                $out = array_merge($out, $ser);
            }
            else
            {
                $v = urlencode($v);
            }

            if ($as_query_string)
            {
                $k = urlencode($key . '[' . $k . ']');
                $out[] = $k . '=' . $v;
            }
            else
            {
                $k = $key . '[' . $k . ']';
                $out[] = '<input type="hidden" name="' . $k . '" value="' . $v . '"/>';
            }
        }

        return $out;
    }

    /**
     *
     * @return boolean
     */
    public function isPageSelectionAllowed()
    {
        return $this->allowPageSelection;
    }

    /**
     *
     * @return integer
     */
    public function getNumberOfItemsPerPage()
    {
        return $this->numberOfItemsPerPage;
    }

    /**
     *
     * @return integer
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     *
     * @return integer
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     *
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     *
     * @return string[]
     */
    public function getSourceDataFunction()
    {
        return $this->sourceDataFunction;
    }

    /**
     *
     * @return string[]
     */
    public function getSourceCountFunction()
    {
        return $this->sourceCountFunction;
    }

    public function getSourcePropertiesFunction()
    {
        return $this->sourcePropertiesFunction;
    }

    /**
     *
     * @return string[]
     */
    public function getContentCellAttributes()
    {
        return $this->contentCellAttributes;
    }

    /**
     *
     * @return string[]
     */
    public function getHeaderAttributes()
    {
        return $this->headerAttributes;
    }
}
