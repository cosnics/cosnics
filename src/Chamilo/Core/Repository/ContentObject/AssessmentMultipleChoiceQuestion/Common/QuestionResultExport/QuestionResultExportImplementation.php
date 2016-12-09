<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Common\QuestionResultExport;

use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;

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
        
        $new_answer = array();
        $options = array();
        foreach ($question->get_options() as $option_id => $option)
        {
            $options[$option_id] = $option->get_value();
        }
        
        switch ($question->get_answer_type())
        {
            case AssessmentMultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX :
                foreach ($answer as $answer_part_id => $answer_part)
                {
                    $new_answer[] = $options[$answer_part_id - 1];
                }
                break;
            case AssessmentMultipleChoiceQuestion::ANSWER_TYPE_RADIO :
                foreach ($answer as $answer_part)
                {
                    $new_answer[] = $options[$answer_part];
                }
                break;
        }
        
        $this->add_answer_to_export($new_answer);
    }
}