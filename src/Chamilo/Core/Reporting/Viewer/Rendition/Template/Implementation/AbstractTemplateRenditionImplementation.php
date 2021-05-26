<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Implementation;

use Chamilo\Core\Reporting\ReportingTemplate;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class AbstractTemplateRenditionImplementation
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $context;

    /**
     *
     * @var \Chamilo\Core\Reporting\ReportingTemplate
     */
    private $template;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     * @param ReportingTemplate $template
     */
    public function __construct($context, ReportingTemplate $template)
    {
        $this->context = $context;
        $this->template = $template;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \Chamilo\Core\Reporting\ReportingTemplate
     */
    public function get_template()
    {
        return $this->template;
    }

    /**
     *
     * @param ReportingTemplate $template
     */
    public function set_template(ReportingTemplate $template)
    {
        $this->template = $template;
    }

    /**
     *
     * @return string
     */
    abstract public function get_view();

    /**
     *
     * @return string
     */
    abstract public function get_format();
}
