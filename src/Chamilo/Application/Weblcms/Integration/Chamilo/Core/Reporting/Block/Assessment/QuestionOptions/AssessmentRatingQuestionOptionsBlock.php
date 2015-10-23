<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Core\Reporting\ReportingData;

/**
 * Shows the options of the assessment open question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentRatingQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
{

    /**
     * Counts the data
     * 
     * @return ReportingData
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $this->add_option_headers($reporting_data);
        
        $row_count = 0;
        
        $question = $this->get_question();
        $total_attempts = $this->get_total_attempts();
        
        $answers_count = $this->get_answers_count_from_attempts();
        
        $this->add_option_data(
            $reporting_data, 
            $row_count, 
            $question->get_correct(), 
            true, 
            $answers_count[$question->get_correct()], 
            $total_attempts);
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }
}