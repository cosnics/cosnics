<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Common\QuestionResultExport;

/**
 * The result export implementation for this content object
 * 
 * @package repository\content_object\assessment_match_numeric_question
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class QuestionResultExportImplementation extends \Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\QuestionResultExportImplementation
{

    /**
     * Runs this exporter
     */
    public function run()
    {
        $answer = $this->get_question_result()->get_answer();
        
        $question = $this->get_complex_question()->get_ref_object();
        
        $options = $question->get_options();
        $new_answer = array();
        asort($answer, SORT_NUMERIC);
        
        foreach ($answer as $option_index => $answer_part)
        {
            if ($answer_part != '-1')
            {
                $new_answer[] = '(' . $answer_part . ') ' . $options[$option_index - 1]->get_value();
            }
        }
        if (empty($new_answer))
        {
            $new_answer[] = '';
        }
        
        $this->add_answer_to_export($new_answer);
    }
}