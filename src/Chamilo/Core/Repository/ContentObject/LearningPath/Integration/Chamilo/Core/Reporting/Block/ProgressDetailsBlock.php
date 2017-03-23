<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package core\repository\content_object\learning_path\display\integration\core\reporting
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProgressDetailsBlock extends ReportingBlock
{

    /**
     *
     * @see \core\reporting\ReportingBlock::count_data()
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows(
            array(
                Translation::get('LastStartTime'), 
                Translation::get('Status'), 
                Translation::get('Score'), 
                Translation::get('Time'), 
                Translation::get('Action')));
        
        $current_node = $this->get_parent()->get_parent()->get_current_node();
        
        $counter = 1;
        
        foreach ($current_node->get_data() as $item_attempt)
        {
            $category = $counter;
            $reporting_data->add_category($category);
            $reporting_data->add_data_category_row(
                $category, 
                Translation::get('LastStartTime'), 
                DatetimeUtilities::format_locale_date(null, $item_attempt->get_start_time()));
            $reporting_data->add_data_category_row(
                $category, 
                Translation::get('Status'), 
                Translation::get($item_attempt->get_status() == 'completed' ? 'Completed' : 'Incomplete'));
            $reporting_data->add_data_category_row(
                $category, 
                Translation::get('Score'), 
                $item_attempt->get_score() . '%');
            $reporting_data->add_data_category_row(
                $category, 
                Translation::get('Time'), 
                DatetimeUtilities::format_seconds_to_hours($item_attempt->get_total_time()));
            
            if ($this->get_parent()->get_parent()->is_allowed_to_edit_attempt_data())
            {
                $delete_url = $this->get_parent()->get_parent()->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_ATTEMPT, 
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID => $current_node->getId(),
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ITEM_ATTEMPT_ID => $item_attempt->get_id()));
                
                $action = Theme::getInstance()->getCommonImage(
                    'Action/Delete', 
                    'png', 
                    Translation::get('DeleteAttempt'), 
                    $delete_url, 
                    ToolbarItem::DISPLAY_ICON);
                
                $reporting_data->add_data_category_row($category, Translation::get('Action'), $action);
            }
            
            $counter ++;
        }
        
        $category = '-';
        $reporting_data->add_category($category);
        $reporting_data->add_data_category_row($category, Translation::get('LastStartTime'), '');
        $reporting_data->add_data_category_row(
            $category, 
            Translation::get('Status'), 
            '<span style="font-weight: bold;">' . Translation::get('TotalTime') . '</span>');
        $reporting_data->add_data_category_row($category, Translation::get('Score'), '');
        $reporting_data->add_data_category_row(
            $category, 
            Translation::get('Time'), 
            '<span style="font-weight: bold;">' .
                 DatetimeUtilities::format_seconds_to_hours($current_node->get_total_time()) . '</span>');
        
        return $reporting_data;
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::retrieve_data()
     */
    public function retrieve_data()
    {
        return $this->count_data();
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::get_views()
     */
    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
