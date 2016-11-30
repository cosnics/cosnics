<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Block\QuestionBlock;
use Chamilo\Libraries\Platform\Session\Request;

class TableTemplate extends ReportingTemplate
{
    const PARAM_PAGE_ID = 'object';

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->set_parameter(self::PARAM_PAGE_ID, Request::get(self::PARAM_PAGE_ID));
        $this->add_reporting_block(new QuestionBlock($this));
    }

    public function get_questions($page_id)
    {
        return $this->get_parent()->get_questions($page_id);
    }

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_page()
    {
        return $this->get_parent()->get_page();
    }

    /**
     *
     * @return url
     */
    public function get_question_template_url($question)
    {
        return $this->get_parent()->get_question_template_url($question);
    }
}
