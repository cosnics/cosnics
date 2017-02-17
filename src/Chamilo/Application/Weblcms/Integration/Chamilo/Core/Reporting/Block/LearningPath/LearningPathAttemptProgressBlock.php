<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component\StatisticsViewerComponent;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

class LearningPathAttemptProgressBlock extends ToolBlock
{

    public function get_attempt_id()
    {
        return $this->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows(
            array(
                Translation::get('Type'), 
                Translation::get('Title'), 
                Translation::get('Status'), 
                Translation::get('Score'), 
                Translation::get('Time'), 
                Translation::get('Action')));
        
        $tracker = $this->retrieve_tracker();
        $attempt_data = $this->retrieve_tracker_items($tracker);
        $pid = $this->get_publication_id();
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $pid);
        
        $menu = new LearningPathTree($publication->get_content_object_id(), null, null, $attempt_data);
        $objects = $menu->get_objects();

        /** @var LearningPath $contentObject */
        $contentObject = $publication->get_content_object();
        $learningPathComplexContentObjectPath = $contentObject->get_complex_content_object_path($attempt_data);

        $counter = 1;
        $total = 0;
        
        foreach($learningPathComplexContentObjectPath->get_nodes() as $node)
        {
            $object = $node->get_content_object();
            $wrapper_id = $node->get_complex_content_object_item()->getId();

            $tracker_data = $attempt_data[$wrapper_id];
            
            if ($object instanceof Assessment)
            {
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $wrapper_id;
                Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS) ? $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = Request::get(
                    \Chamilo\Application\Weblcms\Manager::PARAM_USERS) : $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = Session::get_user_id();
                Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID) ? $params[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID] = Request::get(
                    \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID) : $params[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID] = $tracker->get_id();
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ASSESSMENT_ID] = $object->get_id();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                    LearningPath::class_name());
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_ASSESSMENT_RESULTS;
                
                $redirect = new Redirect(
                    $params, 
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID, 
                        \Chamilo\Core\Reporting\Viewer\Manager::PARAM_ACTION));
                $assessment_url = $redirect->getUrl();
                
                $title = '<a href="' . $assessment_url . '">' . $object->get_title() . '</a>';
            }
            else
            {
                $title = $object->get_title();
            }
            
            $category = $counter;
            $reporting_data->add_category($category);
            
            if ($tracker_data)
            {
                $status = Translation::get($tracker_data['completed'] ? 'Completed' : 'Incomplete');
                $score = round($tracker_data['score'] / $tracker_data['size']) . '%';
                $time = DatetimeUtilities::format_seconds_to_hours($tracker_data['time']);
                $total += $tracker_data['time'];
            }
            else
            {
                $status = Translation::get('Incomplete');
                $score = '0%';
                $time = '0:00:00';
            }
            
            $reporting_data->add_data_category_row($category, Translation::get('Type'), $object->get_icon_image());
            $reporting_data->add_data_category_row($category, Translation::get('Title'), $title);
            $reporting_data->add_data_category_row($category, Translation::get('Status'), $status);
            $reporting_data->add_data_category_row($category, Translation::get('Score'), $score);
            $reporting_data->add_data_category_row($category, Translation::get('Time'), $time);
            
            $actions = array();
            
            if ($this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION) ==
                 \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_STATISTICS)
            {
                $params = array_merge(
                    $this->get_parent()->get_parameters(), 
                    $this->get_parent()->get_parent()->get_parameters());
                $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
                $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
                $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_VIEW_STATISTICS;
                $params[StatisticsViewerComponent::PARAM_STAT] = StatisticsViewerComponent::ACTION_DELETE_LPI_ATTEMPTS;
                $params[StatisticsViewerComponent::PARAM_ITEM_ID] = $wrapper_id;
                
                $redirect = new Redirect($params);
                $url = $redirect->getUrl();
                
                $actions[] = Text::create_link(
                    $url, 
                    Theme::getInstance()->getCommonImage(
                        'Action/Delete', 
                        'png', 
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
            
            if ($object instanceof Assessment)
            {
                $actions[] = Text::create_link(
                    $assessment_url, 
                    Theme::getInstance()->getCommonImage(
                        'Action/Reporting', 
                        'png', 
                        Translation::get('Details'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
            
            $reporting_data->add_data_category_row($category, Translation::get('Action'), implode(' ', $actions));
            
            $counter ++;
        }
        
        $category_name = '-';
        $reporting_data->add_category($category_name);
        $reporting_data->add_data_category_row($category_name, Translation::get('Title'), '');
        $reporting_data->add_data_category_row(
            $category_name, 
            Translation::get('Status'), 
            '<span style="font-weight: bold;">' . Translation::get('TotalTime') . '</span>');
        $reporting_data->add_data_category_row($category_name, Translation::get('Score'), '');
        $reporting_data->add_data_category_row(
            $category_name, 
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
            return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
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
                new StaticConditionVariable($publication->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(LearningPathAttempt::class_name(), LearningPathAttempt::PROPERTY_USER_ID), 
                new StaticConditionVariable($this->get_parent()->user_id));
            $condition = new AndCondition($conditions);
            
            $attempt = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve(
                LearningPathAttempt::class_name(), 
                new DataClassRetrieveParameters($condition));
            
            if (! $attempt)
            {
                $attempt = new LearningPathAttempt();
                $attempt->set_user_id($this->get_parent()->user_id);
                $attempt->set_course_id($this->get_course_id());
                $attempt->set_learning_path_id($publication->get_content_object_id());
                $attempt->create();
            }
            
            return $attempt;
        }
    }

    private function retrieve_tracker_items($attempt)
    {
        $item_attempt_data = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(), 
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID), 
            new StaticConditionVariable($attempt->get_id()));
        
        $item_attempts = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            LearningPathItemAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($item_attempt = $item_attempts->next_result())
        {
            $item_id = $item_attempt->get_learning_path_item_id();
            
            if (! $item_attempt_data[$item_id])
            {
                $item_attempt_data[$item_id]['score'] = 0;
                $item_attempt_data[$item_id]['time'] = 0;
            }
            
            $item_attempt_data[$item_id]['trackers'][] = $item_attempt;
            $item_attempt_data[$item_id]['size'] ++;
            $item_attempt_data[$item_id]['score'] += $item_attempt->get_score();
            
            if ($item_attempt->get_total_time())
            {
                $item_attempt_data[$item_id]['time'] += $item_attempt->get_total_time();
            }
            
            if ($item_attempt->get_status() == LearningPathItemAttempt::STATUS_COMPLETED)
            {
                $item_attempt_data[$item_id]['completed'] = 1;
            }
            else
            {
                $item_attempt_data[$item_id]['active_tracker'] = $item_attempt;
            }
        }
        return $item_attempt_data;
    }
}
