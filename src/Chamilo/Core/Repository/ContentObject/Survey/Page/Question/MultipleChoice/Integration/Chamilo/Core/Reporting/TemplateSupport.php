<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Integration\Core\Reporting;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_multiple_choice_question_id
     */
    public function get_answers($survey_multiple_choice_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}