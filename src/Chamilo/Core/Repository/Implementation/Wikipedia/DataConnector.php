<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var WikipediaRestClient
     */
    private $wikipedia;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = Setting :: get('url', $this->get_external_repository_instance_id());

        $this->wikipedia = new \GuzzleHttp\Client(['base_url' => $url]);

        $this->login();
    }

    public function login()
    {
        $login = Setting :: get('login', $this->get_external_repository_instance_id());
        $password = Setting :: get('password', $this->get_external_repository_instance_id());

        $request = $this->wikipedia->createRequest('POST', '');
        $postBody = $request->getBody();
        $postBody->setField('action', 'login');
        $postBody->setField('lgname', $login);
        $postBody->setField('lgpassword', $password);
        $postBody->setField('format', 'xml');
        $postBody->setField('redirects', true);

        $response = $this->wikipedia->send($request);
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
        $parameters['gsrnamespace'] = 0;
        $parameters['gsrlimit'] = $count;
        $parameters['gsroffset'] = $offset;
        $parameters['prop'] = 'info';
        $parameters['inprop'] = 'url';
        $parameters['format'] = 'xml';
        $parameters['export'] = 'export';
        $parameters['redirects'] = true;

        $result = $this->wikipedia->get('', ['query' => $parameters]);
        $results = $result->xml();
        $objects = array();

        foreach ($results->query->pages->page as $page)
        {
            $objects[] = $this->get_article($page);
        }
        return new ArrayResultSet($objects);
    }

    protected function get_article($page)
    {
        $object = new ExternalObject();
        $object->set_id((int) $page->attributes()->pageid);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $file_info = pathinfo(substr((string) $page->attributes()->title, 5));

        $object->set_title((string) $page->attributes()->title);
        $object->set_description((string) $page->attributes()->title);
        $time = strtotime((int) $page->attributes()->touched);
        $object->set_created($time);
        $object->set_modified($time);
        $object->set_type('wikipedia');
        $object->set_urls((string) str_replace('&action=edit', '', $page->attributes()->editurl));
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
        if (! $condition)
        {
            $condition = 'Looney Tunes';
        }

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = urlencode($condition);
        $parameters['gsrnamespace'] = 0;
        $parameters['gsrlimit'] = 1;
        $parameters['prop'] = 'info';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikipedia->get('', ['query' => $parameters]);

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

    /**
     *
     * @return array
     */
    public static function get_sort_properties()
    {
        return array();
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
        $parameters['gsrnamespace'] = 0;
        $parameters['prop'] = 'info';
        $parameters['inprop'] = 'url';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikipedia->get('', ['query' => $parameters]);

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

    public function download_external_repository_object($external_object)
    {
        $url = $external_object->get_render_url();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Chamilo2Bot/1.0');
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
