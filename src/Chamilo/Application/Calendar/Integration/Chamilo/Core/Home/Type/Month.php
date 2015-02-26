<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Calendar\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Platform\Session\Request;

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
        $html = array();

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minimonthcalendar = new MiniMonthRenderer($this, $time, $this->get_link_target());
        $html[] = $minimonthcalendar->render();

        return implode("\n", $html);
    }
}
