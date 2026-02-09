<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrinterComponent extends BrowserComponent
{
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT);

        $this->getPageConfiguration()->addCss(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css', 'print'
        );

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();
        $html[] = $this->renderCalendar();
        $html[] = '<script>';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->getFooterRenderer()->render();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->getService(BaseFooterRenderer::class);
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->getService(BaseHeaderRenderer::class);
    }
}
