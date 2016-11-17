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
     * @var \libraries\architecture\application\Application
     */
    private $context;

    /**
     *
     * @var \core\reporting\ReportingTemplate
     */
    private $template;

    /**
     *
     * @param \libraries\architecture\application\Application $context
     * @param ReportingTemplate $template
     */
    public function __construct($context, ReportingTemplate $template)
    {
        $this->context = $context;
        $this->template = $template;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param \libraries\architecture\application\Application $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \core\reporting\ReportingTemplate
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
