<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Interfaces;

interface TemplateSupport
{

    /**
     *
     * @return array(\repository\ContentObject)
     */
    public function get_pages($survey_id);

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_survey();

    /**
     *
     * @return url
     */
    public function get_page_template_url($page);
}