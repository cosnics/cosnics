<?php
namespace Chamilo\Application\CasStorage\Component;

use Chamilo\Application\CasStorage\Manager;
use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Application\CasStorage\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request::get(Manager::PARAM_REQUEST_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $account_request = DataManager::retrieve_by_id(AccountRequest::class_name(), (int) $id);
                
                if (! $account_request->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedAccountRequestNotDeleted';
                }
                else
                {
                    $message = 'SelectedAccountRequestsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedAccountRequestDeleted';
                }
                else
                {
                    $message = 'SelectedAccountRequestsDeleted';
                }
            }
            
            $this->redirect(
                Translation::get($message, null, Utilities::COMMON_LIBRARIES), 
                ($failures ? true : false), 
                array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoAccountRequestSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }
}
