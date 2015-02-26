<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniDayRenderer;
use Chamilo\Libraries\Platform\Session\Request;

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

        $html = array();

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minidaycalendar = new MiniDayRenderer(
            $this,
            $time,
            $hour_step,
            $time_start,
            $time_end,
            $this->get_link_target());

        $html[] = $minidaycalendar->render();

        return implode("\n", $html);
    }
}
