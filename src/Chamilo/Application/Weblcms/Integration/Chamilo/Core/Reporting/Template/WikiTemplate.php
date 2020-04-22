<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki\WikiMostEditedPageBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki\WikiMostVisitedPageBlock;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 */
class WikiTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->set_template_parameters();
        $this->add_reporting_block(new WikiMostVisitedPageBlock($this));
        $this->add_reporting_block(new WikiMostEditedPageBlock($this));
    }

    public function set_template_parameters()
    {
        $publication_id = Request::get(Manager::PARAM_PUBLICATION);
        $this->set_parameter(Manager::PARAM_PUBLICATION, $publication_id);
    }
}
