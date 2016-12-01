<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Common\QuestionResultExport;

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
        
        foreach ($answer as $answer_part_id => &$answer_part_text)
        {
            $answer_part_text = '(' . $answer_part_id . ') ' . $answer_part_text;
        }
        
        $this->add_answer_to_export($answer);
    }
}