<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer;
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

    private $calendarRenderer;

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
                new CalendarRendererProviderRepository(),
                $this->getUser(),
                $this->getUser(),
                array(),
                \Chamilo\Application\Calendar\Ajax\Manager :: context());

            $calendarLegend = new Legend($dataProvider);

            $time = Request :: get('time') ? intval(Request :: get('time')) : time();
            return new MiniMonthRenderer($dataProvider, $calendarLegend, $time, array(), $this->getLinkTarget());
        }

        return $this->calendarRenderer;
    }
}
