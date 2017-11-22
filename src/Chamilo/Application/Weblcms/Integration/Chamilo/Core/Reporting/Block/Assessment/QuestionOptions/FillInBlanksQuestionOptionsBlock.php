<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Translation\Translation;

/**
 * Shows the options of the assessment open question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FillInBlanksQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
{

    /**
     * Counts the data
     * 
     * @return ReportingData
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $reporting_data->set_rows(
            array(
                Translation::get('Sequence'), 
                Translation::get('Answer'), 
                Translation::get('Correct'), 
                Translation::get('TimesChosen'), 
                Translation::get('DifficultyIndex')));
        
        $question = $this->get_question();
        $options = $question->get_answers();
        
        $row_count = 0;
        
        $total_attempts = $this->get_total_attempts();
        $answers_count = $this->get_answers_count_from_attempts();
        
        foreach ($options as $option_index => $option)
        {
            $this->add_option_data(
                $reporting_data, 
                $row_count, 
                $option->get_value(), 
                true, 
                $answers_count[$option_index][$option->get_value()], 
                $total_attempts);
            
            $reporting_data->add_data_category_row($row_count, Translation::get('Sequence'), $option_index + 1);
            
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
        foreach ($answer_array as $selected_option => $answer)
        {
            $answers[$selected_option][$answer] ++;
        }
    }
}