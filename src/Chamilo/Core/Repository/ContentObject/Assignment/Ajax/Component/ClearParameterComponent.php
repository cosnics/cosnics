<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
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

        $session = unserialize($this->getSessionUtilities()->retrieve('advanced_filter'));

        if ($parameter[1] == 'assignment')
        {
            switch ($parameter[2])
            {
                case 'start_time' :
                    unset($session[Assignment::PROPERTY_START_TIME]);
                    break;
                case 'end_time' :
                    unset($session[Assignment::PROPERTY_END_TIME]);
                    break;
                case 'time' :
                    unset($session[Assignment::PROPERTY_START_TIME]);
                    unset($session[Assignment::PROPERTY_END_TIME]);
                    break;
            }

            $this->getSessionUtilities()->register('advanced_filter', serialize($session));
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
