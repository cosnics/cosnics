<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Renderer\Type\FullCalendarRenderer;
use Chamilo\Application\Calendar\Service\FullCalendarRendererProvider;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FullCalendarComponent extends Manager implements DelegateComponent
{

    private $viewRenderer;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $html = array();

        $fullCalendarRendererProvider = new FullCalendarRendererProvider(
            $this->getService('chamilo.configuration.service.registration_consulter'),
            $this->getUser(),
            $this->getUser());

        $html[] = $this->render_header();

        $html[] = '<div class="row">';
        $html[] = $this->getViewRenderer()->render();
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\FullCalendarRenderer
     */
    protected function getViewRenderer()
    {
        if (! isset($this->viewRenderer))
        {
            $this->viewRenderer = new FullCalendarRenderer($this->getCurrentRendererTime());
        }

        return $this->viewRenderer;
    }
}

