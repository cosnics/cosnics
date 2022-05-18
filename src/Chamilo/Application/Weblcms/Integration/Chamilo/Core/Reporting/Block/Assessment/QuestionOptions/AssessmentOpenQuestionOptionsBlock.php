<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Shows the options of the assessment open question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentOpenQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
{

    /**
     * Counts the data
     * 
     * @return ReportingData
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows(array(Translation::get('User'), Translation::get('Answer')));
        
        $question_attempts = $this->get_attempts();
        
        $row_count = 0;
        
        foreach($question_attempts as $question_attempt)
        {
            $reporting_data->add_category($row_count);
            
            $reporting_data->add_data_category_row(
                $row_count, 
                Translation::get('User'), 
                DataManager::get_fullname_from_user(
                    $question_attempt->getOptionalProperty(self::PROPERTY_ASSESSMENT_ATTEMPT)->get_user_id()));
            
            $answers = unserialize($question_attempt->get_answer());
            
            $reporting_data->add_data_category_row($row_count, Translation::get('Answer'), $answers[0]);
            
            $row_count ++;
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }
}