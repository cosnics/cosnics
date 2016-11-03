<?php
namespace Chamilo\Core\Repository\Implementation\Wikimedia;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @author Scaramanga Test developer key for Wikimedia: 61a0f40b9cb4c22ec6282e85ce2ae768 Test developer secret for
 *         Wikimedia: e267cbf5b7a1ad23
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var WikimediaRestClient
     */
    private $wikimedia;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = Setting :: get('url', $this->get_external_repository_instance_id());
        $this->wikimedia = new \GuzzleHttp\Client(['base_url' => $url]);

        $this->login();
    }

    public function login()
    {
        $parameters = array();
        $parameters['action'] = 'login';

        $login = Setting :: get('login', $this->get_external_repository_instance_id());
        $password = Setting :: get('password', $this->get_external_repository_instance_id());

        $request = $this->wikimedia->createRequest('POST', '');
        $parameters['lgname'] = $login;
        $parameters['lgpassword'] = $password;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $response = $this->wikimedia->send($request);
    }

    /**
     *
     * @param $instance_id int
     * @return DataConnector:
     */
    public static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new self($instance_id);
        }
        return self :: $instance[$instance_id];
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
        if (! $condition)
        {
            $condition = 'Looney Tunes';
        }

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = urlencode($condition);
        // define('WIKIMEDIA_FILE_NS', 6);
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = $count;
        $parameters['gsroffset'] = $offset;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp|url|dimensions|mime|user|userid|size';
        $parameters['iiurlwidth'] = 192;
        $parameters['iiurlheight'] = 192;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->get('', ['query' => $parameters]);
        $results = $result->xml();
        $objects = array();

        foreach ($result->query->pages->page as $page)
        {
            $objects[] = $this->get_image($page);
        }

        return new ArrayResultSet($objects);
    }

    protected function get_image($page)
    {
        $object = new ExternalObject();
        $object->set_id((int) $page->attributes()->pageid);
        $object->set_external_repository_id($this->get_external_repository_instance_id());

        $file_info = pathinfo(substr((string) $page->attributes()->title, 5));
        $object->set_title($file_info['filename']);
        $object->set_description($file_info['filename']);

        $time = strtotime((int) $page->imageinfo->ii->attributes()->timestamp);
        $object->set_created($time);
        $object->set_modified($time);
        $object->set_owner_id((string) $page->imageinfo->ii->attributes()->user);

        $photo_urls = array();

        $original_width = (int) $page->imageinfo->ii->attributes()->width;
        $original_height = (int) $page->imageinfo->ii->attributes()->height;

        if ($original_width <= 192)
        {
            $photo_urls[ExternalObject :: SIZE_THUMBNAIL] = array(
                'source' => (string) $page->imageinfo->ii->attributes()->url,
                'width' => $original_width,
                'height' => $original_height);
        }
        else
        {
            $photo_urls[ExternalObject :: SIZE_THUMBNAIL] = array(
                'source' => (string) $page->imageinfo->ii->attributes()->thumburl,
                'width' => (int) $page->imageinfo->ii->attributes()->thumbwidth,
                'height' => (int) $page->imageinfo->ii->attributes()->thumbheight);
        }

        if ($original_width <= 500)
        {
            $photo_urls[ExternalObject :: SIZE_MEDIUM] = array(
                'source' => (string) $page->imageinfo->ii->attributes()->url,
                'width' => $original_width,
                'height' => $original_height);
        }
        else
        {
            $thumbnail = $this->get_additional_thumbnail_url($page->imageinfo->ii->attributes()->thumburl, 500);
            $thumbnail_dimensions = ImageManipulation :: rescale($original_width, $original_height, 500, 500);

            $photo_urls[ExternalObject :: SIZE_MEDIUM] = array(
                'source' => $thumbnail,
                'width' => $thumbnail_dimensions[0],
                'height' => $thumbnail_dimensions[1]);
        }

        $photo_urls[ExternalObject :: SIZE_ORIGINAL] = array(
            'source' => (string) $page->imageinfo->ii->attributes()->url,
            'width' => $original_width,
            'height' => $original_height);
        $object->set_urls($photo_urls);

        $object->set_type($file_info['extension']);
        $object->set_rights($this->determine_rights());

        return $object;
    }

    protected function get_additional_thumbnail_url($url, $size)
    {
        $path_info = pathinfo($url);
        $filename = explode('-', $path_info['basename'], 2);
        return $path_info['dirname'] . '/' . $size . 'px-' . $filename[1];
    }

    /**
     *
     * @param $condition mixed
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        if (! $condition)
        {
            $condition = 'Looney Tunes';
        }

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = urlencode($condition);
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = 1;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->get('', ['query' => $parameters]);

        return $result->xml()->query->searchinfo->attributes()->totalhits;
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
        return null;
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['pageids'] = $id;
        $parameters['gsrnamespace'] = 6;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp|url|dimensions|mime|user|userid|size';
        $parameters['iiurlwidth'] = 192;
        $parameters['iiurlheight'] = 192;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->get('', ['query' => $parameters]);

        return $this->get_article($result->xml()->query->pages->page);
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        return true;
    }

    public function export_external_repository_object($id)
    {
        return true;
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
        $rights[ExternalObject :: RIGHT_USE] = true;
        $rights[ExternalObject :: RIGHT_EDIT] = false;
        $rights[ExternalObject :: RIGHT_DELETE] = false;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = true;

        return $rights;
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return true;
    }
}
