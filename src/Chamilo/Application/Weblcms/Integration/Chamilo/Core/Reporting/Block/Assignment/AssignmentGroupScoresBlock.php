<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of scores of each assigment per
 *          group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
abstract class AssignmentGroupScoresBlock extends AssignmentReportingManager
{

    private $reporting_data;

    public function count_data()
    {
        if (! isset($this->reporting_data))
        {
            $this->reporting_data = new ReportingData();
            
            $this->course_id = $this->get_course_id();
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), 
                    ContentObjectPublication::PROPERTY_COURSE_ID), 
                new StaticConditionVariable($this->course_id));
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), 
                    ContentObjectPublication::PROPERTY_TOOL), 
                new StaticConditionVariable(
                    ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assignment::class_name())));
            
            $condition = new AndCondition($conditions);
            $order_by = new OrderBy(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), 
                    ContentObjectPublication::PROPERTY_MODIFIED_DATE));
            $publication_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
                $condition, 
                $order_by);
            
            $publications = array();
            // fill publications with group assignments
            while ($publication = $publication_resultset->next_result())
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
                
                $method = 'get_allow_group_submissions';
                if (method_exists($content_object, $method) && $content_object->$method())
                {
                    $publications[] = $publication;
                }
            }
            
            // set the table headers
            $headings = array();
            $headings[] = Translation::get('Name');
            
            foreach ($publications as $publication)
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
                
                if (count($publications) > 5)
                {
                    $headings[] = '<div id="' . $publication[ContentObjectPublication::PROPERTY_ID] . '">' .
                         substr($content_object->get_title(), 0, 14) . '</div>';
                }
                else
                {
                    $headings[] = '<div id="' . $publication[ContentObjectPublication::PROPERTY_ID] . '">' .
                         $content_object->get_title() . '</div>';
                }
            }
            $this->reporting_data->set_rows($headings);
            
            $groups = $this->get_groups();
            // traverse groups
            foreach ($groups as $key => $group)
            {
                $this->reporting_data->add_category($key);
                $this->reporting_data->add_data_category_row($key, Translation::get('Name'), $group->get_name());
                
                foreach ($publications as $publication)
                {
                    $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                        ContentObject::class_name(), 
                        $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
                    
                    if (count($publications) > 5)
                    {
                        $title = '<div id="' . $publication[ContentObjectPublication::PROPERTY_ID] . '">' .
                             substr($content_object->get_title(), 0, 14) . '</div>';
                    }
                    else
                    {
                        $title = '<div id="' . $publication[ContentObjectPublication::PROPERTY_ID] . '">' .
                             $content_object->get_title() . '</div>';
                    }
                    
                    $submission_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
                    $conditions = array();
                    
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
                        new StaticConditionVariable($publication[ContentObjectPublication::PROPERTY_ID]));
                    
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
                        new StaticConditionVariable($this->get_current_submitter_type()));
                    
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
                        new StaticConditionVariable($group->get_id()));
                    
                    $condition = new AndCondition($conditions);
                    
                    $submissions_by_group = DataManager::retrieves(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                        new DataClassRetrievesParameters($condition))->as_array();
                    
                    $submission_ids = array();
                    foreach ($submissions_by_group as $submission)
                    {
                        $submission_ids[] = $submission->get_id();
                    }
                    
                    if (count($submission_ids) == 0)
                    {
                        if ($this->is_publication_target_group(
                            $group->get_id(), 
                            $publication[ContentObjectPublication::PROPERTY_ID]))
                        {
                            $this->reporting_data->add_data_category_row($key, $title, null);
                            continue;
                        }
                        
                        $this->reporting_data->add_data_category_row($key, $title, 'X');
                        continue;
                    }
                    
                    $score_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();
                    $condition = new InCondition(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
                        $submission_ids);
                    
                    $score_trackers = DataManager::retrieves(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                        new DataClassRetrievesParameters($condition))->as_array();
                    
                    if (count($score_trackers) > 0)
                    {
                        $score = $this->format_score_html($this->get_score($score_trackers));
                        
                        $this->reporting_data->add_data_category_row($key, $title, $score);
                    }
                    else
                    {
                        $params = array();
                        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->course_id;
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                            Assignment::class_name(), 
                            true);
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_BROWSE_SUBMISSIONS;
                        $params[\Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE] = ContentObjectRenderer::TYPE_TABLE;
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publication[ContentObjectPublication::PROPERTY_ID];
                        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] = $group->get_id();
                        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] = $this->get_current_submitter_type();
                        
                        $redirect = new Redirect($params);
                        $link = $redirect->getUrl();
                        
                        $this->reporting_data->add_data_category_row(
                            $key, 
                            $title, 
                            '<span style="text-decoration: blink;"><b><a href="' . $link . '">?</a></b></span>');
                    }
                }
            }
            
            $this->reporting_data->hide_categories();
        }
        
        return $this->reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    /**
     * This function should return the submitter type as an int.
     */
    abstract public function get_current_submitter_type();

    /**
     * This function should return the groups as an array.
     */
    abstract public function get_groups();

    /**
     * This function check whether the group with the given group id belongs to the given target entities.
     */
    abstract public function is_target_entity_group($target_entities, $group_id);

    private function is_publication_target_group($group_id, $pub_id)
    {
        $target_entities = WeblcmsRights::getInstance()->get_target_entities(
            WeblcmsRights::VIEW_RIGHT, 
            \Chamilo\Application\Weblcms\Manager::context(), 
            $pub_id, 
            WeblcmsRights::TYPE_PUBLICATION, 
            $this->course_id, 
            WeblcmsRights::TREE_TYPE_COURSE);
        
        if ($target_entities[0] || count($target_entities) == 0)
        {
            return true;
        }
        
        return $this->is_target_entity_group($target_entities, $group_id);
    }

    /**
     * Returns the score from the given score trackers.
     * The type of score is determined by the request. If no score type
     * is found, it will return the average score.
     * 
     * @param $score_trackers \application\weblcms\integration\core\tracking\tracker\SubmissionScore The score trackers
     *        used to determine the score
     * @return int The score
     */
    private function get_score($score_trackers)
    {
        $score_type = (Request::post('sel')) ? Request::post('sel') : Request::get('sel');
        
        return $this->get_score_by_type($score_trackers, $score_type);
    }
}
