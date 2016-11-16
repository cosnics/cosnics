<?php
namespace Chamilo\Core\Repository\Implementation\Office365;

use Chamilo\Core\Repository\External\Infrastructure\Service\MicrosoftGraphClientSettingsProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Protocol\MicrosoftClient\MicrosoftClientService;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * Data Connector Microsoft Graph API
 * @notes The Graph API has some shortcomings when querying files on Microsoft OneDrive.
 * - Cannot retrieve folders only or files only, we always receive both sorts of object.
 * - API does not support offset and count.
 * - API cannot perform full text search inside a folder.
 * The implementation resembles the OneDrive browser on office.com:
 * - Folders appear in the table on the right pane.
 * - Menu only shows the path to the current directory.
 * - Searching for items in folder is restricted to searching for items starting with given string.
 * 
 * @author Andras Zolnay - edufiles
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $driveItems;

    private $folderPath;

    private $rootFolderId;

    /**
     *
     * @var MicrosoftClientService;
     */
    protected $microsoftClientService;
    const DOCUMENTS_SHARED = 'shared_with_me';
    const DOCUMENTS_RECENT = 'recent';

    /**
     * DataConnector constructor.
     * 
     * @param Instance $externalRepositoryInstance
     */
    public function __construct($externalRepositoryInstance)
    {
        parent::__construct($externalRepositoryInstance);
        
        $user = new User();
        $user->setId(Session::get_user_id());
        
        $this->microsoftClientService = new MicrosoftClientService(
            new MicrosoftGraphClientSettingsProvider($externalRepositoryInstance, $user));
    }

    public function login()
    {
        $error = Request::get('error');
        if (! is_null($error))
        {
            return false;
        }
        
        $replyParameters = array();
        $replyParameters[Application::PARAM_CONTEXT] = Manager::context();
        $replyParameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGIN;
        $replyParameters[Manager::PARAM_EXTERNAL_REPOSITORY] = $this->get_external_repository_instance_id();
        
        $code = Request::get('code');
        return $this->microsoftClientService->login($replyParameters, $code);
    }

    /**
     * Retrieves the given objects and converts the response to an ExternalObject instance.
     * 
     * @param $id string
     * @return ExternalObject
     */
    public function retrieve_external_repository_object($id)
    {
        $request = $this->microsoftClientService->createRequest('GET', 'drive/items/' . $id);
        $result = $this->microsoftClientService->sendRequest($request);
        
        return $this->parseExternalRepositoryObject($result);
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    public function count_external_repository_objects($condition)
    {
        return count($this->getDriveItems($condition));
    }

    /**
     *
     * @param $id string
     */
    public function delete_external_repository_object($id)
    {
        $this->service->files->delete($id);
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function export_external_repository_object($content_object)
    {
    }

    /**
     *
     * @param $query mixed
     *
     * @return mixed
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    public function retrieve_external_repository_objects($condition, $orderProperty, $offset, $count)
    {
        $driveItems = $this->getDriveItems($condition, $orderProperty);
        
        $objects = array();
        $end = min(count($driveItems), $offset + $count);
        for ($i = $offset; $i < $end; ++ $i)
        {
            $objects[] = $this->parseExternalRepositoryObject($driveItems[$i]);
        }
        
        return new ArrayResultSet($objects);
    }

    public function retrieveChildFolder($parent_id)
    {
        $folderPath = $this->getFolderPath();
        return new ArrayResultSet($folderPath[$parent_id]);
    }

    public function import_external_repository_object($id)
    {
        $request = $this->microsoftClientService->createRequest('GET', 'drive/items/' . $id . '/content');
        return $this->microsoftClientService->sendRequest($request, false);
    }

    public function getFolderPath()
    {
        if (! isset($this->folderPath))
        {
            $this->folderPath = array();
            
            $folderId = Request::get(Manager::PARAM_FOLDER);
            if (! empty($folderId))
            {
                $this->folderPath[$folderId] = array(); // Children of current folder are in table.
                
                while (true)
                {
                    $folder = $this->retrieveFolder($folderId);
                    if (empty($folder->getParent()))
                    {
                        break;
                    }
                    
                    $this->folderPath[$folder->getParent()] = array($folder);
                    $folderId = $folder->getParent();
                }
            }
        }
        
        return $this->folderPath;
    }

    public function getRootFolderId()
    {
        if (! isset($this->rootFolderId))
        {
            $this->rootFolderId = $this->retrieveFolder('root')->getId();
        }
        
        return $this->rootFolderId;
    }

    public function retrieveFolder($id)
    {
        $folder = new Folder();
        
        if ($id == self::DOCUMENTS_SHARED || $id == self::DOCUMENTS_RECENT)
        {
            $folder->setId($id);
            $folder->setTitle($id);
            $folder->setParent(null);
        }
        else
        {
            $request = $this->microsoftClientService->createRequest('GET', 'drive/items/' . $id);
            $result = $this->microsoftClientService->sendRequest($request);
            
            $folder->setId($result->id);
            $folder->setTitle($result->name);
            $folder->setParent($result->parentReference->id);
        }
        
        return $folder;
    }

    /**
     * \brief Creates an external repository object from the result of GET request.
     * 
     * @param \stdClass $response
     * @return ExternalObject
     */
    private function parseExternalRepositoryObject($response)
    {
        $object = new ExternalObject();
        $object->set_id($response->id);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_created(strtotime($response->createdDateTime));
        $object->set_modified(strtotime($response->lastModifiedDateTime));
        
        if (is_null($response->remoteItem))
        {
            $object->set_title($response->name);
            $object->set_type($this->parseType($response));
            $object->set_rights($this->parseRights($response));
            $object->set_owner_id($response->createdBy->user->id);
            $object->set_owner_name($response->createdBy->user->displayName);
            $object->setModifierId($response->lastModifiedBy->user->displayName);
            $object->setUrl($response->webUrl);
        }
        else
        {
            $object->set_title($response->remoteItem->name);
            $object->set_type($this->parseType($response->remoteItem));
            $object->set_rights($this->parseRights($response->remoteItem));
        }
        
        return $object;
    }

    /**
     * Determines the type of the external object
     * Type is partially based on facets folder and image and partially based on the the extension of the file.
     */
    private function parseType($response)
    {
        if (! is_null($response->folder))
        {
            return ExternalObject::TYPE_FOLDER;
        }
        
        if (! is_null($response->image))
        {
            return ExternalObject::TYPE_IMAGE;
        }
        
        $file_properties = FileProperties::from_path($response->name);
        if (! empty($file_properties->get_extension()))
        {
            return $file_properties->get_extension();
        }
        
        return ExternalObject::TYPE_FILE;
    }

    /**
     * Returns rights dependent on type.
     */
    private function parseRights($response)
    {
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = is_null($response->folder);
        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = is_null($response->folder);
        return $rights;
    }

    /**
     * \brief Retrieves all driveitems in current folder for given $searchString.
     * All driveitems are retrieved by looping over all resulting pages.
     */
    private function getDriveItems($searchString = null, $orderProperty = null)
    {
        if (! isset($this->driveItems))
        {
            $this->driveItems = array();
            
            $link = $this->getDriveItemsRequest($searchString);
            while (true)
            {
                $request = $this->microsoftClientService->createRequest('GET', $link);
                $result = get_object_vars($this->microsoftClientService->sendRequest($request));
                
                $this->driveItems = array_merge($this->driveItems, $result['value']);
                
                if (array_key_exists('@odata.nextLink', $result))
                {
                    $link = $result['@odata.nextLink'];
                }
                else
                {
                    break;
                }
            }
        }
        
        $this->sortDriveItems($this->driveItems, $orderProperty);
        
        // die(var_dump($this->driveItems));
        return $this->driveItems;
    }

    /**
     * Return search request for given condition and ordering.
     * 
     * @return Examples: - Condition is NULL and PARAM_FOLDER is null: drive/root/children
     */
    private function getDriveItemsRequest($searchString = null)
    {
        $request = 'drive/';
        
        $folder = Request::get(Manager::PARAM_FOLDER);
        if (is_null($folder))
        {
            $request .= 'root/children';
        }
        else 
            if ($folder == self::DOCUMENTS_SHARED)
            {
                $request .= 'sharedWithMe';
            }
            else 
                if ($folder == self::DOCUMENTS_RECENT)
                {
                    $request .= 'recent';
                }
                else
                {
                    $request .= 'items/' . $folder . '/children';
                }
        
        $queryParameters = array();
        
        // Retrieve only used properties
        // $queryParameters[] = '$select=id,name,folder,file,parentReference,createdDateTime';
        
        if (! is_null($searchString))
        {
            // substringof does not seem to be implemented although part of OData Filters.
            // $queryParameters[] = '$filter=substringof(\'' . $searchString . '\', name)';
            // $search is accepted, however does not have any effect.
            // $queryParameters[] = '$search=' . $searchString;
            // Funtion endswith does not ssem to be implemented either.
            // $queryParameters[] = '$filter=startswith(name, \'' . $searchString . '\') Or endswith(name, \'' .
            // $searchString . '\')';
            $queryParameters[] = '$filter=startswith(name, \'' . $searchString . '\')';
        }
        
        if (! empty($queryParameters))
        {
            $request .= '?' . implode('&', $queryParameters);
        }
        
        return $request;
    }

    private function sortDriveItems(&$driveItems, $orderProperty)
    {
        if (! empty($orderProperty))
        {
            switch ($orderProperty[0]->get_property()->get_property())
            {
                case ExternalObject::PROPERTY_TITLE :
                    $compareFunctionName = 'compareDriveItemNames';
                    
                    $folderId = Request::get(Manager::PARAM_FOLDER);
                    if ($folderId == self::DOCUMENTS_SHARED || $folderId == self::DOCUMENTS_RECENT)
                    {
                        $compareFunctionName .= 'RemoteItem';
                    }
                    
                    break;
                
                case ExternalObject::PROPERTY_CREATED :
                    $compareFunctionName = 'compareDriveItemCreationDates';
                    break;
                
                default :
                    $compareFunctionName = null;
                    break;
            }
            
            $direction = 'Desc';
            if ($orderProperty[0]->get_direction() == SORT_ASC)
            {
                $direction = 'Asc';
            }
            
            if (! is_null($compareFunctionName))
            {
                usort($driveItems, array(self, $compareFunctionName . $direction));
            }
        }
    }

    public static function compareDriveItemNamesAsc($a, $b)
    {
        return strcmp($a->name, $b->name);
    }

    public static function compareDriveItemNamesDesc($a, $b)
    {
        return strcmp($b->name, $a->name);
    }

    public static function compareDriveItemNamesRemoteItemAsc($a, $b)
    {
        return strcmp($a->remoteItem->name, $b->remoteItem->name);
    }

    public static function compareDriveItemNamesRemoteItemDesc($a, $b)
    {
        return strcmp($b->remoteItem->name, $a->remoteItem->name);
    }

    public static function compareDriveItemCreationDatesAsc($a, $b)
    {
        return strtotime($a->createdDateTime) > strtotime($b->createdDateTime);
    }

    public static function compareDriveItemCreationDatesDesc($a, $b)
    {
        return strtotime($b->createdDateTime) > strtotime($a->createdDateTime);
    }
}
