<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;

/**
 *
 * @author Hans De Bisschop
 */
class ColumnAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_TAB = 'tab';
    const PROPERTY_HTML = 'html';
    const PROPERTY_WIDTH = 'width';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_TAB);
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

        $post_tab = $this->getPostDataValue(self :: PARAM_TAB);

        if (isset($post_tab))
        {
            $tabId = $this->getPostDataValue(self :: PARAM_TAB);

            // Retrieve the columns of the current row to alter their width
//             $conditions = array();
//             $conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_PARENT_ID),
//                 new StaticConditionVariable($tabId));
//             $conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_USER_ID),
//                 new StaticConditionVariable($user_id));

//             $condition = new AndCondition($conditions);
//             $parameters = new DataClassRetrievesParameters($condition);
//             $columns = DataManager :: retrieves(Column :: class_name(), $parameters);

//             $width_conditions = array();
//             $width_conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_PARENT_ID),
//                 new StaticConditionVariable($tabId));
//             $width_conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_USER_ID),
//                 new StaticConditionVariable($user_id));
//             $width_condition = new AndCondition($width_conditions);
//             $columns_width = DataManager :: retrieves(
//                 Column :: class_name(),
//                 new DataClassRetrievesParameters($width_condition));

//             $width_total = $columns_width->size() - 1;
//             while ($col = $columns_width->next_result())
//             {
//                 $width_total += $col->getWidth();
//             }

            // Create the new column + a dummy block for it
            $new_column = new Column();
            $new_column->setParentId($tabId);
            $new_column->setTitle(Translation :: get('NewColumn'));
            $new_column->setWidth(2);
            $new_column->setUserId($user_id);

            if (! $new_column->create())
            {
                JsonAjaxResult :: general_error(Translation :: get('ColumnNotAdded'));
            }

            // Render the actual html to be displayed
            $html[] = '<div class="col-xs-12 col-md-' . $new_column->getWidth() . ' portal-column" data-tab-id="' . $tabId .
                 '" data-element-id="' . $new_column->get_id() . '">';

            $html[] = '<div class="panel panel-default portal-column-empty show">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<div class="pull-right">';

            $glyph = new BootstrapGlyph('remove');
            $delete_text = Translation :: get('Delete');

            $html[] = '<a href="#" class="portal-action portal-action-column-delete" data-column-id="' .
                 $new_column->get_id() . ' title="' . $delete_text . '">';
            $html[] = $glyph->render() . '</a>';

            $html[] = '</div>';
            $html[] = '<h3 class="panel-title">' . Translation :: get('EmptyColumnTitle') . '</h3>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = Translation :: get('EmptyColumnBody');
            $html[] = '</div>';
            $html[] = '</div>';

            $html[] = '</div>';

            // Update the older columns width and add them to the JSON object
//             $border = 1;
//             $free_width = max(100 - $width_total, 0);
//             $width_to_remove = max(20 - $free_width, 0);

//             while ($column = $columns->next_result())
//             {
//                 if ($width_to_remove > 0)
//                 {
//                     $delta = max($column->getWidth() - 19, 0);
//                     $delta = min($width_to_remove, $delta);
//                     if ($delta > 0)
//                     {
//                         $column->setWidth($column->getWidth() - $delta);
//                         $column->update();
//                         $width_to_remove = max($width_to_remove - $delta - $border, 0);
//                     }
//                 }
//             }

//             $width_conditions = array();
//             $width_conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_PARENT_ID),
//                 new StaticConditionVariable($tabId));
//             $width_conditions[] = new EqualityCondition(
//                 new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_USER_ID),
//                 new StaticConditionVariable($user_id));
//             $width_condition = new AndCondition($width_conditions);

//             $columns_width = DataManager :: retrieves(
//                 Element :: class_name(),
//                 new DataClassRetrievesParameters($width_condition));
//             $widths = array();

//             while ($col = $columns_width->next_result())
//             {
//                 $widths['portal_column_' . $col->getId()] = $col->getWidth();
//             }

            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PROPERTY_HTML, implode(PHP_EOL, $html));
//             $result->set_property(self :: PROPERTY_WIDTH, $widths);
            $result->display();
        }
        else
        {
            JsonAjaxResult :: bad_request();
        }
    }
}
