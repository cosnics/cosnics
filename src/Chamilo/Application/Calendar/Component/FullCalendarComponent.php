<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FullCalendarComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        $html[] = '<div class="row">';
        $html[] = $this->getFullCalendarRenderer()->render($this->getCurrentRendererTime());
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\FullCalendarRenderer
     */
    protected function getFullCalendarRenderer()
    {
        return $this->getService('chamilo.application.calendar.service.full_calendar_renderer');
    }
}

