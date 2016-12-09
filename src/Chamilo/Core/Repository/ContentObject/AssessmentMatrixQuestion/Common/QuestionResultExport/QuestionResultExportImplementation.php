<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Common\QuestionResultExport;

use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;

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
        
        if (! is_null($answer))
        {
            $options = array();
            foreach ($question->get_options() as $option_id => $option)
            {
                $options[$option_id] = $option->get_value();
            }
            $matches = $question->get_matches();
            
            switch ($question->get_matrix_type())
            {
                case AssessmentMatrixQuestion :: MATRIX_TYPE_CHECKBOX :
                    foreach ($answer as $answer_part_id => &$answer_part_match_ids)
                    {
                        $answer_part_matches = array();
                        foreach ($answer_part_match_ids as $answer_part_match_id => $answer_part_match)
                        {
                            $answer_part_matches[] = $matches[$answer_part_match_id];
                        }
                        $answer_part_match_ids = $options[$answer_part_id] . ' = ' . implode(', ', $answer_part_matches);
                    }
                    break;
                case AssessmentMatrixQuestion :: MATRIX_TYPE_RADIO :
                    foreach ($answer as $answer_part_id => &$answer_part)
                    {
                        $answer_part = $options[$answer_part_id] . ' = ' . $matches[$answer_part];
                    }
                    break;
            }
        }
        else
        {
            $answer = array('');
        }
        
        $this->add_answer_to_export($answer);
    }
}