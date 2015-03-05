<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_matching_question_id
     */
    public function get_answers($survey_matching_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}