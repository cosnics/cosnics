<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Core\Reporting\ReportingData;

/**
 * Shows the options of the assessment open question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MatchQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
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
        
        $total_attempts = $this->get_total_attempts();
        $answers_count = $this->get_answers_count_from_attempts();
        
        $question = $this->get_question();
        $options = $question->get_options();
        
        $row_count = 0;
        foreach ($options as $option)
        {
            $calculation_value = floatval(str_replace(',', '.', $option->get_value()));
            
            $this->add_option_data(
                $reporting_data, 
                $row_count, 
                $option->get_value(), 
                true, 
                $answers_count[$calculation_value], 
                $total_attempts);
            
            $row_count ++;
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    /**
     * Returns the answer from the attempt
     * 
     * @param QuestionAttempt $attempt
     * @param mixed[] $answers
     */
    protected function get_answers_count_from_attempt(QuestionAttempt $attempt, &$answers)
    {
        $answer_array = unserialize($attempt->get_answer());
        $answers[floatval(str_replace(',', '.', $answer_array[0]))] ++;
    }
}