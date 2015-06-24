<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Vimeo\Vimeo;

/**
 * Consumer Key: Consumer Secret:
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{
    const SORT_DATE_POSTED = 'date-posted';
    const SORT_DATE_TAKEN = 'date-taken';
    const SORT_INTERESTINGNESS = 'interestingness';
    const SORT_RELEVANCE = 'relevance';
    //
    private $vimeo;

    private $consumer_key;

    private $consumer_secret;

    private $token;

    private $user;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->consumer_key = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'consumer_key',
            $this->get_external_repository_instance_id());
        $this->consumer_secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'consumer_secret',
            $this->get_external_repository_instance_id());

        $this->vimeo = new Vimeo($this->consumer_key, $this->consumer_secret, 'f0259caafb186aec05e8d0b492584348');

        // $oauth_token = Setting :: get('oauth_token', $this->get_external_repository_instance_id());
        // $oauth_token_secret = Setting :: get('oauth_token_secret', $this->get_external_repository_instance_id());

        // if (! $oauth_token || ! $oauth_token_secret)
        // {
        // if (! $_SESSION['request_token'])
        // {
        // $redirect = new Redirect();
        // $currentUrl = $redirect->getCurrentUrl();

        // $redirect->writeHeader($this->vimeo->buildAuthorizationEndpoint($currentUrl, 'delete'));
        // }
        // else
        // {
        // $this->vimeo->setToken($_SESSION['request_token'], $_SESSION['request_token_secret'], 'access', true);
        // var_dump($_GET);
        // $this->token = $this->vimeo->getAccessToken($_GET['oauth_verifier']);

        // $user_setting = new Setting();
        // $user_setting->set_external_id($this->get_external_repository_instance_id());
        // $user_setting->set_variable('oauth_token');
        // $user_setting->set_user_id(Session :: get_user_id());
        // $user_setting->set_value($this->token['oauth_token']);
        // $user_setting->create();

        // $user_setting = new Setting();
        // $user_setting->set_external_id($this->get_external_repository_instance_id());
        // $user_setting->set_variable('oauth_token_secret');
        // $user_setting->set_user_id(Session :: get_user_id());
        // $user_setting->set_value($this->token['oauth_token_secret']);
        // $user_setting->create();
        // }
        // }
        // else
        // {
        // $this->vimeo->setToken($oauth_token, $oauth_token_secret, 'access');
        // }
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
     * @return array
     */
    public function retrieve_videos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);

        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $attributes = 'description,upload_date,modified_date,owner';

        $search_parameters = array();
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;

        if ($order_property)
        {

            $order_direction = $this->convert_order_property($order_property);

            if ($order_direction)
            {
                $search_parameters['sort'] = $order_direction;
            }
        }
        // videos for the current user.
        switch ($feed_type)
        {
            case Manager :: FEED_TYPE_MY_PHOTOS :

                if ($condition)
                {
                    $search_parameters['query'] = $condition;
                    $search_parameters['user_id'] = $this->get_user_info()->id;

                    $videos = $this->vimeo->call('vimeo.videos.search', $search_parameters);
                }
                else
                {
                    $videos = $this->vimeo->call('vimeo.videos.getAll');
                }
                break;
            case Manager :: FEED_TYPE_GENERAL :
                $search_parameters['query'] = $condition ? $condition : 'chamilo';

                $videos = $this->vimeo->call('vimeo.videos.search', $search_parameters);
                break;
            default :
                if ($condition)
                {
                    $search_parameters['query'] = $condition;
                    $search_parameters['user_id'] = $this->get_user_info()->id;

                    $videos = $this->vimeo->call('vimeo.videos.search', $search_parameters);
                }
                else
                {
                    // get all videos
                    $search_parameters['query'] = 'test';
                    $videos = $this->vimeo->request('/videos', $search_parameters);

                }
                break;
        }
        return $videos;
    }

    public function get_user_info()
    {
        if (! isset($this->user))
        {
            $token = $this->vimeo->getToken();
            $response = $this->vimeo->call('vimeo.people.getInfo', array('user_id' => $token[0]));
            $this->user = $response->person;
        }
        return $this->user;
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
        $videos = $this->retrieve_videos($condition, $order_property, $offset, $count);
        $videos_id = array();
        foreach ($videos['body']['data'] as $video)
        {
            $video = $video_info->video[0];
            $object = new ExternalObject();
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_id($videos['body']['data'][0]['uri']);
            $object->set_title($videos['body']['data'][0]['name']);
            $object->set_description($videos['body']['data'][0]['description']);
            $object->set_created(strtotime($videos['body']['data'][0]['created_time']));
            $object->set_modified(strtotime($videos['body']['data'][0]['modified_time']));
            $object->set_duration($videos['body']['data'][0]['duration']);
            $object->set_owner_id($videos['body']['data'][0]['user']['name']);
            $object->set_urls($videos['body']['data'][0]['link']);
            foreach ($videos['body']['data'][0]['tags'] as $tag)
            {
                $tags[] = $tag['name'];
            }
            $object->set_tags($tags);
            $object->set_type('video');

            // $object->set_thumbnail($video->thumbnails->thumbnail[1]->_content);

            // $object->set_rights($this->determine_rights($video));
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
        $videos = $this->retrieve_videos($condition, null, 1, 1);
        return $videos->videos->total;
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

        if ($feed_type == Manager :: FEED_TYPE_MY_PHOTOS)
        {
            return array(
                self :: SORT_DATE_POSTED,
                self :: SORT_DATE_TAKEN,
                self :: SORT_INTERESTINGNESS,
                self :: SORT_RELEVANCE);
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
        $video = $this->vimeo->call('vimeo.videos.getInfo', array('video_id' => $id));
        $video = $video->video[0];
var_dump($video);
        $video = $videos['body']['data'][0];
        $object = new ExternalObject();

        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($video['body']['data'][0]['uri']);
        $object->set_title($video['body']['data'][0]['name']);
        $object->set_description($video['body']['data'][0]['description']);
        $object->set_created(strtotime($video['body']['data'][0]['created_time']));
        $object->set_modified(strtotime($video['body']['data'][0]['modified_time']));
        $object->set_duration($video['body']['data'][0]['duration']);
        $object->set_owner_id($video['body']['data'][0]['user']['name']);
        $object->set_urls($video['body']['data'][0]['link']);
        foreach ($video['body']['data'][0]['tags'] as $tag)
        {
            $tags[] = $tag['name'];
        }
        $object->set_tags($tags);

        // $object->set_thumbnail($video->thumbnails->thumbnail[2]->_content);

        // $object->set_rights($this->determine_rights($video));

        return $object;
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        $response = $this->vimeo->call(
            'vimeo.videos.setDescription',
            array('description' => $values['description'], 'video_id' => $values['id']));
        if (! $response->stat == 'ok')
        {
            return false;
        }
        else
        {
            $response = $this->vimeo->call(
                'vimeo.videos.setTitle',
                array('title' => $values['title'], 'video_id' => $values['id']));
            if (! $response->stat == 'ok')
            {
                return false;
            }
            else
            {
                $response = $this->vimeo->call('vimeo.videos.clearTags', array('video_id' => $values['id']));
                if ($response->stat == 'ok')
                {
                    $response = $this->vimeo->call(
                        'vimeo.videos.addTags',
                        array('video_id' => $values['id'], 'tags' => $values['tags']));
                    if (! $response->stat == 'ok')
                    {
                        return false;
                    }
                }
                return true;
            }
        }
    }

    /**
     *
     * @param $values array
     * @param $photo_path string
     * @return mixed
     */
    public function create_external_repository_object($values, $video_path)
    {
        $video_id = $this->vimeo->upload($video_path);
        $response = $this->vimeo->call(
            'vimeo.videos.setDescription',
            array('description' => $values['description'], 'video_id' => $video_id));

        if (! $response->stat == 'ok')
        {
            return false;
        }
        else
        {
            $response = $this->vimeo->call(
                'vimeo.videos.setTitle',
                array('title' => $values['title'], 'video_id' => $video_id));
            if (! $response->stat == 'ok')
            {
                return false;
            }
            else
            {
                $response = $this->vimeo->call('vimeo.videos.clearTags', array('video_id' => $video_id));
                if ($response->stat == 'ok')
                {
                    $response = $this->vimeo->call(
                        'vimeo.videos.addTags',
                        array('video_id' => $video_id, 'tags' => $values['tags']));
                    if (! $response->stat == 'ok')
                    {
                        return false;
                    }
                }

                return true;
            }
        }
    }

    /**
     *
     * @param $content_object ContentObject
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        $video_id = $this->vimeo->upload($content_object->get_full_path());

        $response = $this->vimeo->call(
            'vimeo.videos.setDescription',
            array('description' => $content_object->get_description(), 'video_id' => $video_id));
        if (! $response->stat == 'ok')
        {
            return false;
        }
        else
        {
            $response = $this->vimeo->call(
                'vimeo.videos.setTitle',
                array('title' => $content_object->get_title(), 'video_id' => $video_id));
            if (! $response->stat == 'ok')
            {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param $license int
     * @param $photo_user_id string
     * @return boolean
     */
    public function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = true;
        $rights[ExternalObject :: RIGHT_EDIT] = true;
        $rights[ExternalObject :: RIGHT_DELETE] = true;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     *
     * @param $id string
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->vimeo->call('vimeo.videos.delete', array('video_id' => $id));
    }
}
