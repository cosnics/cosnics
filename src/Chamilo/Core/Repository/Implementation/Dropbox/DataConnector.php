<?php
namespace Chamilo\Core\Repository\Implementation\Dropbox;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Dropbox\API;
use Dropbox\OAuth\Consumer\Curl;
use Dropbox\OAuth\Storage\Encrypter;

require_once Path :: getInstance()->getPluginPath(__NAMESPACE__) . 'Dropbox/Autoloader.class.php';
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $dropbox;

    private $consumer;

    private $key;

    private $secret;

    private $tokens;

    private $oauth;
    const SORT_DATE_CREATED = 'date-created';

    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        spl_autoload_register('Dropbox\Autoloader::load');
        $this->key = Setting :: get('key', $this->get_external_repository_instance_id());
        $this->secret = Setting :: get('secret', $this->get_external_repository_instance_id());

        $redirect = new Redirect();
        $callback = $redirect->getCurrentUrl();
        $security_key = \Chamilo\Configuration\Configuration :: get('Chamilo\Configuration', 'general', 'security_key');
        $encrypter = new Encrypter($security_key);
        $storage = new \Dropbox\OAuth\Storage\Session($encrypter);
        $curl = new Curl($this->key, $this->secret, $storage, $callback);

        if ($_SESSION['dropbox_api']['access_token'])
        {
            $setting = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_setting_from_variable_name(
                'access_token',
                $this->get_external_repository_instance_id());
            $token = $_SESSION['dropbox_api']['access_token'];
            $setting->set_value($token);
            if ($setting->create())
            {
                Session :: unregister('dropbox_api');
                $storage->set($encrypter->decrypt($token), 'access_token');
            }
        }

        else
        {
            $token = Setting :: get('access_token', $this->get_external_repository_instance_id());
            $storage->set($encrypter->decrypt($token), 'access_token');
        }

        $this->dropbox = new API($curl, 'dropbox');
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
        $folder = Request :: get('folder');
        if (! is_null($folder))
        {
            $folder = urldecode($folder);
        }
        else
            $folder = '/';

        $files = $this->dropbox->metaData($folder);
        return $files;
    }

    public function retrieve_folder($path, $condition = null, $order_property, $offset, $count)
    {
        $folders = $this->dropbox->metaData($path);
        return $folders;
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
        $files = $this->retrieve_files($condition, $order_property, $offset, $count);
        $file_count = 0;

        $objects = array();
        foreach ($files['body']->contents as $file)
        {
            if ($file->is_dir != 1)
            {
                $object = new ExternalObject();
                $object->set_id((string) substr($file->path, 1));
                $object->set_external_repository_id($this->get_external_repository_instance_id());
                $object->set_title((string) substr($file->path, strripos($file->path, '/') + 1));
                $object->set_created($file->modified);
                $object->set_modified($file->modified);
                $object->set_type($file->icon);
                $object->set_description($file->size);
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
        $folders = array();
        $files = $this->retrieve_folder('/');

        foreach ($files['body']->contents as $file)
        {
            if ($file->is_dir == 1)
            {
                $folder[] = array();
                $folder['title'] = substr($file->path, strripos($file->path, '/') + 1);
                $folder['url'] = str_replace('__PLACEHOLDER__', substr($file->path, 1), $folder_url);
                $folder['class'] = 'category';
                $sub = $this->get_folder_tree($folder_url, $file->path);
                if (count($sub) > 0)
                {
                    $folder['sub'] = $sub;
                }
                $folders[] = $folder;
            }
        }
        return $folders;
    }

    public function get_folder_tree($folder_url, $folder_path)
    {
        $folders = $this->retrieve_folder($folder_path);
        $items = array();
        foreach ($folders['body']->contents as $child)
        {
            if ($child->is_dir == 1)
            {
                $sub_folder = array();
                $sub_folder['title'] = substr($child->path, strripos($child->path, '/') + 1);
                $sub_folder['url'] = str_replace('__PLACEHOLDER__', $child->path, $folder_url);
                $sub_folder['class'] = 'category';

                $sub = $this->get_folder_tree($folder_url, $child->path);
                if (count($sub) > 0)
                {
                    $sub_folder['sub'] = $sub;
                }
                $items[] = $sub_folder;
            }
        }
        return $items;
    }

    /**
     *
     * @param $condition mixed
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        $files = $this->retrieve_files($condition);

        $objects = array();
        $count = 0;
        foreach ($files['body']->contents as $file)
        {
            if ($file->is_dir != 1)
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
            if ($order_property == self :: SORT_RELEVANCE)
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
        $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();

        if (($feed_type == Manager :: FEED_TYPE_GENERAL && $query))
        {
            return array(self :: SORT_DATE_CREATED);
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
        $file = $this->dropbox->metaData($id);

        $object = new ExternalObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($id);
        $object->set_title(str_replace('/', '', substr($id, strripos($id, '/'))));
        $object->set_created($file->modified);
        $object->set_modified($file->modified);
        $object->set_type($file->icon);
        $object->set_description($file->size);

        $object->set_rights($this->determine_rights());
        return $object;
    }

    public function determine_rights()
    {
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = true;
        $rights[ExternalObject :: RIGHT_EDIT] = false;
        $rights[ExternalObject :: RIGHT_DELETE] = true;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = true;
        return $rights;
    }

    /**
     *
     * @param $values array
     * @param $file_path string
     * @return mixed
     */
    public function create_external_repository_object($file, $file_path)
    {
        $file = str_replace(' ', '', $file);
        return $this->dropbox->putFile($file_path, $file);
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        $file = str_replace(' ', '', $content_object->get_title());
        return $this->dropbox->putFile($content_object->get_full_path(), $file);
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->dropbox->delete($id);
    }

    public function download_external_repository_object($id)
    {
        $file = $this->dropbox->getFile($id);
        return $file['data'];
    }

    public function update_external_repository_object($values)
    {
    }

    public function encode($path)
    {
        $file = explode('/', $path);
        $newpath = array();
        foreach ($file as $f)
        {
            $newpath[] = rawurlencode($f);
        }
        return implode('/', $newpath);
    }

    public function create_external_repository_folder($folder)
    {
        return $this->dropbox->createFolder($folder);
    }
}
