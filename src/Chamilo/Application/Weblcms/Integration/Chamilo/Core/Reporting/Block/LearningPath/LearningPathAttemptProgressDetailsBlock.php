<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component\StatisticsViewerComponent;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\String\Text;

class LearningPathAttemptProgressDetailsBlock extends ToolBlock
{

    public function get_attempt_id()
    {
        return $this->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID);
    }

    public function get_parent_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('LastStartTime'), 
                Translation::get('Status'), 
                Translation::get('Score'), 
                Translation::get('Time')));
        if ($this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION) ==
             \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_STATISTICS)
        {
            $reporting_data->add_row(Translation::get('Action'));
        }
        
        $parent_id = $this->get_parent_id();
        
        $attempt_id = $this->get_attempt_id();
        $tracker = $this->retrieve_tracker($attempt_id);
        $attempt_data = $this->retrieve_tracker_items($tracker);
        
        $data = array();
        
        $total = 0;
        
        $tracker_datas = $attempt_data[$parent_id];
        
        foreach ($tracker_datas['trackers'] as $index => $tracker)
        {
            $data[Translation::get('LastStartTime')] = DatetimeUtilities::format_locale_date(
                null, 
                $tracker->get_start_time());
            $data[Translation::get('Status')] = Translation::get(
                $tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete');
            $data[Translation::get('Score')] = $tracker->get_score() . '%';
            $data[Translation::get('Time')] = DatetimeUtilities::format_seconds_to_hours($tracker->get_total_time());
            $total += $tracker->get_total_time();
            
            $category_name = ($index + 1);
            $reporting_data->add_category($category_name);
            $reporting_data->add_data_category_row(
                $category_name, 
                Translation::get('LastStartTime'), 
                DatetimeUtilities::format_locale_date(null, $tracker->get_start_time()));
            $reporting_data->add_data_category_row(
                $category_name, 
                Translation::get('Status'), 
                Translation::get($tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete'));
            $reporting_data->add_data_category_row(
                $category_name, 
                Translation::get('Score'), 
                $tracker->get_score() . '%');
            $reporting_data->add_data_category_row(
                $category_name, 
                Translation::get('Time'), 
                DatetimeUtilities::format_seconds_to_hours($tracker->get_total_time()));
            
            if ($this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION) ==
                 \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_STATISTICS)
            {
                $params = $this->get_parent()->get_parameters();
                $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
                $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
                $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_STATISTICS;
                $params[StatisticsViewerComponent::PARAM_STAT] = StatisticsViewerComponent::ACTION_DELETE_LPI_ATTEMPT;
                $params[StatisticsViewerComponent::PARAM_DELETE_ID] = $tracker->get_id();
                
                $redirect = new Redirect($params);
                $url = $redirect->getUrl();
                
                $reporting_data->add_data_category_row(
                    $category_name, 
                    Translation::get('Action'), 
                    Text::create_link($url, Theme::getInstance()->getCommonImage('Action/Delete')));
            }
            // $i++;
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
            '<span style="font-weight: bold;">' . DatetimeUtilities::format_seconds_to_hours($total) . '</span>');
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    private function retrieve_tracker()
    {
        $attempt_id = $this->get_attempt_id();
        
        if ($this->get_attempt_id())
        {
            return \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                LearningPathAttempt::class_name(), 
                $attempt_id);
        }
        else
        {
            $pid = $this->get_publication_id();
            
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(), 
                $pid);
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    LearningPathAttempt::class_name(), 
                    LearningPathAttempt::PROPERTY_COURSE_ID), 
                new StaticConditionVariable($this->get_course_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    LearningPathAttempt::class_name(), 
                    LearningPathAttempt::PROPERTY_LEARNING_PATH_ID), 
                new StaticConditionVariable($publication->get_content_object_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(LearningPathAttempt::class_name(), LearningPathAttempt::PROPERTY_USER_ID), 
                new StaticConditionVariable($this->get_parent()->get_parent()->get_user_id()));
            $condition = new AndCondition($conditions);
            
            $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve(
                LearningPathAttempt::class_name(), 
                new DataClassRetrieveParameters($condition));
            
            if (! $attempt instanceof LearningPathAttempt)
            {
                $attempt = new LearningPathAttempt();
                $attempt->set_user_id($this->get_parent()->get_parent()->get_user_id());
                $attempt->set_course_id($this->get_course_id());
                $attempt->set_learning_path_id($publication->get_content_object_id());
                $attempt->create();
            }
            
            return $attempt;
        }
    }

    private function retrieve_tracker_items($learning_path_attempt)
    {
        $lpi_attempt_data = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(), 
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID), 
            new StaticConditionVariable($learning_path_attempt->get_id()));
        
        $item_attempts = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
            LearningPathItemAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($item_attempt = $item_attempts->next_result())
        {
            $item_id = $item_attempt->get_learning_path_item_id();
            if (! $lpi_attempt_data[$item_id])
            {
                $lpi_attempt_data[$item_id]['score'] = 0;
                $lpi_attempt_data[$item_id]['time'] = 0;
            }
            
            $lpi_attempt_data[$item_id]['trackers'][] = $item_attempt;
            $lpi_attempt_data[$item_id]['size'] ++;
            $lpi_attempt_data[$item_id]['score'] += $item_attempt->get_score();
            
            if ($item_attempt->get_total_time())
            {
                $lpi_attempt_data[$item_id]['time'] += $item_attempt->get_total_time();
            }
            
            if ($item_attempt->get_status() == LearningPathItemAttempt::STATUS_COMPLETED)
            {
                $lpi_attempt_data[$item_id]['completed'] = 1;
            }
            else
            {
                $lpi_attempt_data[$item_id]['active_tracker'] = $item_attempt;
            }
        }
        return $lpi_attempt_data;
    }
}
