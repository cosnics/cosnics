<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Processor;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;

/**
 * handles request processing (calls to ephorus webservices)
 * 
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class RequestProcessor
{

    /**
     * Hand in documents based on a base_request
     * 
     * @param $base_requests
     * @return int
     */
    public function hand_in_documents($base_requests)
    {
        if (! is_array($base_requests))
        {
            $base_requests = array($base_requests);
        }
        
        $failures = 0;
        
        foreach ($base_requests as $base_request)
        {
            
            if (! $this->hand_in_document($base_request))
            {
                $failures ++;
            }
        }
        
        return $failures;
    }

    /**
     * Changes the visibility based on index_type index_type = 1 => show index_type = 2 => hide
     * 
     * @param $document_guids array
     *
     * @return boolean
     */
    public function change_documents_visibility_on_index($document_guids)
    {
        $failures = 0;
        
        foreach ($document_guids as $document_guid => $show_on_index)
        {
            if ($show_on_index)
            {
                $index_type = 1;
            }
            else
            {
                $index_type = 2;
            }
            
            if (! $this->change_single_document_visibility_on_index($document_guid, $index_type))
            {
                $failures ++;
            }
        }
        
        return $failures;
    }

    /**
     * Changes the visibility based on index_type index_type = 1 => show index_type = 2 => hide
     */
    private function change_single_document_visibility_on_index($document_guid, $index_type)
    {
        $index_document_service_wsdl = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'index_document_service_wsdl'));
        
        if (! $index_document_service_wsdl)
        {
            /**
             * @noinspection PhpDynamicAsStaticMethodCallInspection
             */
            throw new \Exception(Translation::get('IndexDocumentWsdlNotConfigured'));
        }
        
        $client = $this->get_soap_client($index_document_service_wsdl);
        
        $parameters = array();
        $parameters['documentGuid'] = $document_guid;
        $parameters['indexType'] = $index_type;
        $result = $client->call('IndexDocument', $parameters);
        
        $show_debug = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'debugging_enabled'));
        
        if ($show_debug)
        {
            $page = Page::getInstance();
            $page->setViewMode(Page::VIEW_MODE_HEADERLESS);
            
            $html = array();
            
            $html[] = $page->getHeader()->toHtml();
            $html[] = '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
            $html[] = '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
            $html[] = '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            $html[] = $page->getFooter()->toHtml();
            
            return implode(PHP_EOL, $html);
        }
        
        if ($result['IndexDocumentResult'])
        {
            $request_object = \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager::retrieve_request_by_guid(
                $document_guid);
            if ($index_type == 1)
            {
                $request_object->set_visible_on_index(1);
                $request_object->update();
            }
            else
            {
                $request_object->set_visible_on_index(0);
                $request_object->update();
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Sends a request to ephorus and saves the reference in the chamilo database
     * 
     * @param \application\weblcms\tool\ephorus\Request $base_request
     *
     * @return boolean
     */
    private function hand_in_document($base_request)
    {
        if ($base_request->is_content_object_valid())
        {
            $parameters = $this->get_hand_in_request_parameters($base_request);
            
            $document_guid = $this->send_hand_in_request($parameters);
            
            $base_request->set_guid($document_guid);
            $base_request->set_process_type($parameters['processType']);
            
            return $base_request->create();
        }
        else
        {
            return false;
        }
    }

    /**
     * Sends the request to Ephorus and returns the guid generated by ephorus
     * 
     * @param $parameters array
     *
     * @throws \Exception
     * @return string
     */
    private function send_hand_in_request($parameters)
    {
        $hand_in_wsdl = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'handin_service_wsdl'));
        
        if (! $hand_in_wsdl)
        {
            /**
             * @noinspection PhpDynamicAsStaticMethodCallInspection
             */
            throw new \Exception(Translation::get('HandInWsdlNotConfigured'));
        }
        
        $client = $this->get_soap_client($hand_in_wsdl);
        $result = $client->call('UploadDocument', $parameters);
        
        $show_debug = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'debugging_enabled'));
        
        if ($show_debug)
        {
            $page = Page::getInstance();
            $page->setViewMode(Page::VIEW_MODE_HEADERLESS);
            
            $html[] = $page->getHeader()->toHtml();
            $html[] = '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
            $html[] = '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
            $html[] = '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            $html[] = $page->getFooter()->toHtml();
            
            return implode(PHP_EOL, $html);
        }
        
        return $result['UploadDocumentResult'];
    }

    /**
     * Create the client instance
     * 
     * @param $wsdl
     */
    private function get_soap_client($wsdl)
    {
        /**
         * @noinspection PhpUndefinedClassInspection
         */
        $client = new \nusoap_client($wsdl, true);
        
        $client->setDebugLevel(0);
        $client->timeout = 500;
        $client->response_timeout = 500;
        $client->scheme = 'https';
        
        return $client;
    }

    /**
     * Fills up an array with request parameters to call the ephorus webservice
     * 
     * @param \application\weblcms\tool\ephorus\Request $request
     *
     * @throws \libraries\architecture\ObjectNotExistException
     * @throws \Exception
     * @internal param $content_object_id
     * @return array
     */
    private function get_hand_in_request_parameters(Request $request)
    {
        $document = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $request->get_content_object_id());
        
        if (! $document)
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException(
                'Document', 
                $request->get_content_object_id());
        }
        
        $author = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            $request->get_author_id());
        
        if (! $author)
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException(
                'User', 
                $request->get_author_id());
        }
        
        $parameters = array();
        
        $hand_in_code = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'hand_in_code'));
        if (! $hand_in_code)
        {
            /**
             * @noinspection PhpDynamicAsStaticMethodCallInspection
             */
            throw new \Exception(Translation::get('HandInCodeNotConfigured'));
        }
        
        $parameters['code'] = $hand_in_code;
        $parameters['firstName'] = $author->get_firstname();
        $parameters['middleName'] = "";
        $parameters['lastName'] = $author->get_lastname();
        $parameters['studentEmail'] = $author->get_email();
        $parameters['studentNumber'] = $author->get_official_code();
        $parameters['comment'] = $document->get_description();
        $parameters['fileName'] = $document->get_filename();
        
        $parameters['file'] = base64_encode(file_get_contents($document->get_full_path()));
        $parameters['processType'] = 3;
        
        return $parameters;
    }
}
