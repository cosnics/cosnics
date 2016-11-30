<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseAssignmentSubmittersTemplate;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying all assigments within a course and their
 *          submission stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssignmentBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Title'), 
                Translation::get('NumberOfSubmissions'), 
                Translation::get('LastSubmission'), 
                Translation::get('AverageScore'), 
                Translation::get('AssignmentDetails')));
        
        $course_id = $this->get_course_id();
        $tool = ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assignment::class_name(), true);
        $submissions_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
        $score_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();
        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Statistics') . '" title="' .
             Translation::get('Details') . '" />';
        $count = 1;
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable($tool));
        $condition = new AndCondition($conditions);
        
        $pub_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition);
        
        while ($pub = $pub_resultset->next_result())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($pub[ContentObjectPublication::PROPERTY_ID]));
            
            $submissions = DataManager::retrieves(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                new DataClassRetrievesParameters($condition))->as_array();
            
            $score = $score_count = $last_submission = null;
            
            foreach ($submissions as $submission)
            {
                if ($submission->get_date_submitted() > $last_submission)
                {
                    $last_submission = $submission->get_date_submitted();
                }
                
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
                    new StaticConditionVariable($submission->get_id()));
                
                $result = DataManager::retrieve(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                    new DataClassRetrieveParameters($condition));
                
                if ($result)
                {
                    $score += $result->get_score();
                    $score_count ++;
                }
            }
            if ($last_submission != null)
            {
                $last_submission = DatetimeUtilities::format_locale_date(
                    Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
                         Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES), 
                        $last_submission);
            }
            if ($score != null)
            {
                $score = $this->get_score_bar($score / $score_count);
            }
            
            $params = $this->get_parent()->get_parameters();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = CourseAssignmentSubmittersTemplate::class_name();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $pub[ContentObjectPublication::PROPERTY_ID];
            $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';
            
            $params = array();
            $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $tool;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $pub[ContentObjectPublication::PROPERTY_ID];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_BROWSE_SUBMITTERS;
            
            $redirect = new Redirect($params);
            $url_title = $redirect->getUrl();
            
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $pub[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            
            $reporting_data->add_category($count);
            $reporting_data->add_data_category_row(
                $count, 
                Translation::get('Title'), 
                '<a href="' . $url_title . '">' . $content_object->get_title() . '</a>');
            $reporting_data->add_data_category_row($count, Translation::get('NumberOfSubmissions'), count($submissions));
            $reporting_data->add_data_category_row($count, Translation::get('LastSubmission'), $last_submission);
            $reporting_data->add_data_category_row($count, Translation::get('AverageScore'), $score);
            $reporting_data->add_data_category_row($count, Translation::get('AssignmentDetails'), $link);
            
            $count ++;
        }
        
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
}
