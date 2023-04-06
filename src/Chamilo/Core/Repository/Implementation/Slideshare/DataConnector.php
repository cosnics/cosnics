<?php
namespace Chamilo\Core\Repository\Implementation\Slideshare;

use Chamilo\Core\Repository\Implementation\Slideshare\DataConnector\RestClient;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{
    const SORT_RELEVANCE = 'relevance';

    /**
     * @var \GuzzleHttp\Client
     */
    private $slideshare;

    private $consumer_key;

    private $consumer_secret;

    private $user;

    private $password;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent::__construct($external_repository_instance);
        
        $this->consumer_key = Setting::get('consumer_key', $this->get_external_repository_instance_id());
        $this->consumer_secret = Setting::get('consumer_secret', $this->get_external_repository_instance_id());
        
        $this->slideshare = new \GuzzleHttp\Client(['base_uri' => 'https://www.slideshare.net/api/2/']);
        $this->login();
    }

    public function login()
    {
        $login = Setting::get('username', $this->get_external_repository_instance_id());
        $password = Setting::get('password', $this->get_external_repository_instance_id());
        
//        $request = $this->slideshare->request('POST', '');
//        $postBody = $request->getBody();
//        $postBody->setField('action', 'login');
//        $postBody->setField('lgname', $login);
//        $postBody->setField('lgpassword', $password);
//        $postBody->setField('format', 'xml');
//        $postBody->setField('redirects', true);
//
//        $response = $this->slideshare->send($request);
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
        if (is_null($condition))
        {
            $condition = 'default';
        }
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
        $params = array();
        $params['api_key'] = $this->consumer_key;
        $params['ts'] = $date;
        $params['hash'] = $hash;
        $params['tag'] = $condition;
        $params['limit'] = $count;
        $params['offset'] = $offset;
        
        $result = $this->slideshare->get('get_slideshows_by_tag', ['query' => $params]);
        $slideshows = (array) $result->get_response_content();
        
        $objects = array();
        foreach ($slideshows['Slideshow'] as $slideshow)
        {
            $objects[] = $this->get_slideshow($slideshow);
        }
        return new ArrayResultSet($objects);
    }

    public function get_slideshow($slideshow)
    {
        $slideshow = (array) $slideshow;
        $object = new ExternalObject();
        $object->set_id((int) $slideshow['ID']);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        
        $object->set_title((string) $slideshow['Title']);
        $object->set_description((string) $slideshow['Description']);
        $object->set_created($slideshow['Created']);
        $object->set_modified($slideshow['Updated']);
        
        $object->set_urls((string) $slideshow['URL']);
        $object->set_thumbnail($slideshow['ThumbnailSmallURL']);
        
        $object->set_rights($this->determine_rights());
        return $object;
    }

    /**
     *
     * @param $condition mixed
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        if (is_null($condition))
        {
            $condition = 'default';
        }
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
        $params = array();
        $params['api_key'] = $this->consumer_key;
        $params['ts'] = $date;
        $params['hash'] = $hash;
        $params['tag'] = $condition;
        
        $result = $this->slideshare->get('get_slideshows_by_tag', ['query' => $params]);
        $slideshows = (array) $result->get_response_content();
        
        $objects = array();
        $count = 0;
        foreach ($slideshows['Slideshow'] as $slideshow)
        {
            $count ++;
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
        
        return array();
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
        $params = array();
        $params['slideshow_id'] = $id;
        $params['api_key'] = $this->consumer_key;
        $params['ts'] = $date;
        $params['hash'] = $hash;
        $slideshow = $this->slideshare->get('get_slideshow', ['query' => $params]);
        $slideshow = (array) $slideshow->get_response_content();
        
        $object = new ExternalObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($slideshow['ID']);
        $object->set_title($slideshow['Title']);
        $object->set_description($slideshow['Description']);
        $object->set_created($slideshow['Created']);
        $object->set_modified($slideshow['Updated']);
        $object->set_owner_id($slideshow['Username']);
        $object->set_urls($slideshow['URL']);
        $object->set_embed($slideshow['Embed']);
        $object->set_rights($this->determine_rights());
        
        return $object;
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        /*
         * $date = time(); $hash = sha1($this->consumer_secret . $date); $params = array(); $params['api_key'] =
         * $this->consumer_key; $params['ts'] = $date; $params['hash'] = $hash; $params['slideshow_id'] =
         * $values[ExternalObject::PROPERTY_ID]; $result = $this->slideshare->send_request(SlideshareRestClient ::
         * METHOD_GET, 'edit_slideshow', $params); $slideshows = (array) $result->get_response_content();
         */
    }

    /**
     *
     * @param $values array
     * @return mixed
     */
    public function create_external_repository_object($values, $slideshow)
    {
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
        $params = array();
        $params['api_key'] = $this->consumer_key;
        $params['ts'] = $date;
        $params['hash'] = $hash;
        $params['username'] = $this->user;
        $params['password'] = $this->password;
        $params['slideshow_title'] = $values['title'];
        $params['slideshow_srcfile'] = file_get_contents($slideshow['tmp_name']);
        $this->slideshare->add_header('Content-Type', 'multipart/form-data');
        $this->slideshare->add_header('enctype', 'multipart/form-data');
        $slideshow1 = $this->slideshare->send_request(RestClient::METHOD_POST, 'upload_slideshow', $params);
        /*
         * $slideshow1 = $slideshow1->get_response_content();
         */
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
    }

    /**
     *
     * @param $license int
     * @return boolean
     */
    public function determine_rights()
    {
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = true;
        $rights[ExternalObject::RIGHT_EDIT] = true;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        /*
         * $date = time(); $hash = sha1($this->consumer_secret . $date); $params = array(); $params['api_key'] =
         * $this->consumer_key; $params['ts'] = $date; $params['hash'] = $hash; $params['slideshow_id'] = $id;
         * $params['username'] = $this->user; $params['password'] = $this->password; $slideshow =
         * $this->slideshare->send_request(SlideshareRestClient :: METHOD_GET, 'delete_slideshow', $params); $slideshow
         * =
         * (array) $slideshow->get_response_content();
         */
    }

    public function download_external_repository_object($id)
    {
    }
}
