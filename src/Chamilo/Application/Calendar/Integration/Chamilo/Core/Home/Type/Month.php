<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Month extends Block
{

    public function display_content()
    {
        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            array(),
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minimonthcalendar = new MiniMonthRenderer($dataProvider, $time, $this->get_link_target());
        return $minimonthcalendar->render();
    }
}
