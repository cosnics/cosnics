<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Implementation;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class DummyTemplateRenditionImplementation extends AbstractTemplateRenditionImplementation
{

    /**
     *
     * @var string
     */
    private $format;

    /**
     *
     * @var string
     */
    private $view;

    /**
     *
     * @param \libraries\architecture\application\Application $context
     * @param ReportingTemplate $template
     * @param string $format
     * @param string $view
     */
    public function __construct($context, ReportingTemplate $template, $format, $view)
    {
        parent :: __construct($context, $template);
        $this->format = $format;
        $this->view = $view;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return TemplateRendition :: launch($this);
    }

    /**
     *
     * @see \core\reporting\AbstractTemplateRenditionImplementation::get_view()
     */
    public function get_view()
    {
        return $this->view;
    }

    /**
     *
     * @see \core\reporting\AbstractTemplateRenditionImplementation::get_format()
     */
    public function get_format()
    {
        return $this->format;
    }
}
