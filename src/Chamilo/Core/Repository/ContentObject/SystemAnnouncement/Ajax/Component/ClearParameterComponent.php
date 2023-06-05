<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class ClearParameterComponent extends Manager
{
    public const PARAM_PARAMETER = 'parameter';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        $parameter = $this->getPostDataValue(self::PARAM_PARAMETER);
        $parameter = explode('_', $parameter, 3);

        $session = unserialize($this->getSession()->get('advanced_filter'));

        if ($parameter[1] == 'system_announcement')
        {
            switch ($parameter[2])
            {
                case 'icon' :
                    unset($session[SystemAnnouncement::PROPERTY_ICON]);
                    break;
            }

            $this->getSession()->set('advanced_filter', serialize($session));
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::bad_request();
        }
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters(): array
    {
        return [self::PARAM_PARAMETER];
    }
}
