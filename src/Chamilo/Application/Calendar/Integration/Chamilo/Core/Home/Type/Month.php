<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Ajax\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\LegendRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Month extends BlockRenderer implements StaticBlockTitleInterface
{

    private $calendarRenderer;

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
        if (!isset($this->calendarRenderer))
        {
            $dataProvider = new CalendarRendererProvider(
                new CalendarRendererProviderRepository(), $this->getUser(), $this->getUser(), array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager::context(),
                MiniMonthRenderer::PARAM_TYPE => MiniMonthRenderer::TYPE_DAY
            ), Manager::context()
            );

            $calendarLegend = new LegendRenderer($this->getNotificationMessageManager(), $dataProvider);

            $time = Request::get('time') ? intval(Request::get('time')) : time();

            $this->calendarRenderer =
                new MiniMonthRenderer($dataProvider, $calendarLegend, $time, [], $this->getLinkTarget());
        }

        return $this->calendarRenderer;
    }

    public function getTitle()
    {
        return $this->getCalendarRenderer()->renderTitle();
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

    /**
     *
     * @return string
     */
    public function renderContentHeader()
    {
        $html = [];

        $html[] = '<div class="portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        return implode(PHP_EOL, $html);
    }
}
