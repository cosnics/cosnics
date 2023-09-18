<?php
namespace Chamilo\Core\Reporting\Viewer\Component;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class ViewerComponent extends Manager implements BreadcrumbLessComponentInterface
{

    public function run()
    {
        $format = $this->getRequest()->query->get(self::PARAM_FORMAT, TemplateRendition::FORMAT_HTML);
        $view = $this->getRequest()->query->get(self::PARAM_VIEW, TemplateRendition::VIEW_BASIC);

        $html = [];

        $html[] = $this->render_header();
        $html[] = TemplateRenditionImplementation::launch($this, $this->get_template(), $format, $view);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
