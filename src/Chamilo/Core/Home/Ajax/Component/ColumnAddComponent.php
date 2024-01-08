<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Hans De Bisschop
 */
class ColumnAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_TAB = 'tab';
    const PROPERTY_HTML = 'html';
    const PROPERTY_WIDTH = 'width';

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Column[]
     */
    private $columns;

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TAB);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $userId = DataManager::determine_user_id();

        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();
        }

        $tabId = $this->getPostDataValue(self::PARAM_TAB);

        if (isset($tabId))
        {
            if (count($this->getColumns()) >= 12)
            {
                JsonAjaxResult::general_error(Translation::get('TooManyColumns'));
            }

            try
            {
                $newColumnWidth = $this->determineNewColumnWidth();
            }
            catch (\Exception $exception)
            {
                $newColumnWidth = 1;
                $newWidths = $this->recalculateColumnWidths();
            }

            // Create the new column + a dummy block for it
            $newColumn = new Column();
            $newColumn->setParentId($tabId);
            $newColumn->setTitle(Translation::get('NewColumn'));
            $newColumn->setWidth($newColumnWidth);
            $newColumn->setUserId($userId);

            if (! $newColumn->create())
            {
                JsonAjaxResult::general_error(Translation::get('ColumnNotAdded'));
            }

            // Render the actual html to be displayed
            $html[] = '<div class="col col-md-' . $newColumn->getWidth() . ' portal-column" data-tab-id="' . $tabId .
                 '" data-element-id="' . $newColumn->get_id() . '">';

            $html[] = '<div class="panel panel-warning portal-column-empty show">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<div class="pull-right">';

            $glyph = new FontAwesomeGlyph('times');

            $html[] = '<a href="#" class="portal-action portal-action-column-delete show" data-column-id="' .
                 $newColumn->get_id() . ' title="' . Translation::get('Delete') . '">';
            $html[] = $glyph->render() . '</a>';

            $html[] = '</div>';
            $html[] = '<h3 class="panel-title">' . Translation::get('EmptyColumnTitle') . '</h3>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = Translation::get('EmptyColumnBody');
            $html[] = '</div>';
            $html[] = '</div>';

            $html[] = '</div>';

            $result = new JsonAjaxResult(200);
            $result->set_property(self::PROPERTY_HTML, implode(PHP_EOL, $html));

            if (isset($newWidths))
            {
                $result->set_property(self::PROPERTY_WIDTH, $newWidths);
            }

            $result->display();
        }
        else
        {
            JsonAjaxResult::bad_request();
        }
    }

    public function recalculateColumnWidths()
    {
        $currentTotal = $this->getCurrentTotalWidth();
        $columns = $this->getColumns();
        $newWidths = array();

        foreach ($columns as $column)
        {
            $newWidths[$column->getId()] = $column->getWidth();
        }

        while ($currentTotal > 11)
        {
            arsort($newWidths);

            foreach ($newWidths as $columnId => $newWidth)
            {
                $newWidths[$columnId] = $newWidth - 1;
                $currentTotal --;

                break;
            }
        }

        foreach ($columns as $column)
        {
            $column->setWidth($newWidths[$column->getId()]);
            $column->update();
        }

        return $newWidths;
    }

    public function orderColumnsByWidth($widthLeft, $widthRight)
    {
        if ($widthLeft < $widthRight)
        {
            return - 1;
        }
        elseif ($widthLeft > $widthRight)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     *
     * @return integer
     */
    public function getCurrentTotalWidth()
    {
        $widthTotal = 0;

        foreach ($this->getColumns() as $column)
        {
            $widthTotal += $column->getWidth();
        }

        return $widthTotal;
    }

    /**
     *
     * @return integer
     */
    public function determineNewColumnWidth()
    {
        $widthTotal = $this->getCurrentTotalWidth();

        if ($widthTotal < 12)
        {
            return 12 - $widthTotal;
        }
        else
        {
            throw new \Exception('ColumnsTooWide');
        }
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Column[]
     */
    public function getColumns()
    {
        if (! isset($this->columns))
        {
            $tabId = $this->getPostDataValue(self::PARAM_TAB);
            $userId = DataManager::determine_user_id();

            $conditions = array();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_PARENT_ID),
                new StaticConditionVariable($tabId));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_USER_ID),
                new StaticConditionVariable($userId));

            $parameters = new DataClassRetrievesParameters(new AndCondition($conditions));
            $this->columns = DataManager::retrieves(Column::class_name(), $parameters)->as_array();
        }

        return $this->columns;
    }
}
