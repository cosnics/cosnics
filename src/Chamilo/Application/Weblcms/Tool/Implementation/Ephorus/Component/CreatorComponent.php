<?php
/**
 * User: Pieterjan Broekaert
 * Date: 30/07/12
 * Time: 12:41
 */
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatorComponent extends EphorusRequestComponent
{
    /**
     * Runs the component
     */
    public function run()
    {
        $base_requests = $this->get_base_requests();

        $requestManager = $this->getRequestManager();
        $failures = $requestManager->handInDocuments($base_requests);

        if ($failures > 0)
        {
            $is_error_message = true;
        }
        else
        {
            $is_error_message = false;
        }
        
        $message = $this->get_result(
            $failures, 
            count($base_requests), 
            'SelectedRequestNotCreated', 
            'SelectedRequestsNotCreated', 
            'SelectedRequestCreated', 
            'SelectedRequestsCreated');
        
        $this->redirect_after_create($message, $is_error_message);
    }


}
