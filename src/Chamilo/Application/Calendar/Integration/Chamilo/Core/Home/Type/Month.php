<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\View\MiniMonthRenderer;

/**
 *
 * @package Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Month extends \Chamilo\Core\Home\BlockRendition
{

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
        $minimonthcalendar = new MiniMonthRenderer($dataProvider, $calendarLegend, $time, $this->getLinkTarget());
        return $minimonthcalendar->render();
    }
}
