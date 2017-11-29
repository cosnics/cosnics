<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer\Type;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @return string
     */
    public function render($displayTime, $viewActions = array())
    {
        $html = array();

        $html[] = '<div class="table-calendar-main">';
        $html[] = '<div id="loading">Loading...</div>';
        $html[] = '<div id="calendar"></div>';
        $html[] = '<div id="legend"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->getDependencies($displayTime);

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $displayTime
     * @return string
     */
    protected function getConfiguration($displayTime)
    {
        $html = array();

        $html[] = '    		    		header: false,
		                           defaultDate: "' . date('Y-m-d', $displayTime) . '",
                           navLinks: true,
                           height: "auto",
                           firstDay: 1,
                           timeFormat: "HH:mm",
                           businessHours: {
                               dow: [ 1, 2, 3, 4, 5 ],
                               start: "10:00",
                               end: "18:00"
                           },
locale : "nl",
                           			eventSources: ' . $this->getEventSources() . ',
                           themeSystem: "bootstrap3",
                           bootstrapGlyphicons: {
    close: "glyphicon-remove",
    prev: "glyphicon-triangle-left",
    next: "glyphicon-triangle-right",
    prevYear: "glyphicon-backward",
    nextYear: "glyphicon-forward"
},
                           			loading: function(bool, view) {
                               				$("#loading").toggle(bool);
                           			}';

        return implode(PHP_EOL, $html);
    }

    protected function getEventSources()
    {
        $eventSources = $this->getFullCalendarRendererProvider()->getEventSources();
        $parsedEventSources = [];

        foreach ($eventSources as $eventSource)
        {
            $eventSourceAjaxUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => $eventSource . '\Ajax',
                    Application::PARAM_ACTION => 'FullCalendarEvents'));

            $parsedEventSource = '{
                                        url: ' . json_encode($eventSourceAjaxUrl->getUrl()) . ', cache: true
                                  }';

            $parsedEventSources[] = $parsedEventSource;
        }

        return '[' . implode(',', $parsedEventSources) . ']';
    }

    /**
     *
     * @param integer $displayTime
     * @return string
     */
    protected function getDependencies($displayTime)
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
                 'fullcalendar/locale-all.js');

        $html[] = ResourceManager::getInstance()->get_resource_html(
            PathBuilder::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) .
                 'fullcalendar/fullcalendar.min.css');

        $html[] = '<script>';
        $html[] = '	$(document).ready(function() {';
        $html[] = '    		var fullCalendar = $(\'#calendar\').fullCalendar({';
        $html[] = $this->getConfiguration($displayTime);
        $html[] = '    		});';

        $html[] = '	});';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
