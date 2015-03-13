<?php
namespace Chamilo\Core\Repository\Implementation\Flickr;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use phpFlickr;

require_once Path :: getInstance()->getPluginPath(__NAMESPACE__) . 'phpflickr-3.1.1/phpFlickr.php';
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{
    const SORT_DATE_POSTED = 'date-posted';
    const SORT_DATE_TAKEN = 'date-taken';
    const SORT_INTERESTINGNESS = 'interestingness';
    const SORT_RELEVANCE = 'relevance';

    /**
     *
     * @var phpFlickr
     */
    private $flickr;

    /**
     *
     * @var string
     */
    private $key;

    /**
     *
     * @var string
     */
    private $secret;

    /**
     *
     * @var array
     */
    private $licenses;

    /**
     * The id of the user on Flickr
     *
     * @var string
     */
    private $user_id;

    private $session_token;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'key',
            $this->get_external_repository_instance_id());
        $this->secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'secret',
            $this->get_external_repository_instance_id());

        $this->flickr = new phpFlickr($this->key, $this->secret);
        // $this->flickr->enableCache('fs', Path :: getInstance()->getCachePath(__NAMESPACE__));

        $this->session_token = $external_repository_instance->get_setting('session_token')->get_value();

        $this->flickr->setToken($this->session_token);
    }

    public function login()
    {
        $frob = Request :: get('frob');

        if (! $frob)
        {
            $redirect = new Redirect();
            $currentUrl = $redirect->getCurrentUrl();
            $this->flickr->auth("delete", $currentUrl);
            return false;
        }
        else
        {
            $token = $this->flickr->auth_getToken($frob);

            if ($token['token']['_content'])
            {
                $setting = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_setting_from_variable_name(
                    'session_token',
                    $this->get_external_repository_instance_id());

                $setting->set_value($token['token']['_content']);
                if ($setting->update())
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
    }

    public function logout()
    {
        Session :: unregister('phpFlickr_auth_token');
        return true;
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
     * @return array:
     */
    public function retrieve_licenses()
    {
        if (! isset($this->licenses))
        {
            $raw_licenses = $this->flickr->photos_licenses_getInfo();

            $this->licenses = array();
            foreach ($raw_licenses as $raw_license)
            {
                $this->licenses[$raw_license['id']] = array(
                    'name' => $raw_license['name'],
                    'url' => $raw_license['url']);
            }
        }

        return $this->licenses;
    }

    /**
     *
     * @return string
     */
    public function retrieve_user_id()
    {
        if (! isset($this->user_id))
        {
            $hidden = $this->flickr->prefs_getHidden();
            if (is_null($hidden['nsid']))
            {
                $this->user_id = false;
            }
            else
            {
                $this->user_id = $hidden['nsid'];
            }
        }

        return $this->user_id;
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     * @return array
     */
    public function retrieve_photos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);

        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $attributes = 'description,date_upload,owner_name,license,media,original_format,last_update,url_sq,url_t,url_s,url_m,url_l,url_o';

        $search_parameters = array();
        $search_parameters['api_key'] = $this->key;
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;
        $search_parameters['text'] = $condition;
        $search_parameters['extras'] = $attributes;

        if ($order_property)
        {
            $order_direction = $this->convert_order_property($order_property);

            if ($order_direction)
            {
                $search_parameters['sort'] = $order_direction;
            }
        }

        switch ($feed_type)
        {
            case Manager :: FEED_TYPE_GENERAL :
                $photos = ($condition ? $this->flickr->photos_search($search_parameters) : $this->flickr->photos_getRecent(
                    null,
                    $attributes,
                    $count,
                    $offset));
                break;
            case Manager :: FEED_TYPE_MOST_INTERESTING :
                $photos = $this->flickr->interestingness_getList(null, null, $attributes, $count, $offset);
                break;
            case Manager :: FEED_TYPE_MOST_RECENT :
                $photos = $this->flickr->photos_getRecent(null, $attributes, $count, $offset);
                break;
            case Manager :: FEED_TYPE_MY_PHOTOS :
                // $search_parameters['user_id'] = 'me';
                $photos = $this->flickr->photos_search($search_parameters);
                break;
            default :
                if ($this->session_token)
                {
                    $search_parameters['user_id'] = 'me';
                    $photos = $this->flickr->photos_search($search_parameters);
                }
                else
                {
                    $photos = ($condition ? $this->flickr->photos_search($search_parameters) : $this->flickr->photos_getRecent(
                        null,
                        $attributes,
                        $count,
                        $offset));
                }
                break;
        }
        return $photos;
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
        $photos = $this->retrieve_photos($condition, $order_property, $offset, $count);
        // $licenses = $this->retrieve_licenses();
        $licenses = ExternalObject :: get_possible_licenses();

        $objects = array();

        foreach ($photos['photos']['photo'] as $photo)
        {
            $object = new ExternalObject();
            $object->set_id($photo['id']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($photo['title']);
            $object->set_description($photo['description']['_content']);
            $object->set_created($photo['dateupload']);
            $object->set_modified($photo['lastupdate']);
            $object->set_owner_id($photo['owner']);
            $object->set_owner_name($photo['ownername']);

            $photo_urls = array();
            foreach (ExternalObject :: get_possible_sizes() as $key => $size)
            {
                if (isset($photo['url_' . $key]))
                {
                    $photo_urls[$size] = array(
                        'source' => $photo['url_' . $key],
                        'width' => $photo['width_' . $key],
                        'height' => $photo['height_' . $key]);
                }
            }
            $object->set_urls($photo_urls);

            // $photo_size = array();
            // $photo_size['source'] = $photo['url_sq'];
            // $photo_size['width'] = 75;
            // $photo_size['height'] = 75;
            //
            // $object->set_urls(array('square' => $photo_size));
            //
            // $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
            // $photo_urls = array();
            //
            // foreach ($photo_sizes as $photo_size)
            // {
            // $key = strtolower($photo_size['label']);
            // unset($photo_size['label']);
            // unset($photo_size['media']);
            // unset($photo_size['url']);
            // $photo_urls[$key] = $photo_size;
            // }
            // $object->set_urls($photo_urls);

            $object->set_license($licenses[$photo['license']]);

            $types = array();
            $types[] = $photo['media'];
            if (isset($photo['originalformat']))
            {
                $types[] = strtolower($photo['originalformat']);
            }
            $object->set_type(implode('_', $types));
            $object->set_rights($this->determine_rights($photo['license'], $photo['owner']));

            $objects[] = $object;
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
        $photos = $this->retrieve_photos($condition, null, 1, 1);
        return $photos['photos']['total'];
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
                if ($order_property == ExternalObject :: PROPERTY_CREATED)
                {
                    $order_property = 'date-posted';
                }

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

        if (($feed_type == Manager :: FEED_TYPE_GENERAL && $query) || $feed_type == Manager :: FEED_TYPE_MY_PHOTOS)
        {
            $properties = array();
            $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), self :: SORT_DATE_POSTED);
            $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), self :: SORT_DATE_TAKEN);
            $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), self :: SORT_INTERESTINGNESS);
            $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), self :: SORT_RELEVANCE);
            return $properties;
        }
        else
        {
            return array();
        }
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        // $licenses = $this->retrieve_licenses();
        $licenses = ExternalObject :: get_possible_licenses();
        $photo = $this->flickr->photos_getInfo($id);
        $photo = $photo['photo'];

        $object = new ExternalObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($photo['id']);
        $object->set_title($photo['title']['_content']);
        $object->set_description($photo['description']['_content']);
        $object->set_created($photo['dateuploaded']);
        $object->set_modified($photo['dates']['lastupdate']);
        $object->set_owner_id($photo['owner']['nsid']);
        $object->set_owner_name($photo['owner']['username']);

        $tags = array();
        foreach ($photo['tags']['tag'] as $tag)
        {
            $tags[] = array('display' => $tag['raw'], 'text' => $tag['_content']);
        }
        $object->set_tags($tags);

        $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
        $photo_urls = array();

        foreach ($photo_sizes as $photo_size)
        {
            $key = strtolower($photo_size['label']);
            if (in_array($key, ExternalObject :: get_possible_sizes()))
            {
                unset($photo_size['label']);
                unset($photo_size['media']);
                unset($photo_size['url']);
                $photo_urls[$key] = $photo_size;
            }
        }

        $object->set_urls($photo_urls);
        $object->set_license($licenses[$photo['license']]);

        $types = array();
        $types[] = $photo['media'];
        if (isset($photo['originalformat']))
        {
            $types[] = strtolower($photo['originalformat']);
        }
        $object->set_type(implode('_', $types));

        $object->set_rights($this->determine_rights($photo['license'], $photo['owner']['nsid']));

        return $object;
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        $success = $this->flickr->photos_setMeta(
            $values[ExternalObject :: PROPERTY_ID],
            $values[ExternalObject :: PROPERTY_TITLE],
            $values[ExternalObject :: PROPERTY_DESCRIPTION]);

        if (! $success)
        {
            return false;
        }
        else
        {
            $tags = explode(',', $values[ExternalObject :: PROPERTY_TAGS]);
            $tags = '"' . implode('" "', $tags) . '"';

            $success = $this->flickr->photos_setTags($values[ExternalObject :: PROPERTY_ID], $tags);

            if (! $success)
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param $values array
     * @param $photo_path string
     * @return mixed
     */
    public function create_external_repository_object($values, $photo_path)
    {
        $tags = explode(',', $values[ExternalObject :: PROPERTY_TAGS]);
        $tags = '"' . implode('" "', $tags) . '"';

        return $this->flickr->sync_upload(
            $photo_path,
            $values[ExternalObject :: PROPERTY_TITLE],
            $values[ExternalObject :: PROPERTY_DESCRIPTION],
            $tags);
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        return $this->flickr->sync_upload(
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
    public function determine_rights($license, $photo_user_id)
    {
        $users_match = ($this->retrieve_user_id() == $photo_user_id ? true : false);
        // $compatible_license = ($license == 0 ? false : true);
        $compatible_license = true;

        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = $compatible_license || $users_match;
        $rights[ExternalObject :: RIGHT_EDIT] = $users_match;
        $rights[ExternalObject :: RIGHT_DELETE] = $users_match;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = $compatible_license || $users_match;

        return $rights;
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->flickr->photos_delete($id);
    }
}