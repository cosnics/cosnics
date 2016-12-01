<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_open_question_id
     */
    public function get_answers($survey_open_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}