<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Core\Reporting\ReportingData;

/**
 * Shows the options of the assessment multiple choice question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMultipleChoiceQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
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
        
        $total_attempts = $this->get_total_attempts();
        $answers_count = $this->get_answers_count_from_attempts();
        
        $options = $this->get_question()->get_options();
        foreach ($options as $option_index => $option)
        {
            $this->add_option_data(
                $reporting_data, 
                $row_count, 
                $option->get_value(), 
                $option->is_correct(), 
                $answers_count[$option_index], 
                $total_attempts);
            
            $row_count ++;
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }
}