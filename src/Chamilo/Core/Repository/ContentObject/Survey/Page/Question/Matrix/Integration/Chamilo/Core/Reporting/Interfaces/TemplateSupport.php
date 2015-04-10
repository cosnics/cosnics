<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_matrix_question_id
     */
    public function get_answers($survey_matrix_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}