<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Common\QuestionResultExport;

use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;

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
        $new_answer = [];
        switch ($question->get_answer_type())
        {
            case AssessmentSelectQuestion::ANSWER_TYPE_CHECKBOX :
                if (! is_null($answer))
                {
                    foreach ($answer[0] as $answer_part)
                    {
                        $new_answer[] = $options[$answer_part]->get_value();
                    }
                }
                else
                {
                    $new_answer[] = '';
                }
                break;
            case AssessmentSelectQuestion::ANSWER_TYPE_RADIO :
                if (! is_null($options[$answer[0]]))
                {
                    $new_answer[] = $options[$answer[0]]->get_value();
                }
                else
                {
                    $new_answer[] = '';
                }
                break;
        }
        
        $this->add_answer_to_export(implode(', ', $new_answer));
    }
}