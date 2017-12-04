<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer;
use Chamilo\Libraries\Calendar\Service\Legend;
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
        return $this->getCalendarRenderer()->renderTitle();
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
        return $this->getCalendarRenderer()->renderCalendar();
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer
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

            $calendarLegend = new Legend($dataProvider);

            $time = Request::get('time') ? intval(Request::get('time')) : time();
            return new MiniMonthRenderer($dataProvider, $calendarLegend, $time, array());
        }

        return $this->calendarRenderer;
    }
}
