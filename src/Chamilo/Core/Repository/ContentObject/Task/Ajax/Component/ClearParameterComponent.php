<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Task\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class ClearParameterComponent extends Manager
{
    const PARAM_PARAMETER = 'parameter';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        $parameter = $this->getPostDataValue(self::PARAM_PARAMETER);
        $parameter = explode('_', $parameter, 3);

        $session = unserialize(Session::retrieve('advanced_filter'));

        if ($parameter[1] == 'task')
        {
            switch ($parameter[2])
            {
                case 'start_date' :
                    unset($session[Task::PROPERTY_START_DATE]);
                    break;
                case 'end_date' :
                    unset($session[Task::PROPERTY_DUE_DATE]);
                    break;
                case 'date' :
                    unset($session[Task::PROPERTY_START_DATE]);
                    unset($session[Task::PROPERTY_DUE_DATE]);
                    break;
                case Task::PROPERTY_FREQUENCY :
                    unset($session[Task::PROPERTY_FREQUENCY]);
                    break;
                case Task::PROPERTY_CATEGORY :
                    unset($session[Task::PROPERTY_CATEGORY]);
                    break;
                case Task::PROPERTY_PRIORITY :
                    unset($session[Task::PROPERTY_PRIORITY]);
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

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARAMETER);
    }
}
