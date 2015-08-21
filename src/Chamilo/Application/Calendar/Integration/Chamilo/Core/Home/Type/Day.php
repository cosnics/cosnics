<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniDayRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Libraries\Calendar\Renderer\Legend;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Day extends Block
{

    public function display_content()
    {
        $configuration = $this->get_configuration();

        $hour_step = $configuration['hour_step'];
        $time_start = $configuration['time_start'];
        $time_end = $configuration['time_end'];

        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            array(),
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $calendarLegend = new Legend($dataProvider);

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minidaycalendar = new MiniDayRenderer(
            $dataProvider,
            $calendarLegend,
            $time,
            $this->get_link_target(),
            $hour_step,
            $time_start,
            $time_end);

        return $minidaycalendar->render();
    }
}
