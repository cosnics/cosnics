<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Common\QuestionResultExport;

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
        
        $options = array();
        foreach ($question->get_options() as $option_id => $option)
        {
            $options[$option_id] = $option->get_value();
        }
        $matches = $question->get_matches();
        
        foreach ($answer as $answer_part_id => &$answer_part)
        {
            $answer_part = $options[$answer_part_id] . ' = ' . $matches[$answer_part];
        }
        
        $this->add_answer_to_export($answer);
    }
}