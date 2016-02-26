<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Theme;

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
        Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);

        $header = Page :: getInstance()->getHeader();
        $header->addCssFile(Theme :: getInstance()->getCssPath(self :: package(), true) . 'Print.css', 'print');

        $this->set_parameter(ViewRenderer :: PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(ViewRenderer :: PARAM_TIME, $this->getCurrentRendererTime());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->renderNormalCalendar();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
