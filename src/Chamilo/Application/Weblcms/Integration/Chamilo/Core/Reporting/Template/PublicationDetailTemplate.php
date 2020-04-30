<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication\PublicationAccessBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
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
class PublicationDetailTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->tool = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL);
        $this->pid = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);

        $this->add_reporting_block($this->get_publication_access());

        $currentTool = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);
        if ($currentTool == 'Reporting')
        {
            $this->add_breadcrumbs();
        }
    }

    public function get_publication_access()
    {
        $course_weblcms_block = new PublicationAccessBlock($this);

        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        $user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        $reporting_tool = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL);

        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $user_id);
        }
        if ($this->tool)
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL,
                $reporting_tool);
        }
        if ($this->pid)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->pid);
        }

        return $course_weblcms_block;
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
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => ToolPublicationsDetailTemplate::class_name()),
                    array(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION)),
                Translation::get(
                    'TypeName',
                    null,
                    Manager::get_tool_type_namespace(
                        $this->tool ? $this->tool : Request::get('tool')))));

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->pid);

        if (! isset($publication))
            throw new ObjectNotExistException(Translation::get('ContentObjectPublication'), $this->pid);

        $trail->add(new Breadcrumb($this->get_url(), $publication->get_content_object()->get_title()));
    }
}
