<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Block\TableBlock;
use Chamilo\Libraries\Platform\Session\Request;

class TableTemplate extends ReportingTemplate
{
    const PARAM_QUESTION_ID = 'object';

    public function __construct($parent)
    {
        parent :: __construct($parent);
        $this->set_parameter(self :: PARAM_QUESTION_ID, Request :: get(self :: PARAM_QUESTION_ID));
        $this->add_reporting_block(new TableBlock($this));
    }

    public function get_answers($question_id)
    {
        return $this->get_parent()->get_answers($question_id);
    }

    /**
     *
     * @return \repository\ContentObject
     */
    public function get_question()
    {
        return $this->get_parent()->get_question();
    }
}
