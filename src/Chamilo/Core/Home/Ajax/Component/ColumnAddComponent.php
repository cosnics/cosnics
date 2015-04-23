<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Hans De Bisschop
 */
class ColumnAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_ROW = 'row';
    const PROPERTY_HTML = 'html';
    const PROPERTY_WIDTH = 'width';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_ROW);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $user_id = DataManager :: determine_user_id();

        if ($user_id === false)
        {
            JsonAjaxResult :: not_allowed();
        }

        $post_row = $this->getPostDataValue(self :: PARAM_ROW);

        if (isset($post_row))
        {
            $row_data = explode('_', $this->getPostDataValue(self :: PARAM_ROW));
            $row_id = $row_data[2];

            // Retrieve the columns of the current row to alter their width
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW),
                new StaticConditionVariable($row_id));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_USER),
                new StaticConditionVariable($user_id));

            $condition = new AndCondition($conditions);
            $parameters = new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                new OrderBy(new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_WIDTH)));
            $columns = DataManager :: retrieves(Column :: class_name(), $parameters);

            $width_conditions = array();
            $width_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW),
                new StaticConditionVariable($row_id));
            $width_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_USER),
                new StaticConditionVariable($user_id));
            $width_condition = new AndCondition($width_conditions);
            $columns_width = DataManager :: retrieves(Column :: class_name(), $width_condition);

            $width_total = $columns_width->size() - 1;
            while ($col = $columns_width->next_result())
            {
                $width_total += $col->get_width();
            }

            // Create the new column + a dummy block for it
            $new_column = new Column();
            $new_column->set_row($row_id);
            $new_column->set_title(Translation :: get('NewColumn'));
            $new_column->set_width('19');
            $new_column->set_user($user_id);
            if (! $new_column->create())
            {
                JsonAjaxResult :: general_error(Translation :: get('ColumnNotAdded'));
            }

            // Render the actual html to be displayed
            $html[] = '<div class="portal_column" id="portal_column_' . $new_column->get_id() . '" style="width: ' .
                 $new_column->get_width() . '%;">';

            $html[] = '<div class="empty_portal_column" style="display:block;">';
            $html[] = htmlspecialchars(Translation :: get('EmptyColumnText'));
            $img = Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Action/RemoveColumn');
            $html[] = '<div class="deleteColumn"><a href="#"><img src="' . $img . '" alt="' .
                 Translation :: get('RemoveColumn') . '"/></a></div>';
            $html[] = '</div>';
            $html[] = '</div>';

            // Update the older columns width and add them to the JSON object
            $border = 1;
            $free_width = max(100 - $width_total, 0);
            $width_to_remove = max(20 - $free_width, 0);

            while ($column = $columns->next_result())
            {
                if ($width_to_remove > 0)
                {
                    $delta = max($column->get_width() - 19, 0);
                    $delta = min($width_to_remove, $delta);
                    if ($delta > 0)
                    {
                        $column->set_width($column->get_width() - $delta);
                        $column->update();
                        $width_to_remove = max($width_to_remove - $delta - $border, 0);
                    }
                }
            }

            $width_conditions = array();
            $width_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW),
                new StaticConditionVariable($row_id));
            $width_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_USER),
                new StaticConditionVariable($user_id));
            $width_condition = new AndCondition($width_conditions);

            $columns_width = DataManager :: retrieves(Column :: class_name(), $width_condition);
            $widths = array();
            while ($col = $columns_width->next_result())
            {
                $widths['portal_column_' . $col->get_id()] = $col->get_width();
            }

            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PROPERTY_HTML, implode(PHP_EOL, $html));
            $result->set_property(self :: PROPERTY_WIDTH, $widths);
            $result->display();
        }
        else
        {
            JsonAjaxResult :: bad_request();
        }
    }
}
