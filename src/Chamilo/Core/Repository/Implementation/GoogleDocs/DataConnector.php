<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\External\Infrastructure\Service\GoogleClientSettingsProvider;
use Chamilo\Core\Repository\Implementation\GoogleDocs\API\Google_Service_Drive;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Protocol\GoogleClient\GoogleClientService;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var Google_Service_Drive
     */
    private $service;

    /**
     *
     * @var GoogleClientService;
     */
    protected $googleClientService;
    const FOLDERS_MINE = 1;
    const FOLDERS_SHARED = 2;
    const DOCUMENTS_OWNED = 'mine';
    const DOCUMENTS_SHARED = '-mine';
    const DOCUMENTS_RECENT = 'recent';
    const DOCUMENTS_FOLLOWED = 'followed';
    const DOCUMENTS_TRASH = 'trashed';

    /**
     * DataConnector constructor.
     * 
     * @param Instance $external_repository_instance
     */
    public function __construct($external_repository_instance)
    {
        parent::__construct($external_repository_instance);
        
        $user = new User();
        $user->setId(Session::get_user_id());
        
        $this->googleClientService = new GoogleClientService(
            new GoogleClientSettingsProvider(
                $external_repository_instance, 
                $user, 
                'https://www.googleapis.com/auth/drive'));
        
        $this->service = new Google_Service_Drive($this->googleClientService->getGoogleClient());
    }

    public function login()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::package(), 
                Manager::PARAM_ACTION => Manager::ACTION_LOGIN, 
                Manager::PARAM_EXTERNAL_REPOSITORY => $this->get_external_repository_instance_id()));
        
        $code = Request::get('code');
        $this->googleClientService->login($redirect->getUrl(), $code);
        
        return true;
    }

    /**
     *
     * @param $id string
     */
    public function retrieve_external_repository_object($id)
    {
        $file = $this->service->files->get(
            $id, 
            array(
                'fields' => 'id,name,modifiedTime,createdTime,owners,iconLink,description,mimeType,viewedByMeTime,lastModifyingUser,thumbnailLink'));
        
        $object = new ExternalObject();
        $object->set_id($file->id);
        $object->set_description($file->description);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($file->name);
        $object->set_created(strtotime($file->createdTime));
        $object->set_type($file->mimeType);
        $object->set_icon_link($file->iconLink);
        
        // $exportLinks = $file->exportLinks;
        // $exportLinks = $this->service->files->get($file->id, array('alt'=>'media'));//$file->exportLinks;
        // $exportLinks = $this->service->files->get($file->id, 'application/pdf');//$file->exportLinks;
        /*
         * if (count($exportLinks) > 0)
         * {
         * $object->set_export_links($exportLinks);
         * }
         * else
         * {
         * $object->set_export_links(array($file->mimeType => $file->downloadUrl));
         * }
         */
        if ($file->viewedByMeTime != null)
        {
            $object->set_viewed(strtotime($file->viewedByMeTime));
        }
        elseif ($file->modifiedTime != null)
        {
            $object->set_viewed(strtotime($file->modifiedTime));
        }
        else
        {
            $object->set_viewed(strtotime($file->createdTime));
        }
        
        $object->set_modified(strtotime($file->modifiedTime));
        $object->set_owner_id($file->owners[0]['emailAddress']);
        $object->set_owner_name($file->owners[0]['displayName']);
        $object->set_modifier_id($file->lastModifyingUser['emailAddress']);
        
        if ($file->embedLink && strpos($file->embedLink, 'video.google.com') === false)
        {
            $object->set_content($file->embedLink);
        }
        else
        {
            $object->set_content(str_replace('=s220', '=w1000', $file->thumbnailLink));
        }
        
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = $file->copyable;
        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = $file->copyable;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $object->set_rights($rights);
        
        return $object;
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    public function count_external_repository_objects($condition)
    {
        if (! is_null($condition))
        {
            $condition = 'name contains \'' . $condition . '\' and ';
        }
        $condition .= 'trashed=false';
        
        $folder = Request::get(Manager::PARAM_FOLDER);
        if (is_null($folder))
        {
            $folder = 'root';
        }
        $condition .= ' and \'' . $folder . '\' in parents and mimeType != \'application/vnd.google-apps.folder\'';
        
        $files = $this->service->files->listFiles(array('q' => $condition));
        
        $files_items = $files['modelData']['files'];
        
        return count($files_items);
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
    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        if ($order_property[0]->get_property()->get_property() == ExternalObject::PROPERTY_TITLE)
        {
            $orderBy = 'name' . ($order_property[0]->get_direction() == SORT_DESC ? ' desc' : '');
        }
        elseif ($order_property[0]->get_property()->get_property() == ExternalObject::PROPERTY_CREATED)
        {
            $orderBy = 'createdTime' . ($order_property[0]->get_direction() == SORT_DESC ? ' desc' : '');
        }
        else
        {
            $orderBy = null;
        }
        
        if (! is_null($condition))
        {
            $condition = 'name contains \'' . $condition . '\' and ';
        }
        $condition .= 'trashed=false';
        
        $folder = Request::get(Manager::PARAM_FOLDER);
        if (is_null($folder))
        {
            $folder = 'root';
        }
        $condition .= ' and \'' . $folder . '\' in parents and mimeType != \'application/vnd.google-apps.folder\'';
        
        $files = $this->service->files->listFiles(
            array(
                'q' => $condition, 
                'pageSize' => $count, 
                'orderBy' => $orderBy, 
                'fields' => 'files(id,name,modifiedTime,createdTime,iconLink,description,mimeType,viewedByMeTime,owners,capabilities)'));
        $files_items = $files['modelData']['files'];
        $objects = array();
        
        foreach ($files_items as $file_item)
        {
            $object = new ExternalObject();
            $object->set_id($file_item['id']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($file_item['name']);
            $object->set_created(strtotime($file_item['createdTime']));
            
            $object->set_type($file_item['mimeType']);
            $object->set_icon_link($file_item['iconLink']);
            if ($file_item['viewedByMeTime'] != null)
            {
                $object->set_viewed(strtotime($file_item['viewedByMeTime']));
            }
            elseif ($file_item['modifiedTime'] != null)
            {
                $object->set_viewed(strtotime($file_item['modifiedTime']));
            }
            else
            {
                $object->set_viewed(strtotime($file_item['createdTime']));
            }
            
            $object->set_modified(strtotime($file_item['modifiedTime']));
            $object->set_owner_id($file_item['owners'][0]['emailAddress']);
            $object->set_owner_name($file_item['owners'][0]['displayName']);
            
            // $exportLinks = $file_item['files']['export'];
            $exportLinks = $file_item['exportLinks'];
            
            if (count($exportLinks) > 0)
            {
                $object->set_export_links($exportLinks);
            }
            elseif ($file_item['downloadUrl'])
            {
                // $object->set_export_links(array($file_item['mimeType']=>'test', 'application/Pdf'=>'test'));
                $object->set_export_links(array($file_item['mimeType'] => $file_item['downloadUrl']));
            }
            
            if ($file_item['lastModifyingUser'])
            
            {
                $object->set_modifier_id($file_item['lastModifyingUser']['emailAddress']);
            }
            else
            {
                $object->set_owner_id($file_item['owners'][0]['emailAddress']);
            }
            
            $rights = array();
            $rights[ExternalObject::RIGHT_USE] = $file_item['capabilities']['canCopy'];
            $rights[ExternalObject::RIGHT_EDIT] = false;
            $rights[ExternalObject::RIGHT_DOWNLOAD] = $file_item['capabilities']['canCopy'];
            $rights[ExternalObject::RIGHT_DELETE] = false;
            $object->set_rights($rights);
            
            // $newParent = new \Google_Service_Drive_ParentReference();
            
            if ($file_item['parents'][0]['id'])
            {
                $newParent->setId($file_item['parents'][0]['id']);
                $this->service->parents->insert($file_item['id'], $newParent);
            }
            
            if ($file_item['embedLink'] && strpos($file_item['embedLink'], 'video.google.com') === false)
            {
                $object->set_content($file_item['embedLink']);
            }
            else
            {
                $object->set_content(str_replace('=s220', '=w1000', $file_item['thumbnailLink']));
            }
            
            $object->set_preview($file_item['embedLink']);
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    public function retrieve_my_folders($id)
    {
        return $this->retrieve_folders(
            "'" . $id . "' in parents and trashed = false and mimeType = 'application/vnd.google-apps.folder'");
    }

    public function retrieve_shared_folders($id)
    {
        return $this->retrieve_folders(
            "'" . $id .
                 "' in parents and sharedWithMe and trashed=false and mimeType = 'application/vnd.google-apps.folder'");
    }

    private function retrieve_folders($query)
    {
        $files = $this->service->files->listFiles(array('orderBy' => 'name', 'q' => $query));
        $files_items = $files['modelData']['files'];
        $folders = array();
        
        foreach ($files_items as $file_item)
        {
            $folder = new Folder();
            
            $folder->setId($file_item['id']);
            $folder->setTitle($file_item['name']);
            $folder->setParent($file_item['parents'][0]['id']);
            
            $folders[] = $folder;
        }
        
        return new ArrayResultSet($folders);
    }

    /**
     *
     * @param $folderId string
     *
     * @return array
     */
    public function download_external_repository_object($url)
    {
        // $session_token = $this->google_docs->getHttpClient()->getAuthSubToken();
        // $opts = array(
        // 'http' => array(
        // 'method' => 'GET',
        // 'header' => "GData-Version: 3.0\r\n" . "Authorization: AuthSub token=\"$session_token\"\r\n"));
        
        // return file_get_contents($url, false, stream_context_create($opts));
        return file_get_contents($url);
    }

    public function import_external_repository_object($externalExportURL)
    {
        if (! $externalExportURL)
        {
            throw new \InvalidArgumentException('Could not import an Google Drive because the download URL is invalid');
        }
        
        $request = new \Google_Http_Request($externalExportURL, 'GET', null, null);
        
        $httpRequest = $this->service->getClient()->getAuth()->authenticatedRequest($request);
        if ($httpRequest->getResponseHttpCode() == 200)
        {
            return $httpRequest->getResponseBody();
        }
        else
        {
            throw new \RuntimeException('Could not import the google drive file');
        }
    }

    public function create_external_repository_object($file, $folder)
    {
        $google_file = new \Google_Service_Drive_DriveFile();
        
        if (is_array($file))
        {
            $title = explode('.', $file['name']);
            $google_file->setName($title[0]);
            $google_file->setMimeType($file['type']);
            
            $data = file_get_contents($file['name']);
        }
        else
        {
            $google_file->setName($file);
            $google_file->setMimeType('application/vnd.google-apps.folder');
            $data = null;
        }
        
        $google_file->setParents(array($folder));
        
        $fileCreated = $this->service->files->create($google_file, array('data' => $data, 'mimeType' => $file['type']));
        
        return $fileCreated->getId();
    }

    public function retrieve_folder($id)
    {
        $file = $this->service->files->get($id);
        $folder = new Folder();
        $folder->setId($file['id']);
        $folder->setTitle($file['name']);
        $folder->setParent($file['parents'][0]['id']);
        
        return $folder;
    }
}
