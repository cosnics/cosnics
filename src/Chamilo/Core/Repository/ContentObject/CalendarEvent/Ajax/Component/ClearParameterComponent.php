<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ClearParameterComponent extends \Chamilo\Core\Repository\ContentObject\CalendarEvent\Ajax\Manager
{
    const PARAM_PARAMETER = 'parameter';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARAMETER);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $parameter = $this->getPostDataValue(self::PARAM_PARAMETER);
        $parameter = explode('_', $parameter, 3);
        
        $session = unserialize(Session::retrieve('advanced_filter'));
        
        if ($parameter[1] == 'calendar_event')
        {
            switch ($parameter[2])
            {
                case 'start_date' :
                    unset($session[CalendarEvent::PROPERTY_START_DATE]);
                    break;
                case 'end_date' :
                    unset($session[CalendarEvent::PROPERTY_END_DATE]);
                    break;
                case 'date' :
                    unset($session[CalendarEvent::PROPERTY_START_DATE]);
                    unset($session[CalendarEvent::PROPERTY_END_DATE]);
                    break;
            }
            
            Session::register('advanced_filter', serialize($session));
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::bad_request();
        }
    }
}
