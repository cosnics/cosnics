<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with all attempts of an assessment from one user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentAttemptsUserBlock extends AssessmentBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows($this->get_reporting_data_rows());
        
        $counter = 0;
        
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->getPublicationId());
        
        $assessment = $publication->get_content_object();
        $isHotpotatoes = $assessment->get_type() == Hotpotatoes::class;
        
        $assessment_attempts = $this->get_assessment_attempts($this->getPublicationId(), $this->get_user_id());
        while ($assessment_attempt = $assessment_attempts->next_result())
        {
            $start_time = DatetimeUtilities::format_locale_date(null, $assessment_attempt->get_start_time());
            $end_time = DatetimeUtilities::format_locale_date(null, $assessment_attempt->get_end_time());
            $time = DatetimeUtilities::format_seconds_to_hours($assessment_attempt->get_total_time());
            $score = $this->get_score_bar($assessment_attempt->get_total_score());
            
            $reporting_data->add_category($counter);
            $reporting_data->add_data_category_row($counter, Translation::get('StartTime'), $start_time);
            $reporting_data->add_data_category_row($counter, Translation::get('EndTime'), $end_time);
            
            $reporting_data->add_data_category_row(
                $counter, 
                Translation::get('Status'), 
                $assessment_attempt->get_status_as_string());
            
            if ($assessment_attempt->get_status() == AssessmentAttempt::STATUS_COMPLETED)
            {
                $reporting_data->add_data_category_row($counter, Translation::get('Time'), $time);
                $reporting_data->add_data_category_row($counter, Translation::get('Score'), $score);
                
                if (! $isHotpotatoes)
                {
                    $reporting_data->add_data_category_row(
                        $counter, 
                        Translation::get('AttemptDetails'), 
                        $this->get_assessment_result_viewer_link($assessment_attempt->get_id()));
                }
            }
            
            $this->add_additional_information_for_attempt($assessment_attempt, $counter, $reporting_data);
            
            $counter ++;
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
        return array(Html::VIEW_TABLE);
    }

    /**
     * Returns the rows for the reporting data
     * 
     * @return string[]
     */
    protected function get_reporting_data_rows()
    {
        return array(
            Translation::get('StartTime'), 
            Translation::get('EndTime'), 
            Translation::get('Time'), 
            Translation::get('Score'), 
            Translation::get('Status'), 
            Translation::get('AttemptDetails'));
    }

    /**
     * Adds additional data for the assessment attempt
     * 
     * @param AssessmentAttempt $assessment_attempt
     * @param int $counter
     * @param ReportingData $reporting_data
     */
    protected function add_additional_information_for_attempt($assessment_attempt, $counter, $reporting_data)
    {
    }
}
