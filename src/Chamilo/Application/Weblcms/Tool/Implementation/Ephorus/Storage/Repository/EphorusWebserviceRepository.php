<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;
use nusoap_client;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusWebserviceRepository
{
    /**
     * @var string
     */
    protected $handInWsdl;

    /**
     * @var string
     */
    protected $handInCode;

    /**
     * @var string
     */
    protected $indexWsdl;

    /**
     * @var bool
     */
    protected $lastDebugString;

    /**
     * EphorusWebserviceRepository constructor.
     *
     * @param string $handInWsdl
     * @param string $handInCode
     * @param string $indexWsdl
     */
    public function __construct(string $handInWsdl, string $handInCode, string $indexWsdl)
    {
        $this->handInWsdl = $handInWsdl;
        $this->handInCode = $handInCode;
        $this->indexWsdl = $indexWsdl;
    }

    /**
     * Changes the visibility based on index_type index_type = 1 => show index_type = 2 => hide
     *
     * @param string $documentGuid
     * @param bool $showDocument
     *
     * @return bool
     */
    public function changeDocumentVisiblity($documentGuid, $showDocument = true)
    {
        $indexType = $showDocument ? 1 : 2;

        $parameters = array();
        $parameters['documentGuid'] = $documentGuid;
        $parameters['indexType'] = $indexType;

        $result = $this->callWsdlFunctions($this->indexWsdl, 'IndexDocument', $parameters);

        return !empty($result['IndexDocumentResult']);
    }

    /**
     * Hands in a file to ephorus
     *
     * @param \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File $file
     * @param \Chamilo\Core\User\Storage\DataClass\User $author
     *
     * @return string
     */
    public function handInDocument(File $file, User $author)
    {
        $parameters = array();

        $parameters['code'] = $this->handInCode;
        $parameters['firstName'] = $author->get_firstname();
        $parameters['middleName'] = "";
        $parameters['lastName'] = $author->get_lastname();
        $parameters['studentEmail'] = $author->get_email();
        $parameters['studentNumber'] = $author->get_official_code();
        $parameters['comment'] = $file->get_description();
        $parameters['fileName'] = $file->get_filename();

        $parameters['file'] = base64_encode(file_get_contents($file->get_full_path()));
        $parameters['processType'] = Request::PROCESS_TYPE_CHECK_AND_INVISIBLE;

        $webserviceResult = $this->callWsdlFunctions($this->handInWsdl, 'UploadDocument', $parameters);
        $guidResult = $webserviceResult['UploadDocumentResult'];

        if(empty($guidResult))
        {
            return null;
        }

        return $guidResult;
    }

    /**
     * Create the client instance
     *
     * @param string $wsdl
     *
     * @return \nusoap_client
     */
    protected function getSoapClient($wsdl)
    {
        $client = new nusoap_client($wsdl, true);

        $client->setDebugLevel(0);
        $client->timeout = 500;
        $client->response_timeout = 500;
        $client->scheme = 'https';

        return $client;
    }

    /**
     * @param string $wsdl
     * @param string $function
     * @param array $parameters
     *
     * @return array
     */
    protected function callWsdlFunctions(string $wsdl, string $function, array $parameters)
    {
        $client = $this->getSoapClient($wsdl);
        $result = $client->call($function, $parameters);
        $this->storeDebugInfo($client);

        return $result;
    }

    /**
     * @param \nusoap_client $client
     */
    protected function storeDebugInfo(nusoap_client $client)
    {
        $html = array();

        $html[] = '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
        $html[] = '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
        $html[] = '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';

        $this->lastDebugString = implode(PHP_EOL, $html);
    }
}