<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Block\PageBlock;
use Chamilo\Libraries\Platform\Session\Request;

class TableTemplate extends ReportingTemplate
{
    const PARAM_SURVEY_ID = 'object';

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->set_parameter(self::PARAM_SURVEY_ID, Request::get(self::PARAM_SURVEY_ID));
        $this->add_reporting_block(new PageBlock($this));
    }

    public function get_pages($survey_id)
    {
        return $this->get_parent()->get_pages($survey_id);
    }

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_survey()
    {
        return $this->get_parent()->get_survey();
    }

    /**
     *
     * @return url
     */
    public function get_page_template_url($page)
    {
        return $this->get_parent()->get_page_template_url($page);
    }
}
