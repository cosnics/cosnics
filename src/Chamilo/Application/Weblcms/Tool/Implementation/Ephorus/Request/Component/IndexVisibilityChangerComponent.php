<?php
/**
 * User: Pieterjan Broekaert
 * Date: 30/07/12
 * Time: 12:41
 */
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Processor\RequestProcessor;

class IndexVisibilityChangerComponent extends Manager
{

    /**
     * Runs the component
     */
    public function run()
    {
        $document_guids = $this->get_parent()->get_request_guids();
        
        $request_processor = new RequestProcessor();
        $failures = $request_processor->change_documents_visibility_on_index($document_guids);
        
        $message = $this->get_result(
            $failures, 
            count($document_guids), 
            'VisibilityNotChanged', 
            'VisibilityNotChanged', 
            'VisibilityChanged', 
            'VisibilityChanged');
        
        $this->get_parent()->redirect_after_create($message);
    }
}
