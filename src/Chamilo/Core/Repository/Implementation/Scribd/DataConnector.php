<?php
namespace Chamilo\Core\Repository\Implementation\Scribd;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Scribd;

require_once Path::getInstance()->getPluginPath(__NAMESPACE__) . 'scribd/scribd.php';
/**
 * API key : 5gc6g4z4e7wokpvjcbe31 API secret : sec-4tohbh6q34uplg867z4n0qsaxr
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{
    const BASIC_URL = 'http://api.scribd.org/api?';

    private $scribd;

    private $api_key;

    private $api_secret;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent::__construct($external_repository_instance);
        
        $this->api_key = Setting::get('api_key', $this->get_external_repository_instance_id());
        $this->api_secret = Setting::get('api_secret', $this->get_external_repository_instance_id());
        $username = Setting::get('username', $this->get_external_repository_instance_id());
        $password = Setting::get('password', $this->get_external_repository_instance_id());
        
        $this->scribd = new Scribd($this->api_key, $this->api_secret);
        $result = $this->scribd->login($username, $password);
    }

    /**
     *
     * @param $instance_id int
     * @return DataConnector:
     */
    // static function getInstance($instance_id)
    // {
    // if (! isset(self :: $instance[$instance_id]))
    // {
    // self :: $instance[$instance_id] = new self($instance_id);
    // }
    // return self :: $instance[$instance_id];
    // }
    
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
        // $offset = (($offset - ($offset % $count)) / $count) + 1;
        $response = $this->scribd->search($condition, $count, $offset, "all");
        $objects = array();
        
        foreach ($response->result_set->result as $result)
        {
            $scribd_object = new ExternalObject();
            $scribd_object->set_external_repository_id($this->get_external_repository_instance_id());
            $scribd_object->set_id((int) $result->doc_id);
            $scribd_object->set_owner_id(trim((string) $result->uploaded_by));
            $scribd_object->set_title(trim((string) $result->title));
            $scribd_object->set_description(trim((string) $result->description));
            $scribd_object->set_license((string) $result->license);
            $scribd_object->set_url((string) $result->thumbnail_url);
            $string_download_formats = (string) $result->download_formats;
            if (! empty($string_download_formats))
            {
                $scribd_object->set_download_formats(explode(',', $string_download_formats));
            }
            $scribd_object->set_created(strtotime($result->when_uploaded));
            $scribd_object->set_modified(strtotime($result->when_updated));
            $scribd_object->set_tags(explode(',', trim((string) $result->tags)));
            $scribd_object->set_type('scribd');
            $scribd_object->set_rights($this->determine_rights());
            $objects[] = $scribd_object;
            $id = (string) $scribd_object->get_id();
            $cache_path = Path::getInstance()->namespaceToFullPath(__NAMESPACE__) . 'files/cache/document/' . $id[0] .
                 '/' . $id[1] . '/';
            $cache_file = $cache_path . $id;
            if (! file_exists($cache_file) && filemtime($cache_file) < strtotime("-1 week"))
            {
                Filesystem::write_to_file($cache_file, serialize($scribd_object));
            }
        }
        return new ArrayResultSet($objects);
    }

    /**
     *
     * @param $condition mixed
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        $response = $this->scribd->search($condition, 1, 0, "all");
        return (int) ($response->result_set->attributes()->totalResultsAvailable);
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
        // $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);
        // $query = ActionBarSearchForm :: get_query();
        //
        // if (($feed_type == Manager :: FEED_TYPE_GENERAL && $query) ||
        // $feed_type ==
        // Manager :: FEED_TYPE_MY_PHOTOS)
        // {
        // return array(self :: SORT_DATE_POSTED,
        // self :: SORT_DATE_TAKEN,
        // self :: SORT_INTERESTINGNESS,
        // self :: SORT_RELEVANCE);
        // }
        // else
        // {
        return array();
        // }
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        $id = (string) $id;
        $cache_path = Path::getInstance()->namespaceToFullPath(__NAMESPACE__) . 'files/cache/document/' . $id[0] . '/' .
             $id[1] . '/';
        $cache_file = $cache_path . $id;
        
        if (file_exists($cache_file))
        {
            return unserialize(file_get_contents($cache_file));
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        return $this->scribd->sync_upload(
            $content_object->get_full_path(), 
            $content_object->get_title(), 
            $content_object->get_description());
    }

    /**
     *
     * @param $license int
     * @param $photo_user_id string
     * @return boolean
     */
    public function determine_rights()
    {
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = true;
        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = true;
        
        return $rights;
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->scribd->photos_delete($id);
    }

    public function download_external_repository_object($object, $download_format)
    {
        $response = $this->scribd->getDownloadUrl($object->get_id(), $download_format);
        $download_link = trim((string) $response->download_link);
        $extension = pathinfo(parse_url($download_link, PHP_URL_PATH), PATHINFO_EXTENSION);
        return array($extension, file_get_contents($download_link));
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        $this->scribd->changeSettings(
            $values[ExternalObject::PROPERTY_ID], 
            $values[ExternalObject::PROPERTY_TITLE], 
            $values[ExternalObject::PROPERTY_DESCRIPTION], 
            $values[ExternalObject::PROPERTY_TAGS]);
        return true;
    }

    /**
     *
     * @param $values array
     * @param $photo_path string
     * @return mixed
     */
    public function create_external_repository_object($values, $document_path)
    {
        $response = $this->scribd->upload($document_path);
        
        $doc_id = (int) $response->doc_id;
        $values[ExternalObject::PROPERTY_ID] = $doc_id;
        if ($this->update_external_repository_object($values))
        {
            $result = $this->scribd->getSettings($doc_id);
            
            $scribd_object = new ExternalObject();
            $scribd_object->set_external_repository_id($this->get_external_repository_instance_id());
            $scribd_object->set_id((int) $result->doc_id);
            $scribd_object->set_owner_id(trim((string) $result->uploaded_by));
            $scribd_object->set_title(trim((string) $result->title));
            $scribd_object->set_description(trim((string) $result->description));
            $scribd_object->set_license((string) $result->license);
            $scribd_object->set_url((string) $result->thumbnail_url);
            $string_download_formats = (string) $result->download_formats;
            if (! empty($string_download_formats))
            {
                $scribd_object->set_download_formats(explode(',', $string_download_formats));
            }
            $scribd_object->set_created(strtotime($result->when_uploaded));
            $scribd_object->set_modified(strtotime($result->when_updated));
            $scribd_object->set_tags(explode(',', trim((string) $result->tags)));
            $scribd_object->set_type('scribd');
            $scribd_object->set_rights($this->determine_rights());
            $objects[] = $scribd_object;
            
            $id = (string) $scribd_object->get_id();
            $cache_path = Path::getInstance()->namespaceToFullPath(__NAMESPACE__) . 'files/cache/document/' . $id[0] .
                 '/' . $id[1] . '/';
            $cache_file = $cache_path . $id;
            if (! file_exists($cache_file) && filemtime($cache_file) < strtotime("-1 week"))
            {
                Filesystem::write_to_file($cache_file, serialize($scribd_object));
            }
            
            return $doc_id;
        }
        else
        {
            return false;
        }
    }
}
