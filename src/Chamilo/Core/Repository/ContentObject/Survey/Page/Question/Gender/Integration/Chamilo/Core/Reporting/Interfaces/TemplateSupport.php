<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @param int $survey_gender_question_id
     */
    public function get_answers($survey_gender_question_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question();
}