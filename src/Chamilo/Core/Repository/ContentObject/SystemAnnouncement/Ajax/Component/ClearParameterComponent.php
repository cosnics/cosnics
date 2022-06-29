<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class ClearParameterComponent extends Manager
{
    const PARAM_PARAMETER = 'parameter';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
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
        
        if ($parameter[1] == 'system_announcement')
        {
            switch ($parameter[2])
            {
                case 'icon' :
                    unset($session[SystemAnnouncement::PROPERTY_ICON]);
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
