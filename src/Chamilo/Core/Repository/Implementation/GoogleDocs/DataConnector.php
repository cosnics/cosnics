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

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $service;

    private $client;
    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';
    const FOLDERS_MINE = 1;
    const FOLDERS_SHARED = 2;
    const DOCUMENTS_OWNED = 'mine';
    const DOCUMENTS_SHARED = '-mine';
    const DOCUMENTS_RECENT = 'recent';
    const DOCUMENTS_FOLLOWED = 'followed';
    const DOCUMENTS_TRASH = 'trashed';
    // const DOCUMENTS_FILES = 'pdf';
    // const DOCUMENTS_DOCUMENTS = 'document';
    // const DOCUMENTS_PRESENTATIONS = 'presentation';
    // const DOCUMENTS_SPREADSHEETS = 'spreadsheet';
    // const DOCUMENTS_DRAWINGS = 'drawings';

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    // public function __construct($external_repository_instance)
    // {
    // parent :: __construct($external_repository_instance);

    // $this->session_token = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
    // 'session_token',
    // $this->get_external_repository_instance_id());

    // $this->client = new \Google_Client();
    // $this->client->setApplicationName('Drive API Quickstart');
    // $this->client->setScopes(implode(' ', array(\Google_Service_Drive :: DRIVE_METADATA_READONLY)));
    // $this->client->setAuthConfigFile('C:\wamp\www\corec5june\client_secret.json');
    // // $this->client->setAccessType('offline');

    // $credentialsPath = $this->expandHomeDirectory('~/.credentials/drive-api-quickstart.json');
    // if (file_exists($credentialsPath))
    // {
    // $accessToken = file_get_contents($credentialsPath);
    // }
    // else
    // {
    // // Request authorization from the user.
    // $authUrl = $this->client->createAuthUrl();
    // printf("Open the following link in your browser:\n%s\n", $authUrl);
    // print 'Enter verification code: ';

    // // $authCode = trim(fgets(STDIN));
    // $authCode = '4/pAPFcdHSMFcfb9zIQDm9cfNR0OiqcJ20tEuYoju6q-g';
    // // Exchange authorization code for an access token.
    // $accessToken = $this->client->authenticate($authCode);

    // $token = $this->client->getAccessToken();
    // $this->client->setAccessToken($token);

    // $user_setting = new Setting();
    // $user_setting->set_user_id(Session :: get_user_id());
    // $user_setting->set_variable('session_token');
    // $user_setting->set_value($token);

    // return $user_setting->create();

    // // Store the credentials to disk.
    // if (! file_exists(dirname($credentialsPath)))
    // {
    // mkdir(dirname($credentialsPath), 0700, true);
    // }
    // file_put_contents($credentialsPath, $accessToken);
    // printf("Credentials saved to %s\n", $credentialsPath);
    // }
    // $this->client->setAccessToken($accessToken);

    // // Refresh the token if it's expired.
    // if ($this->client->isAccessTokenExpired())
    // {
    // $this->client->refreshToken($this->client->getRefreshToken());
    // file_put_contents($credentialsPath, $this->client->getAccessToken());
    // }
    // $this->service = new \Google_Service_Drive($this->client);
    // }
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
        if ($setting instanceof Setting)
        {
            $this->client->setAccessToken($setting->get_value());
        }

        $this->service = new \Google_Service_Drive($this->client);
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

    // public function login()
    // {
    // $this->service = new \Google_Client();
    // $code = Request :: get('code');

    // if (isset($code))
    // {
    // $this->client->authenticate($code);
    // $token = $this->client->getAccessToken();
    // $this->client->setAccessToken($token);

    // $user_setting = new Setting();
    // $user_setting->set_user_id(Session :: get_user_id());
    // $user_setting->set_variable('session_token');
    // $user_setting->set_value($token);

    // return $user_setting->create();
    // }
    // else
    // {
    // $url = $this->client->createAuthUrl('https://www.googleapis.com/auth/drive');
    // header('Location: ' . $url);
    // exit();
    // }
    // }
    public function login()
    {
        $client_id = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_id',
            $this->get_external_repository_instance_id());
        $client_secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_secret',
            $this->get_external_repository_instance_id());

        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setScopes('https://www.googleapis.com/auth/drive');

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
        // var_dump($file);
        $object = new ExternalObject();
        $object->set_id($file->id);
        $object->set_description($file->description);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($file->title);
        $object->set_created(strtotime($file->createdDate));

        $mime_type = explode('application/vnd.google-apps.', $file->mimeType);
        if ($mime_type[0] == '')
        {
            $object->set_type($mime_type[1]);
        }
        else
        {
            $object->set_type($mime_type[0]);
        }

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

        $object->set_content($file->selfLink);
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = $file->copyable;
        $rights[ExternalObject :: RIGHT_EDIT] = $file->editable;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = $file->copyable;
        $rights[ExternalObject :: RIGHT_DELETE] = $file->editable;
        $object->set_rights($rights);

        // $object->set_acl($this->get_document_acl($resource_id[1]));

        return $object;
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
     * @return array
     */
    public static function get_sort_properties()
    {
        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
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
     * application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    public function count_external_repository_objects($condition)
    {
        $files = $this->service->files->listFiles($condition);
        $files_items = $files['modelData']['items'];
        return count($files_items);
    }

    private function get_special_folder_names()
    {
        return array(
            self :: DOCUMENTS_DOCUMENTS,
            self :: DOCUMENTS_DRAWINGS,
            self :: DOCUMENTS_FILES,
            self :: DOCUMENTS_HIDDEN,
            self :: DOCUMENTS_VIEWED,
            self :: DOCUMENTS_OWNED,
            self :: DOCUMENTS_PRESENTATIONS,
            self :: DOCUMENTS_SHARED,
            self :: DOCUMENTS_SPREADSHEETS,
            self :: DOCUMENTS_STARRED,
            self :: DOCUMENTS_TRASH);
    }

    private function get_documents_feed($condition, $order_property = null, $offset = null, $count = null)
    {
        // $folder = Request :: get(Manager :: PARAM_FOLDER);
        // $query = new Zend_Gdata_Docs_Query();

        // if (isset($condition))
        // {
        // $query->setQuery($condition);
        // }
        // elseif (isset($folder))
        // {
        // if (in_array($folder, $this->get_special_folder_names()))
        // {
        // $query->setCategory($folder);
        // }
        // else
        // {
        // $query->setFolder($folder);
        // }
        // }

        // if (count($order_property) > 0)
        // {
        // switch ($order_property[0]->get_property())
        // {
        // case ExternalObject :: PROPERTY_CREATED :
        // $property = 'last-modified';
        // break;
        // case ExternalObject :: PROPERTY_TITLE :
        // $property = 'title';
        // break;
        // default :
        // $property = null;
        // }
        // $query->setOrderBy($property);
        // }

        // $query->setMaxResults($count);

        // if ($offset)
        // {
        // $query->setStartIndex($offset + 1);
        // }
        return $this->service->files->listFiles($condition);
        // return $this->google_docs->getDocumentListFeed($query);
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $files = $this->service->files->listFiles($condition);
        $files_items = $files['modelData']['items'];
        $objects = array();

        foreach ($files_items as $file_item)
        {
            // var_dump($file_item);
            $object = new ExternalObject();
            $object->set_id($file_item['id']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($file_item['title']);
            $object->set_created(strtotime($file_item['createdDate']));

            $mime_type = explode('application/vnd.google-apps.', $file_item['mimeType']);
            if ($mime_type[0] == '')
            {
                $object->set_type($mime_type[1]);
            }
            else
            {
                $object->set_type($mime_type[0]);
            }

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
            $rights[ExternalObject :: RIGHT_EDIT] = $file_item['editable'];
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = $file_item['copyable'];
            $rights[ExternalObject :: RIGHT_DELETE] = $file_item['editable'];
            $object->set_rights($rights);

            $object->set_content($file_item['selfLink']);
            $object->set_preview($file_item['embedLink']);
            $objects[] = $object;
        }

        return new ArrayResultSet($objects);
    }

    private function insert_into_folder($object_id, $parent_id)
    {
        // $newParent = new \Google_Service_Drive_ParentReference();
        // $newParent->setId($parent_id);
        // $this->service->parents->insert($object_id, $newParent);
        // $children = $this->service->children->listChildren($parent_id, array());
        // foreach ($children->getItems() as $child)
        // {
        // print 'File Id: ' . $child->getId();
        // }
        // $newChild = new \Google_Service_Drive_ChildReference();
        // $newChild->setId($object_id);
        // return $this->service->children->insert($parent_id, $newChild);
    }

    // /**
    // *
    // * @param $folder_url string
    // * @return array
    // */
    // public function retrieve_folders($folder_url)
    // {
    // $folder_root = array();
    // $folders_feed = $this->google_docs->getFoldersListFeed();

    // $my_folders = array();
    // $my_folders['title'] = Translation :: get('MyFolders');
    // $my_folders['url'] = '#';
    // $my_folders['class'] = 'category';

    // $shared_folders = array();
    // $shared_folders['title'] = Translation :: get('SharedFolders');
    // // $shared_folders['url'] = str_replace('__PLACEHOLDER__', null, $folder_url);
    // $shared_folders['url'] = '#';
    // $shared_folders['class'] = 'shared_objects';

    // $objects = array();
    // foreach ($folders_feed->entries as $folder)
    // {
    // $parent_link = $folder->getLink('http://schemas.google.com/docs/2007#parent');
    // if ($parent_link instanceof Zend_Gdata_App_Extension_Link)
    // {
    // $parent_url = $parent_link->getHref();
    // $parent_id = explode(
    // ':',
    // urldecode(str_replace('https://docs.google.com/feeds/documents/private/full/', '', $parent_url)));
    // $parent = $parent_id[1];
    // }
    // else
    // {
    // if ($folder->getEditLink())
    // {
    // $parent = self :: FOLDERS_MINE;
    // }
    // else
    // {
    // $parent = self :: FOLDERS_SHARED;
    // }
    // }

    // if (! is_array($objects[$parent]))
    // {
    // $objects[$parent] = array();
    // }

    // if (! isset($objects[$parent][$folder->getResourceId()->getId()]))
    // {
    // $objects[$parent][$folder->getResourceId()->getId()] = $folder;
    // }
    // }

    // $my_folders['sub'] = $this->get_folder_tree(self :: FOLDERS_MINE, $objects, $folder_url);
    // $shared_folders['sub'] = $this->get_folder_tree(self :: FOLDERS_SHARED, $objects, $folder_url);

    // $folder_root[] = $my_folders;
    // $folder_root[] = $shared_folders;
    // return $folder_root;
    // }

    // /**
    // *
    // * @param $index string
    // * @param $folders array
    // * @param $folder_url string
    // * @return array
    // */
    // public function get_folder_tree($index, $folders, $folder_url)
    // {
    // $items = array();
    // foreach ($folders[$index] as $child)
    // {
    // $sub_folder = array();
    // $sub_folder['title'] = $child->getTitle()->getText();
    // $sub_folder['url'] = str_replace('__PLACEHOLDER__', $child->getResourceId()->getId(), $folder_url);
    // $sub_folder['class'] = 'category';

    // $children = $this->get_folder_tree($child->getResourceId()->getId(), $folders, $folder_url);

    // if (count($children) > 0)
    // {
    // $sub_folder['sub'] = $children;
    // }

    // $items[] = $sub_folder;
    // }
    // return $items;
    // }
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

    public function create_external_repository_object($file)
    {
        $service->files->insert($file);

        return $file->getId();
    }

    public function create_external_repository_folder($folder, $parent)
    {
        return $this->google_docs->createFolder($folder, $parent);
    }

    private function get_document_acl($document_id)
    {
        $acl_feed = $this->google_docs->getDocumentAclFeed($document_id);
        $document_acl = new ExternalObjectAcl();

        foreach ($acl_feed->entries as $acl)
        {
            $scope = $acl->getScope();
            $role = $acl->getRole();
            $key = $acl->getWithKey();

            if ($scope->getType() == 'default')
            {
                if (! is_null($key))
                {
                    $document_acl->set_public($key->getRole()->getValue(), $key->getKey());
                }
                else
                {
                    $document_acl->set_public($role->getValue());
                }
            }
            elseif ($scope->getType() == 'user')
            {
                if ($role->getValue() == ExternalObjectAcl :: ACL_OWNER)
                {
                    $document_acl->set_owner($scope->getValue());
                }
                elseif ($role->getValue() == ExternalObjectAcl :: ACL_READER)
                {
                    $document_acl->add_viewer($scope->getValue());
                }
                elseif ($role->getValue() == ExternalObjectAcl :: ACL_WRITER)
                {
                    $document_acl->add_collaborator($scope->getValue());
                }
            }
        }

        return $document_acl;
    }
}
