<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniDayRenderer;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Day extends \Chamilo\Core\Home\BlockRendition implements ConfigurableInterface
{
    const CONFIGURATION_HOUR_STEP = 'hour_step';
    const CONFIGURATION_TIME_START = 'time_start';
    const CONFIGURATION_TIME_END = 'time_end';
    const CONFIGURATION_TIME_HIDE = 'time_hide';

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(
            self :: CONFIGURATION_HOUR_STEP,
            self :: CONFIGURATION_TIME_START,
            self :: CONFIGURATION_TIME_END,
            self :: CONFIGURATION_TIME_HIDE);
    }

    public function displayContent()
    {
        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->getUser(),
            $this->getUser(),
            array(),
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $calendarLegend = new Legend($dataProvider);

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minidaycalendar = new MiniDayRenderer(
            $dataProvider,
            $calendarLegend,
            $time,
            $this->getLinkTarget(),
            $this->getBlock()->getSetting(self :: CONFIGURATION_HOUR_STEP, 1),
            $this->getBlock()->getSetting(self :: CONFIGURATION_TIME_START, 8),
            $this->getBlock()->getSetting(self :: CONFIGURATION_TIME_END, 17),
            $this->getBlock()->getSetting(self :: CONFIGURATION_TIME_HIDE, 17));

        return $minidaycalendar->render();
    }
}
