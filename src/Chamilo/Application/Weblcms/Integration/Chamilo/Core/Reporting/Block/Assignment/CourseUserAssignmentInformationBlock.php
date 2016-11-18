<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseSubmitterSubmissionsTemplate;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of the assignments the user has
 *          sent a submission for
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class CourseUserAssignmentInformationBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Title'), 
                Translation::get('NumberOfSubmissions'), 
                Translation::get('LastSubmission'), 
                Translation::get('NumberOfFeedbacks'), 
                Translation::get('AverageScore'), 
                Translation::get('Submissions')));
        
        $user_id = $this->get_user_id();
        $subm_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
            new StaticConditionVariable($user_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER));
        
        $condition = new AndCondition($conditions);
        
        $submissions = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            new DataClassRetrievesParameters($condition))->as_array();
        
        $feedback_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();
        $score_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();
        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation::get('Details') . '" />';
        
        $pub_submissions = array();
        
        foreach ($submissions as $key => $submission)
        {
            $pub_submissions[$submission->get_publication_id()]['count'] ++;
            $pub_submissions[$submission->get_publication_id()]['subm_ids'][] = $submission->get_id();
            
            if ($pub_submissions[$submission->get_publication_id()]['last'] == null ||
                 $submission->get_date_submitted() > $pub_submissions[$submission->get_publication_id()]['last'])
            {
                $pub_submissions[$submission->get_publication_id()]['last'] = $submission->get_date_submitted();
            }
        }
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable('Assignment'));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable(
                $this->get_parent()->get_parent()->get_parent()->get_parameter(
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE)));
        $condition = new AndCondition($conditions);
        $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition);
        
        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->get_parent()->get_parent()->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = Assignment::get_type_name();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_BROWSE_SUBMITTERS;
        
        $params_detail = $this->get_parent()->get_parameters();
        $params_detail[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = CourseSubmitterSubmissionsTemplate::class_name();
        $params_detail[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] = $user_id;
        $params_detail[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER;
        
        $key = 0;
        while ($publication = $publications_resultset->next_result())
        {
            if (! \Chamilo\Application\Weblcms\Storage\DataManager::is_publication_target_user(
                $user_id, 
                $publication[ContentObjectPublication::PROPERTY_ID]))
            {
                continue;
            }
            ++ $key;
            $feedback_count = $score_display = $last = $link = null;
            
            $assignment = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publication[ContentObjectPublication::PROPERTY_ID];
            
            $redirect = new Redirect($params);
            $url_title = $redirect->getUrl();
            
            if ($pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]])
            {
                
                $last = DatetimeUtilities::format_locale_date(
                    Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
                         Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES), 
                        $pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]]['last']);
                
                $params_detail[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publication[ContentObjectPublication::PROPERTY_ID];
                $link = '<a href="' . $this->get_parent()->get_url($params_detail) . '">' . $img . '</a>';
                
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID), 
                    $pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]]['subm_ids']);
                
                $feedback_count = DataManager::count(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                    new DataClassCountParameters($condition));
                
                $score_display = null;
                
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
                    $pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]]['subm_ids']);
                
                $scores = DataManager::retrieves(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                    new DataClassRetrievesParameters($condition))->as_array();
                
                foreach ($scores as $score)
                {
                    $score_display += $score->get_score();
                }
                if ($scores != null)
                {
                    $score_display = $this->get_score_bar($score_display / count($scores));
                }
            }
            else
            {
                $pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]]['count'] = 0;
            }
            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row(
                $key, 
                Translation::get('Title'), 
                '<a href="' . $url_title . '">' . $assignment->get_title() . '</a>');
            $reporting_data->add_data_category_row(
                $key, 
                Translation::get('NumberOfSubmissions'), 
                $pub_submissions[$publication[ContentObjectPublication::PROPERTY_ID]]['count']);
            $reporting_data->add_data_category_row($key, Translation::get('LastSubmission'), $last);
            $reporting_data->add_data_category_row($key, Translation::get('NumberOfFeedbacks'), $feedback_count);
            $reporting_data->add_data_category_row($key, Translation::get('AverageScore'), $score_display);
            $reporting_data->add_data_category_row($key, Translation::get('Submissions'), $link);
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
