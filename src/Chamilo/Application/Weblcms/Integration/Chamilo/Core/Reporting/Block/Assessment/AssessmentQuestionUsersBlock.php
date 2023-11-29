<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssessmentQuestionAttemptsUserTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of the scores of an assessment
 *          question per user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentQuestionUsersBlock extends AssessmentBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows($this->get_question_user_reporting_info_headers());
        $reporting_data->add_row(Translation::get('Details'));
        
        $publication_id = $this->getPublicationId();
        $course_id = $this->getCourseId();
        
        $question_id = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION);
        $question = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(), 
            $question_id);
        
        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation::get('Details') . '" />';
        
        $users_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_users(
            $publication_id, 
            $course_id);
        
        $user_question_attempts = array();
        
        $question_attempts = $this->get_question_attempts_from_publication_and_question($publication_id, $question_id);
        while ($question_attempt = $question_attempts->next_result())
        {
            $user_question_attempts[$question_attempt->get_optional_property(self::PROPERTY_ASSESSMENT_ATTEMPT)->get_user_id()][] = $question_attempt;
        }
        
        $count = 0;
        while ($user = $users_resultset->next_result())
        {
            $user_attempts = $user_question_attempts[$user->get_id()];
            
            $reporting_data->add_category($count);
            
            $this->add_row_from_array(
                $count, 
                $this->get_question_user_reporting_info($question, $user, $user_attempts), 
                $reporting_data);
            
            if (!is_null($user_attempts) && count($user_attempts) > 0)
            {
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentQuestionAttemptsUserTemplate::class_name();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user->get_id();
                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';
            }
            else
            {
                $link = null;
            }
            
            $reporting_data->add_data_category_row($count, Translation::get('Details'), $link);
            
            $count ++;
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    /**
     * Returns the information headers for the user reporting
     * 
     * @return string[]
     */
    public function get_question_user_reporting_info_headers()
    {
        return array(
            Translation::get('Name'), 
            Translation::get('OfficialCode'), 
            Translation::get('NumberOfAttempts'), 
            Translation::get('AverageScore'), 
            Translation::get('MinScoreAchieved'), 
            Translation::get('MaxScoreAchieved'));
    }

    /**
     * Returns the information for the reporting of a user in a question from an assessment
     * 
     * @param ComplexContentObjectItem $question
     * @param User $user
     * @param UserAttempt[] $user_attempts
     *
     * @return string[]
     */
    public function get_question_user_reporting_info($question, $user, $user_attempts)
    {
        $weight = $question->get_weight();
        
        $min = $max = $score = $attempt_count = $total_time_spent = null;
        foreach ($user_attempts as $question_attempt)
        {
            $attempt_count ++;
            
            $assessment_attempt = $question_attempt->get_optional_property(self::PROPERTY_ASSESSMENT_ATTEMPT);
            
            if ($assessment_attempt->get_status() == AssessmentAttempt::STATUS_COMPLETED)
            {
                $question_attempt_score = $question_attempt->get_score();
                $score += $question_attempt_score;
                
                if (is_null($min) || $question_attempt_score < $min)
                {
                    $min = $question_attempt_score;
                }
                
                if (is_null($max) || $question_attempt_score > $max)
                {
                    $max = $question_attempt_score;
                }
            }
        }
        
        if (! is_null($score))
        {
            $score = $this->get_score_bar($score / $attempt_count / $weight * 100);
        }
        
        if (! is_null($min))
        {
            $min = $this->get_score_bar($min / $weight * 100);
        }
        
        if (! is_null($max))
        {
            $max = $this->get_score_bar($max / $weight * 100);
        }
        
        $reporting_info = array();
        
        $reporting_info[Translation::get('Name')] = $user->get_fullname();
        $reporting_info[Translation::get('OfficialCode')] = $user->get_official_code();
        $reporting_info[Translation::get('NumberOfAttempts')] = $attempt_count;
        $reporting_info[Translation::get('AverageScore')] = $score;
        $reporting_info[Translation::get('MinScoreAchieved')] = $min;
        $reporting_info[Translation::get('MaxScoreAchieved')] = $max;
        
        return $reporting_info;
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
