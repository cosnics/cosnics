<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Month extends \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer implements StaticBlockTitleInterface
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    private $calendarRenderer;

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

    public function getTitle()
    {
//         return $this->getCalendarRenderer()->renderTitle();
    }

    /**
     *
     * @return string
     */
    public function renderContentHeader()
    {
        $html = array();

        $html[] = '<div class="portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderContentFooter()
    {
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function displayContent()
    {
        return $this->getCalendarRenderer()->render();
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer
     */
    protected function getCalendarRenderer()
    {
        if (! isset($this->calendarRenderer))
        {
            $dataProvider = new CalendarRendererProvider(
                $this->getService('chamilo.application.calendar.service.visibility_service'),
                \Chamilo\Application\Calendar\Manager::context(),
                $this->getUser(),
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::context(),
                    ViewRenderer::PARAM_TYPE => ViewRenderer::TYPE_DAY));

            $time = Request::get('time') ? intval(Request::get('time')) : time();
            return new MiniMonthRenderer(
                $dataProvider,
                $this->getCalendarSources(),
                $this->getCalendarConfiguration(),
                $this->getCalendarBuilderFactory()->getCalendarBuilder('MiniMonth')->buildCalendar($time),
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
