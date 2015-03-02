<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssessmentAttemptsUserTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of all users the assessment is
 *          published for and their attempt stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentUsersBlock extends AssessmentBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows($this->get_user_reporting_info_headers());
        $reporting_data->add_row(Translation :: get('UserDetails'));
        
        $pub_id = $this->get_publication_id();
        $course_id = $this->get_course_id();
        
        $count = 1;
        
        $users_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_users(
            $pub_id, 
            $course_id);
        $user_attempts = $this->calculate_user_attempt_summary_data();
        
        while ($user = $users_resultset->next_result())
        {
            $reporting_data->add_category($count);
            
            $reporting_info = $this->get_user_reporting_info($user, $user_attempts[$user->get_id()]);
            $this->add_row_from_array($count, $reporting_info, $reporting_data);
            
            if ($user_attempts[$user->get_id()]['count'] > 0)
            {
                $img = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'action_reporting.png" title="' .
                     Translation :: get('Details') . '" />';
                
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = AssessmentAttemptsUserTemplate :: class_name();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user->get_id();
                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';
                
                $reporting_data->add_data_category_row($count, Translation :: get('UserDetails'), $link);
            }
            
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
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }

    /**
     * Returns the headers for the user reporting information
     * 
     * @return string[]
     */
    protected function get_user_reporting_info_headers()
    {
        return array(
            Translation :: get('Name'), 
            Translation :: get('OfficialCode'), 
            Translation :: get('TotalTime'), 
            Translation :: get('NumberOfAttempts'), 
            Translation :: get('FirstAttempt'), 
            Translation :: get('LastAttempt'), 
            Translation :: get('AverageScore'), 
            Translation :: get('MinScoreAchieved'), 
            Translation :: get('MaxScoreAchieved'));
    }

    /**
     * Returns the reporting info for a given user and his attempts
     * 
     * @param User $user
     * @param mixed[] $user_attempt_summary_data
     *
     * @return string[]
     */
    protected function get_user_reporting_info($user, $user_attempt_summary_data)
    {
        $reporting_info = array();
        
        $score = $first = $last = $time = $min_score = $max_score = null;
        if ($user_attempt_summary_data['status'] == AssessmentAttempt :: STATUS_COMPLETED)
        {
            $score = $this->get_score_bar(
                $user_attempt_summary_data['total_score'] / $user_attempt_summary_data['score_count']);
            
            $first = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                     Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), 
                    $user_attempt_summary_data['first']);
            $last = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                     Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), 
                    $user_attempt_summary_data['last']);
            
            $min_score = $this->get_score_bar($user_attempt_summary_data['min_score']);
            $max_score = $this->get_score_bar($user_attempt_summary_data['max_score']);
        }
        
        $time = Utilities :: format_seconds_to_hours($user_attempt_summary_data['time']);
        
        if ($user_attempt_summary_data['count'] > 0)
        {
            $reporting_info[Translation :: get('NumberOfAttempts')] = $user_attempt_summary_data['count'];
        }
        else
        {
            $reporting_info[Translation :: get('NumberOfAttempts')] = 0;
        }
        
        $reporting_info[Translation :: get('Name')] = $user->get_fullname();
        $reporting_info[Translation :: get('OfficialCode')] = $user->get_official_code();
        $reporting_info[Translation :: get('TotalTime')] = $time;
        $reporting_info[Translation :: get('FirstAttempt')] = $first;
        $reporting_info[Translation :: get('LastAttempt')] = $last;
        $reporting_info[Translation :: get('MinScoreAchieved')] = $min_score;
        $reporting_info[Translation :: get('MaxScoreAchieved')] = $max_score;
        $reporting_info[Translation :: get('AverageScore')] = $score;
        
        return $reporting_info;
    }

    /**
     * Calculates the user attempt summary data
     * 
     * @return mixed[]
     */
    protected function calculate_user_attempt_summary_data()
    {
        $assessment_attempts = $this->retrieve_assessment_attempts();
        
        $user_attempts = array();
        
        while ($assessment_attempt = $assessment_attempts->next_result())
        {
            $user_attempts[$assessment_attempt->get_user_id()]['count'] ++;
            $user_attempts[$assessment_attempt->get_user_id()]['time'] += $assessment_attempt->get_total_time();
            
            if ($assessment_attempt->get_status() == AssessmentAttempt :: STATUS_COMPLETED)
            {
                $user_attempts[$assessment_attempt->get_user_id()]['status'] = $assessment_attempt->get_status();
                
                $user_attempts[$assessment_attempt->get_user_id()]['total_score'] += $assessment_attempt->get_total_score();
                
                $user_attempts[$assessment_attempt->get_user_id()]['score_count'] ++;
                
                if (is_null($user_attempts[$assessment_attempt->get_user_id()]['min_score']) || $user_attempts[$assessment_attempt->get_user_id()]['min_score'] >
                     $assessment_attempt->get_total_score())
                {
                    $user_attempts[$assessment_attempt->get_user_id()]['min_score'] = $assessment_attempt->get_total_score();
                }
                
                if (is_null($user_attempts[$assessment_attempt->get_user_id()]['max_score']) || $user_attempts[$assessment_attempt->get_user_id()]['max_score'] <
                     $assessment_attempt->get_total_score())
                {
                    $user_attempts[$assessment_attempt->get_user_id()]['max_score'] = $assessment_attempt->get_total_score();
                }
            }
            
            if ($user_attempts[$assessment_attempt->get_user_id()]['first'] == null ||
                 $user_attempts[$assessment_attempt->get_user_id()]['first'] > $assessment_attempt->get_start_time())
            {
                $user_attempts[$assessment_attempt->get_user_id()]['first'] = $assessment_attempt->get_start_time();
            }
            
            if ($user_attempts[$assessment_attempt->get_user_id()]['last'] == null ||
                 $assessment_attempt->get_start_time() > $user_attempts[$assessment_attempt->get_user_id()]['last'])
            {
                $user_attempts[$assessment_attempt->get_user_id()]['last'] = $assessment_attempt->get_start_time();
            }
        }
        
        return $user_attempts;
    }

    /**
     * Retrieves the assessment attempts
     * 
     * @return ResultSet
     */
    protected function retrieve_assessment_attempts()
    {
        return WeblcmsTrackingDataManager :: retrieves(
            AssessmentAttempt :: class_name(), 
            $this->get_assessment_attempts_condition());
    }

    /**
     * Returns the condition for the assessment attempts
     * 
     * @return Condition
     */
    protected function get_assessment_attempts_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt :: class_name(), AssessmentAttempt :: PROPERTY_ASSESSMENT_ID), 
            new StaticConditionVariable($this->get_publication_id()));
    }
}
