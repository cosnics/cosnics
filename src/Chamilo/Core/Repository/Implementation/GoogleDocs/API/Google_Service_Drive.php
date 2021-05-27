<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\API;

use Google_Client;

/**
 * Small extension to cover the fact that the orderBy parameter is missing in the definition of the list-method in
 * \Google_Service_Drive_Files_Resource
 * 
 * @package Chamilo\Core\Repository\Implementation\GoogleDocs\API
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Google_Service_Drive extends \Google_Service_Drive
{

    /**
     *
     * @param \Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
        
        /*
         * $this->files = new \Google_Service_Drive_Files_Resource(
         * $this,
         * $this->serviceName,
         * 'files',
         * array(
         * 'methods' => array(
         * 'copy' => array(
         * 'path' => 'files/{fileId}/copy',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true),
         * 'convert' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocrLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'visibility' => array('location' => 'query', 'type' => 'string'),
         * 'pinned' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocr' => array('location' => 'query', 'type' => 'boolean'),
         * 'timedTextTrackName' => array('location' => 'query', 'type' => 'string'),
         * 'timedTextLanguage' => array('location' => 'query', 'type' => 'string'))),
         * 'delete' => array(
         * 'path' => 'files/{fileId}',
         * 'httpMethod' => 'DELETE',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true))),
         * 'emptyTrash' => array('path' => 'files/trash', 'httpMethod' => 'DELETE', 'parameters' => []),
         * 'get' => array(
         * 'path' => 'files/{fileId}',
         * 'httpMethod' => 'GET',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true),
         * 'acknowledgeAbuse' => array('location' => 'query', 'type' => 'boolean'),
         * 'updateViewedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'revisionId' => array('location' => 'query', 'type' => 'string'),
         * 'projection' => array('location' => 'query', 'type' => 'string'))),
         * 'insert' => array(
         * 'path' => 'files',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'convert' => array('location' => 'query', 'type' => 'boolean'),
         * 'useContentAsIndexableText' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocrLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'visibility' => array('location' => 'query', 'type' => 'string'),
         * 'pinned' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocr' => array('location' => 'query', 'type' => 'boolean'),
         * 'timedTextTrackName' => array('location' => 'query', 'type' => 'string'),
         * 'timedTextLanguage' => array('location' => 'query', 'type' => 'string'))),
         * 'list' => array(
         * 'path' => 'files',
         * 'httpMethod' => 'GET',
         * 'parameters' => array(
         * 'q' => array('location' => 'query', 'type' => 'string'),
         * 'pageToken' => array('location' => 'query', 'type' => 'string'),
         * 'corpus' => array('location' => 'query', 'type' => 'string'),
         * 'projection' => array('location' => 'query', 'type' => 'string'),
         * 'orderBy' => array('location' => 'query', 'type' => 'string'),
         * 'maxResults' => array('location' => 'query', 'type' => 'integer'))),
         * 'patch' => array(
         * 'path' => 'files/{fileId}',
         * 'httpMethod' => 'PATCH',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true),
         * 'addParents' => array('location' => 'query', 'type' => 'string'),
         * 'updateViewedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'removeParents' => array('location' => 'query', 'type' => 'string'),
         * 'setModifiedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'convert' => array('location' => 'query', 'type' => 'boolean'),
         * 'useContentAsIndexableText' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocrLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'pinned' => array('location' => 'query', 'type' => 'boolean'),
         * 'newRevision' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocr' => array('location' => 'query', 'type' => 'boolean'),
         * 'timedTextLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'timedTextTrackName' => array('location' => 'query', 'type' => 'string'))),
         * 'touch' => array(
         * 'path' => 'files/{fileId}/touch',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true))),
         * 'trash' => array(
         * 'path' => 'files/{fileId}/trash',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true))),
         * 'untrash' => array(
         * 'path' => 'files/{fileId}/untrash',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true))),
         * 'update' => array(
         * 'path' => 'files/{fileId}',
         * 'httpMethod' => 'PUT',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true),
         * 'addParents' => array('location' => 'query', 'type' => 'string'),
         * 'updateViewedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'removeParents' => array('location' => 'query', 'type' => 'string'),
         * 'setModifiedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'convert' => array('location' => 'query', 'type' => 'boolean'),
         * 'useContentAsIndexableText' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocrLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'pinned' => array('location' => 'query', 'type' => 'boolean'),
         * 'newRevision' => array('location' => 'query', 'type' => 'boolean'),
         * 'ocr' => array('location' => 'query', 'type' => 'boolean'),
         * 'timedTextLanguage' => array('location' => 'query', 'type' => 'string'),
         * 'timedTextTrackName' => array('location' => 'query', 'type' => 'string'))),
         * 'watch' => array(
         * 'path' => 'files/{fileId}/watch',
         * 'httpMethod' => 'POST',
         * 'parameters' => array(
         * 'fileId' => array('location' => 'path', 'type' => 'string', 'required' => true),
         * 'acknowledgeAbuse' => array('location' => 'query', 'type' => 'boolean'),
         * 'updateViewedDate' => array('location' => 'query', 'type' => 'boolean'),
         * 'revisionId' => array('location' => 'query', 'type' => 'string'),
         * 'projection' => array('location' => 'query', 'type' => 'string'))))));
         */
    }
}
