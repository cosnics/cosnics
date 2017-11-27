<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Calendar\Renderer\Form\JumpForm;

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
     * @var \Chamilo\Libraries\Calendar\Renderer\Form\JumpForm
     */
    private $jumpForm;

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
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function renderNavigation()
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(Translation::get('Today'), new FontAwesomeGlyph('home'), '#', Button::DISPLAY_ICON));

        $buttonToolBar->addItem($buttonGroup);

        $buttonGroup->addButton(
            new Button(Translation::get('Previous'), new FontAwesomeGlyph('caret-left'), '#', Button::DISPLAY_ICON));
        $buttonGroup->addButton(
            new Button(Translation::get('Next'), new FontAwesomeGlyph('caret-right'), '#', Button::DISPLAY_ICON));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @return string
     */
    public function renderViewActions($viewActions)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($viewActions as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolBar->addItem($this->renderTypeButton());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function renderTypeButton()
    {
        $rendererTypes = array(
            ViewRenderer::TYPE_MONTH,
            ViewRenderer::TYPE_WEEK,
            ViewRenderer::TYPE_DAY,
            ViewRenderer::TYPE_LIST);

        $button = new DropdownButton(
            Translation::get(ViewRenderer::TYPE_MONTH . 'View'),
            new FontAwesomeGlyph('calendar'));
        $button->setDropdownClasses('dropdown-menu-right');

        foreach ($rendererTypes as $rendererType)
        {
            $button->addSubButton(
                new SubButton(
                    Translation::get($rendererType . 'View'),
                    null,
                    '#',
                    SubButton::DISPLAY_LABEL,
                    false,
                    'not-selected'));
        }

        return $button;
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

        $html[] = '<div class="col-xs-12 col-lg-8 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation();
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle($displayTime);
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions($viewActions);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div id="loading">Loading...</div>';
        $html[] = '<div id="calendar"></div>';
        $html[] = '<div id="legend"></div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-3 table-calendar-sidebar">';
        // $html[] = $this->renderMiniMonth();
        // $html[] = $this->getLegend()->render();
        $html[] = $this->getJumpForm($displayTime)->render();
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
    public function renderTitle($displayTime)
    {
        return Translation::get(date('F', $displayTime) . 'Long', null, Utilities::COMMON_LIBRARIES) . ' ' .
             date('Y', $displayTime);
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
                                        url: ' .
                 json_encode($eventSourceAjaxUrl->getUrl()) . ', cache: true
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

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Form\JumpForm
     */
    protected function getJumpForm($displayTime)
    {
        if (! isset($this->jumpForm))
        {
            $this->jumpForm = new JumpForm('', $displayTime);
        }

        return $this->jumpForm;
    }
}
