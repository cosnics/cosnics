<?php
namespace Chamilo\Core\Repository\Implementation\Box;

use boxclient;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

require_once Path::getInstance()->getPluginPath(__NAMESPACE__) . 'box-api/boxlibphp5.php';
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    const SORT_DATE_CREATED = 'DateCreated';
    const SORT_RELEVANCE = 'relevance';
    private $boxnet;

    private $key;

    private $ticket;

    public function __construct($external_repository_instance)
    {
        parent::__construct($external_repository_instance);
        $this->key = Setting::get('key', $this->get_external_repository_instance_id());
        $session_token = Setting::get('session_token', $this->get_external_repository_instance_id());
        
        $this->boxnet = new boxclient($this->key, '');
        
        $ticket_return = $this->boxnet->getTicket();
        
        $this->ticket = $ticket_return['ticket'];
        
        $auth_token = '';
        
        if ($this->ticket && ($auth_token == '') && $_REQUEST['auth_token'])
        {
            $auth_token = $_REQUEST['auth_token'];
        }
        elseif ($this->ticket && ($auth_token == '') && is_null($session_token))
        {
            $this->boxnet->getAuthToken($this->ticket);
        }
        if (is_null($session_token) && ! is_null($auth_token))
        {
            $setting = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_setting_from_variable_name(
                'session_token', 
                $this->get_external_repository_instance_id());
            $user_setting = new Setting();
            $user_setting->set_setting_id($setting->get_id());
            $user_setting->set_user_id(Session::get_user_id());
            $user_setting->set_value($auth_token);
            $user_setting->create();
        }
        $session_token = Setting::get('session_token', $this->get_external_repository_instance_id());
        $this->boxnet = new boxclient($this->boxnet->api_key, $session_token);
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     * @return array
     */
    public function retrieve_files($condition = null, $order_property, $offset, $count)
    {
        $folder = Request::get('folder');
        if (is_null($folder))
            $folder = 0;
        $files = $this->boxnet->get_files($folder);
        return $files;
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     * @return ArrayResultSet
     */
    public function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $tree = $this->retrieve_files($order_property, $offset, $count);
        $file_count = 0;
        
        $folders = array();
        foreach ($tree as $fold)
        {
            if ($fold['file_name'] != '')
            {
                $object = new ExternalObject();
                $object->set_id($fold['file_id']);
                $object->set_external_repository_id($this->get_external_repository_instance_id());
                $object->set_title($fold['file_name']);
                $object->set_created(date("m.d.y", $fold['created']));
                $object->set_modified(date("m.d.y", $fold['updated']));
                $object->set_description($fold['description']);
                $object->set_rights($this->determine_rights());
                
                $file_count ++;
                if ($file_count > $offset && $file_count <= ($count + $offset))
                {
                    $objects[] = $object;
                }
            }
        }
        return new ArrayResultSet($objects);
    }

    public function retrieve_folders($folder_url)
    {
        $tree = $this->boxnet->getAccountTree();
        $folders = array();
        
        foreach ($tree as $fold)
        {
            if ($fold['folder_name'] != '')
            {
                $folder[] = array();
                $folder['title'] = $fold['folder_name'];
                $folder['url'] = str_replace('__PLACEHOLDER__', $fold['folder_id'], $folder_url);
                $folder['class'] = 'category';
                $folders[] = $folder;
            }
        }
        return $folders;
    }

    /**
     *
     * @param $condition mixed
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        $count = 0;
        $tree = $this->retrieve_files($condition, null, null, $count);
        $folders = array();
        foreach ($tree as $fold)
        {
            if ($fold['file_name'] != '')
            {
                $count ++;
            }
        }
        return $count;
    }

    /**
     *
     * @param $query string
     * @return string
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    /**
     *
     * @param $order_properties ObjectTableOrder
     * @return string null
     */
    public function convert_order_property($order_properties)
    {
        if (count($order_properties) > 0)
        {
            $order_property = $order_properties[0]->get_property();
            if ($order_property == self::SORT_RELEVANCE)
            {
                return $order_property;
            }
            else
            {
                $sorting_direction = $order_properties[0]->get_direction();
                
                if ($sorting_direction == SORT_ASC)
                {
                    return $order_property . '-asc';
                }
                elseif ($sorting_direction == SORT_DESC)
                {
                    return $order_property . '-desc';
                }
            }
        }
        return null;
    }

    /**
     *
     * @return array
     */
    public static function get_sort_properties()
    {
        $feed_type = Request::get(Manager::PARAM_FEED_TYPE);
        $query = ActionBarSearchForm::get_query();
        
        if (($feed_type == Manager::FEED_TYPE_GENERAL && $query))
        {
            return array(self::SORT_DATE_CREATED);
        }
        else
        {
            return array();
        }
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        $file = $this->boxnet->get_file_info($id);
        
        $object = new ExternalObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($file['file_id']);
        $object->set_title($file['file_name']);
        $object->set_created($file['created']);
        $object->set_modified($file['modified']);
        $object->set_rights($this->determine_rights());
        return $object;
    }

    public function determine_rights()
    {
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = true;
        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DELETE] = true;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = true;
        return $rights;
    }

    public function create_external_repository_object($file)
    {
        return $this->boxnet->UploadFile($file);
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        return $this->boxnet->ExportFile($content_object->get_full_path());
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->boxnet->delete_file($id);
    }

    public function download_external_repository_object($id)
    {
        return $this->boxnet->download_file($id);
    }

    public function update_external_repository_object($values)
    {
    }

    public function create_external_repository_folder($parent, $folder)
    {
        return $this->boxnet->createFolder($parent, $folder);
    }
}
