<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @return array(\repository\ContentObject)
     */
    public function get_questions($survey_page_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_page();

    /**
     *
     * @return url
     */
    public function get_question_template_url($question);
}