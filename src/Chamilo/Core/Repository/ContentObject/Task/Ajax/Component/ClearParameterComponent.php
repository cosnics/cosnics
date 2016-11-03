<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class ClearParameterComponent extends \Chamilo\Core\Repository\ContentObject\Task\Ajax\Manager
{
    const PARAM_PARAMETER = 'parameter';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_PARAMETER);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $parameter = $this->getPostDataValue(self :: PARAM_PARAMETER);
        $parameter = explode('_', $parameter, 3);
        
        $session = unserialize(Session :: retrieve('advanced_filter'));
        
        if ($parameter[1] == 'task')
        {
            switch ($parameter[2])
            {
                case 'start_date' :
                    unset($session[Task :: PROPERTY_START_DATE]);
                    break;
                case 'end_date' :
                    unset($session[Task :: PROPERTY_END_DATE]);
                    break;
                case 'date' :
                    unset($session[Task :: PROPERTY_START_DATE]);
                    unset($session[Task :: PROPERTY_END_DATE]);
                    break;
                case Task :: PROPERTY_REPEAT_TYPE :
                    unset($session[Task :: PROPERTY_REPEAT_TYPE]);
                    break;
                case Task :: PROPERTY_TASK_TYPE :
                    unset($session[Task :: PROPERTY_TASK_TYPE]);
                    break;
                case Task :: PROPERTY_TASK_PRIORITY :
                    unset($session[Task :: PROPERTY_TASK_PRIORITY]);
                    break;
            }
            
            Session :: register('advanced_filter', serialize($session));
            JsonAjaxResult :: success();
        }
        else
        {
            JsonAjaxResult :: bad_request();
        }
    }
}
