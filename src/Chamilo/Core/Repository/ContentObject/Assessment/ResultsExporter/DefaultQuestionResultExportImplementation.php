<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

/**
 * The default result export implementation
 * 
 * @package repository\content_object\assessment
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DefaultQuestionResultExportImplementation extends QuestionResultExportImplementation
{

    /**
     * Runs this exporter
     */
    public function run()
    {
        $this->add_answer_to_export($this->get_question_result()->get_answer());
    }
}