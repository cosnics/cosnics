<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 * @package Ehb\Application\Calendar\Extension\SyllabusPlus\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrinterComponent extends BrowserComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT);

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);
        $this->getPageConfiguration()->addCssFile(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css', 'print'
        );

        $this->set_parameter(HtmlCalendarRenderer::PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(HtmlCalendarRenderer::PARAM_TIME, $this->getCurrentRendererTime());

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
