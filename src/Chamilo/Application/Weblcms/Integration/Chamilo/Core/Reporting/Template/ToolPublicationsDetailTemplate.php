<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication\ToolPublicationsBlock;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 */
/**
 *
 * @author Michael Kyndt
 */
class ToolPublicationsDetailTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $tool = Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL);
        $this->set_parameter(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL,
            $tool);

        $this->add_reporting_block(new ToolPublicationsBlock($this));

        $this->add_breadcrumbs();
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID => 4),
                    array(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID)),
                Translation::get('LastAccessToToolsBlock')));

        $trail->add(
            new Breadcrumb(
                $this->get_url(),
                Translation::get(
                    'TypeName',
                    null,
                    Manager::get_tool_type_namespace($this->tool))));
    }
}
