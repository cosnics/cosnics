<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniDayRenderer;
use Chamilo\Libraries\Calendar\Service\Legend;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Day extends \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer implements ConfigurableInterface,
    StaticBlockTitleInterface
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    /**
     * Constants
     */
    const CONFIGURATION_HOUR_STEP = 'hour_step';
    const CONFIGURATION_TIME_START = 'time_start';
    const CONFIGURATION_TIME_END = 'time_end';
    const CONFIGURATION_TIME_HIDE = 'time_hide';

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param int $source
     */
    public function __construct(Application $application, HomeService $homeService, Block $block,
        $source = self::SOURCE_DEFAULT)
    {
        parent::__construct($application, $homeService, $block, $source);
        $this->initializeContainer();
    }

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(
            self::CONFIGURATION_HOUR_STEP,
            self::CONFIGURATION_TIME_START,
            self::CONFIGURATION_TIME_END,
            self::CONFIGURATION_TIME_HIDE);
    }

    public function getTitle()
    {
//         return $this->getCalendarRenderer()->renderTitle();
    }

    public function displayContent()
    {
        return '<div style="max-height: 500px; overflow: auto;">' . $this->getCalendarRenderer()->renderFullCalendar() .
             '</div>';
    }

    public function renderContentHeader()
    {
        $html = array();

        $html[] = '<div class="portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        return implode(PHP_EOL, $html);
    }

    public function renderContentFooter()
    {
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\View\MiniDayRenderer
     */
    protected function getCalendarRenderer()
    {
        if (! isset($this->calendarRenderer))
        {
            $dataProvider = new CalendarRendererProvider(
                $this->getService('chamilo.application.calendar.service.visibility_service'),
                \Chamilo\Application\Calendar\Manager::context(),
                $this->getUser(),
                array());

            $calendarLegend = new Legend($dataProvider);
            $time = Request::get('time') ? intval(Request::get('time')) : time();

            $hourStep = (int) $this->getBlock()->getSetting(self::CONFIGURATION_HOUR_STEP, 1);
            if (! is_integer($hourStep) || $hourStep < 1)
            {
                $hourStep = 1;
            }

            return new MiniDayRenderer(
                $dataProvider,
                $this->getCalendarSources(),
                $this->getCalendarConfiguration(),
                $this->getCalendarBuilderFactory()->getCalendarBuilder('Day')->buildCalendar($time),
                $this->getHtmlTableRendererFactory());
        }

        return $this->calendarRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarSources
     */
    protected function getCalendarSources()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_sources');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_configuration');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Service\CalendarBuilderFactory
     */
    protected function getCalendarBuilderFactory()
    {
        return $this->getService('chamilo.libraries.calendar.format.service.calendar_builder_factory');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory
     */
    protected function getHtmlTableRendererFactory()
    {
        return $this->getService('chamilo.libraries.calendar.event.service.html_table_renderer_factory');
    }
}
