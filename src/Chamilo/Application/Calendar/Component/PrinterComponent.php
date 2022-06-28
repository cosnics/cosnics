<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 *
 * @package Ehb\Application\Calendar\Extension\SyllabusPlus\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrinterComponent extends BrowserComponent implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context());

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);
        $this->getPageConfiguration()->addCssFile(
            $this->getThemePathBuilder()->getCssPath(self::package(), true) . 'Print.css', 'print'
        );

        $this->set_parameter(ViewRenderer::PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(ViewRenderer::PARAM_TIME, $this->getCurrentRendererTime());

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderNormalCalendar();
        $html[] = '<script>';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
