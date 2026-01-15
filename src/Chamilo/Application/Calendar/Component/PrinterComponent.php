<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
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

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderNormalCalendar();
        $html[] = '<script>';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
