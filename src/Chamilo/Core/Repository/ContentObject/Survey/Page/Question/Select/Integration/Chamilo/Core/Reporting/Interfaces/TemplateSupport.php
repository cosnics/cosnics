<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_select_question_id
     */
    public function get_answers($survey_select_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}