<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with the assessment question attempts of one user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentQuestionAttemptsUserBlock extends AssessmentBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows($this->get_reporting_data_rows());
        
        $question_id = Request::get(
            Manager::PARAM_QUESTION);
        
        $question = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class,
            $question_id);
        $weight = $question->get_weight();
        
        $question_attempts = $this->get_question_attempts_from_publication_and_question(
            $this->getPublicationId(), 
            $question_id, 
            $this->get_user_id());
        
        $counter = 0;
        
        foreach($question_attempts as $question_attempt)
        {
            $assessment_attempt = $question_attempt->getOptionalProperty(self::PROPERTY_ASSESSMENT_ATTEMPT);
            
            $date = $assessment_attempt->get_start_time();
            $date = DatetimeUtilities::getInstance()->formatLocaleDate(null, $date);
            
            $reporting_data->add_category($counter);
            
            // if($assessment_attempt->get_status() == AssessmentAttempt::STATUS_COMPLETED)
            {
                $score = $this->get_score_bar($question_attempt->get_score() / $weight * 100);
                
                $reporting_data->add_data_category_row(
                    $counter, 
                    Translation::get('Details'), 
                    $this->get_assessment_result_viewer_link($assessment_attempt->get_id(), $question_attempt->get_id()));
            }
            
            $reporting_data->add_data_category_row($counter, Translation::get('Date'), $date);
            $reporting_data->add_data_category_row($counter, Translation::get('Score'), $score);
            /*
             * $reporting_data->add_data_category_row( $counter, Translation::get('Status'),
             * $assessment_attempt->get_status_as_string() );
             */
            
            $this->add_additional_information_for_attempt(
                $assessment_attempt, 
                $question_attempt, 
                $counter, 
                $reporting_data);
            
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
        return array(Translation::get('Date'), Translation::get('Score'), 
            // Translation::get('Status'),
            Translation::get('Details'));
    }

    /**
     * Adds additional data for the assessment attempt
     * 
     * @param AssessmentAttempt $assessment_attempt
     * @param QuestionAttempt $question_attempt
     * @param int $counter
     * @param ReportingData $reporting_data
     */
    protected function add_additional_information_for_attempt($assessment_attempt, $question_attempt, $counter, 
        $reporting_data)
    {
    }
}
