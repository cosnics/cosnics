<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FullCalendarRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface
     */
    private $fullCalendarRendererProvider;

    /**
     *
     * @param integer $displayTime
     */
    public function __construct(FullCalendarRendererProviderInterface $fullCalendarRendererProvider)
    {
        $this->fullCalendarRendererProvider = $fullCalendarRendererProvider;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface
     */
    public function getFullCalendarRendererProvider()
    {
        return $this->fullCalendarRendererProvider;
    }

    /**
     *
     * @param integer $displayTime
     * @return string
     */
    public function render($displayTime)
    {
        $html = array();

        $html[] = $this->getDependencies();

        $html[] = '<script>';
        $html[] = '	$(document).ready(function() {';
        $html[] = '    		$(\'#calendar\').fullCalendar({';
        $html[] = $this->getConfiguration($displayTime);
        $html[] = '    		});';

        $html[] = '	});';
        $html[] = '</script>';

        $html[] = '<div class="col-xs-12 col-lg-8 table-calendar-main">';
        $html[] = '<div id="loading">Loading...</div>';
        $html[] = '<div id="calendar"></div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-3 table-calendar-sidebar">';
        $html[] = 'Sidebar goes here ...';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $displayTime
     * @return string
     */
    protected function getConfiguration($displayTime)
    {
        $ajaxUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => 'Ehb\Application\Calendar\Extension\SyllabusPlus\Ajax',
                Application::PARAM_ACTION => 'FullCalendarEvents'));

        $html = array();

        $html[] = '    		    		header: {
                               left: "today prev,next title",
                               right: "month,agendaWeek,agendaDay,listWeek"
                           },
		                           defaultDate: "' . date('Y-m-d', $displayTime) . '",
                           navLinks: true,
                           height: "auto",
                           firstDay: 1,
                           timeFormat: "hh:mm",
                           businessHours: {
                               dow: [ 1, 2, 3, 4, 5 ],
                               start: "10:00",
                               end: "18:00"
                           },
                           			eventSources: ' . $this->getEventSources() . ',
                           themeSystem: "bootstrap3",
                           			loading: function(bool, view) {
                               				$("#loading").toggle(bool);
                           			}';

        return implode(PHP_EOL, $html);
    }

    protected function getEventSources()
    {
        $eventSources = $this->getFullCalendarRendererProvider()->getEventSources();
        $parsedEventSources = array();

        foreach ($eventSources as $eventSource)
        {
            $eventSourceAjaxUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => $eventSource . '\Ajax',
                    Application::PARAM_ACTION => 'FullCalendarEvents'));

            $parsedEventSource = new \stdClass();
            $parsedEventSource->url = $eventSourceAjaxUrl->getUrl();

            $parsedEventSources[] = $parsedEventSource;
        }

        return json_encode($parsedEventSources);
    }

    protected function getDependencies()
    {
        $html = array();

        $html[] = ResourceManager::getInstance()->get_resource_html(
            PathBuilder::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) .
                 'fullcalendar/lib/moment.min.js');
        $html[] = ResourceManager::getInstance()->get_resource_html(
            PathBuilder::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) .
                 'fullcalendar/fullcalendar.min.js');

        $html[] = ResourceManager::getInstance()->get_resource_html(
            PathBuilder::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) .
                 'fullcalendar/fullcalendar.min.css');

        return implode(PHP_EOL, $html);
    }
}
