<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Core\Repository\Implementation\GoogleDocs\API\Google_Service_Drive;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $service;

    private $client;
    const FOLDERS_MINE = 1;
    const FOLDERS_SHARED = 2;
    const DOCUMENTS_OWNED = 'mine';
    const DOCUMENTS_SHARED = '-mine';
    const DOCUMENTS_RECENT = 'recent';
    const DOCUMENTS_FOLLOWED = 'followed';
    const DOCUMENTS_TRASH = 'trashed';

    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $key = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'developer_key',
            $this->get_external_repository_instance_id());

        $this->client = new \Google_Client();
        $this->client->setDeveloperKey($key);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_VARIABLE),
            new StaticConditionVariable('session_token'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_USER_ID),
            new StaticConditionVariable(Session :: get_user_id()));
        $condition = new AndCondition($conditions);

        $setting = DataManager :: retrieve(Setting :: class_name(), new DataClassRetrieveParameters($condition));
        if ($setting instanceof Setting && $setting->get_value())
        {
            $this->client->setAccessToken($setting->get_value());
        }

        $client_id = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_id',
            $this->get_external_repository_instance_id());
        $client_secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_secret',
            $this->get_external_repository_instance_id());

        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setScopes('https://www.googleapis.com/auth/drive');

        $this->service = new Google_Service_Drive($this->client);
    }

    function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory))
        {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

    public function login()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: package(),
                Manager :: PARAM_ACTION => Manager :: ACTION_LOGIN,
                Manager :: PARAM_EXTERNAL_REPOSITORY => $this->get_external_repository_instance_id()));

        $this->client->setRedirectUri($redirect->getUrl());

        $this->service = new \Google_Service_Drive($this->client);

        $code = Request :: get('code');

        if (isset($code))
        {
            $this->client->authenticate($code);
            $token = $this->client->getAccessToken();
            $this->client->setAccessToken($token);

            $user_setting = new Setting();
            $user_setting->set_user_id(Session :: get_user_id());
            $user_setting->set_variable('session_token');
            $user_setting->set_value($token);
            $user_setting->set_external_id($this->get_external_repository_instance_id());

            return $user_setting->create();
        }
        else
        {
            $url = $this->client->createAuthUrl('https://www.googleapis.com/auth/drive');
            header('Location: ' . $url);
            exit();
        }
    }

    /**
     *
     * @param $id string
     */
    public function retrieve_external_repository_object($id)
    {
        $file = $this->service->files->get($id);
        $object = new ExternalObject();
        $object->set_id($file->id);
        $object->set_description($file->description);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($file->title);
        $object->set_created(strtotime($file->createdDate));
        $object->set_type($file->iconLink);

        if ($file->lastViewedByMeDate != null)
        {
            $object->set_viewed(strtotime($file->lastViewedByMeDate));
        }
        elseif ($file->modifiedDate != null)
        {
            $object->set_viewed(strtotime($file->modifiedDate));
        }
        else
        {
            $object->set_viewed(strtotime($file->createdDate));
        }

        $object->set_modified(strtotime($file->modifiedDate));
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
        $rights[ExternalObject :: RIGHT_USE] = $file->copyable;
        $rights[ExternalObject :: RIGHT_EDIT] = false;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = $file->copyable;
        $rights[ExternalObject :: RIGHT_DELETE] = false;
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
            $condition = 'title contains \'' . $condition . '\' and ';
        }
        $condition .= 'trashed=false';

        $folder = Request :: get(Manager :: PARAM_FOLDER);
        if (is_null($folder))
        {
            $folder = 'root';
        }
        $condition .= ' and \'' . $folder . '\' in parents and mimeType != \'application/vnd.google-apps.folder\'';

        $files = $this->service->files->listFiles(array('q' => $condition));
        $files_items = $files['modelData']['items'];
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
        if ($order_property[0]->get_property()->get_property() == ExternalObject :: PROPERTY_TITLE)
        {
            $orderBy = 'title' . ($order_property[0]->get_direction() == SORT_DESC ? ' desc' : '');
        }
        elseif ($order_property[0]->get_property()->get_property() == ExternalObject :: PROPERTY_CREATED)
        {
            $orderBy = 'createdDate' . ($order_property[0]->get_direction() == SORT_DESC ? ' desc' : '');
        }
        else
        {
            $orderBy = null;
        }

        if (! is_null($condition))
        {
            $condition = 'title contains \'' . $condition . '\' and ';
        }
        $condition .= 'trashed=false';

        $folder = Request :: get(Manager :: PARAM_FOLDER);
        if (is_null($folder))
        {
            $folder = 'root';
        }
        $condition .= ' and \'' . $folder . '\' in parents and mimeType != \'application/vnd.google-apps.folder\'';

        $files = $this->service->files->listFiles(
            array('q' => $condition, 'maxResults' => $count, 'orderBy' => $orderBy));
        $files_items = $files['modelData']['items'];
        $objects = array();

        foreach ($files_items as $file_item)
        {
            $object = new ExternalObject();
            $object->set_id($file_item['id']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($file_item['title']);
            $object->set_created(strtotime($file_item['createdDate']));

            $object->set_type($file_item['iconLink']);
            if ($file_item['lastViewedByMeDate'] != null)
            {
                $object->set_viewed(strtotime($file_item['lastViewedByMeDate']));
            }
            elseif ($file->modifiedDate != null)
            {
                $object->set_viewed(strtotime($file_item['modifiedDate']));
            }
            else
            {
                $object->set_viewed(strtotime($file_item['createdDate']));
            }

            $object->set_modified(strtotime($file_item['modifiedDate']));
            $object->set_owner_id($file_item['owners'][0]['emailAddress']);
            $object->set_owner_name($file_item['owners'][0]['displayName']);

            if ($file_item['lastModifyingUser'])

            {
                $object->set_modifier_id($file_item['lastModifyingUser']['emailAddress']);
            }
            else
            {
                $object->set_owner_id($file_item['owners'][0]['emailAddress']);
            }

            $rights = array();
            $rights[ExternalObject :: RIGHT_USE] = $file_item['copyable'];
            $rights[ExternalObject :: RIGHT_EDIT] = false;
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = $file_item['copyable'];
            $rights[ExternalObject :: RIGHT_DELETE] = false;
            $object->set_rights($rights);

            $newParent = new \Google_Service_Drive_ParentReference();

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
        $files = $this->service->files->listFiles(array('orderBy' => 'title', 'q' => $query));
        $files_items = $files['modelData']['items'];
        $folders = array();

        foreach ($files_items as $file_item)
        {
            $folder = new Folder();

            $folder->setId($file_item['id']);
            $folder->setTitle($file_item['title']);
            $folder->setParent($file_item['parents'][0]['id']);

            $folders[] = $folder;
        }

        return new ArrayResultSet($folders);
    }

    /**
     *
     * @param $folderId string
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

    public function create_external_repository_object($file, $folder)
    {
        $google_file = new \Google_Service_Drive_DriveFile();

        if (is_array($file))
        {
            $title = explode('.', $file['name']);
            $google_file->setTitle($title[0]);
            $google_file->setMimeType($file['type']);

            $data = file_get_contents($file['name']);
        }
        else
        {
            $google_file->setTitle($file);
            $google_file->setMimeType('application/vnd.google-apps.folder');
            $data = null;
        }

        $parent = new \Google_Service_Drive_ParentReference();
        $parent->setId($folder);
        $google_file->setParents(array($parent));

        $fileCreated = $this->service->files->insert($google_file, array('data' => $data, 'mimeType' => $file['type']));
        return $fileCreated->getId();
    }

    public function retrieve_folder($id)
    {
        $file = $this->service->files->get($id);
        $folder = new Folder();
        $folder->setId($file['id']);
        $folder->setTitle($file['title']);
        $folder->setParent($file['parents'][0]['id']);
        return $folder;
    }
}
