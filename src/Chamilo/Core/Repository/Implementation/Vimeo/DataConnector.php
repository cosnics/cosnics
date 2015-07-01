<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Vimeo\Vimeo;
use Chamilo\Core\Repository\Implementation\Youtube\PageTokenGenerator;

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

        // $offset = (($offset - ($offset % $count)) / $count) + 1;
        $pageNumber = ($offset / $count) + 1;

        $pageToken = PageTokenGenerator :: getInstance()->getToken($count, $pageNumber);

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
                $videos = $this->vimeo->request('/me/videos');
                break;
            case Manager :: FEED_TYPE_GENERAL :
                $search_parameters['query'] = $condition ? $condition : 'chamilo';

                $videos = $this->vimeo->request('/videos', $search_parameters);
                break;
            default :
                if ($condition)
                {
                    $search_parameters['query'] = $condition;

                    $videos = $this->vimeo->request('/me/videos', $search_parameters);
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

    // public function get_user_info()
    // {
    // if (! isset($this->user))
    // {
    // $token = $this->vimeo->getToken();
    // $response = $this->vimeo->request('/users', array('user_id' => $token[0]));
    // $this->user = $response->person;
    // }
    // return $this->user;
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
        $videos = $this->retrieve_videos($condition, $order_property, $offset, $count);

        $videos_id = array();
        foreach ($videos['body']['data'] as $video)
        {
            $object = new ExternalObject();
            $object->set_external_repository_id($this->get_external_repository_instance_id());

            $video_id = explode('/videos/', $video['uri']);
            $object->set_id($video_id[1]);
            $object->set_title($video['name']);
            $object->set_description(nl2br($video['description']));
            $object->set_created(strtotime($video['created_time']));
            $object->set_modified(strtotime($video['modified_time']));
            $object->set_duration($video['duration']);
            $video_user = explode('/users/', $video['user']['uri']);
            $object->set_owner_id($video_user[1]);
            $object->set_owner_name($video['user']['name']);
            $object->set_urls($video['link']);

            foreach ($video['tags'] as $tag)
            {
                $tags[] = $tag['name'];
            }
            $object->set_tags($tags);
            $object->set_type('video');
            $object->set_thumbnail($video['pictures']['sizes'][1]['link']);

            $object->set_rights($this->determine_rights($video));
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
        return $videos['body']['total'];
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

        // if ($feed_type == Manager :: FEED_TYPE_MY_PHOTOS)
        // {
        // return array(
        // self :: SORT_DATE_POSTED,
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
        $video = $this->vimeo->request('/videos', array('query' => $id));
        $video = $video['body']['data'][0];

        $object = new ExternalObject();

        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $video_id = explode('/videos/', $video['uri']);
        $object->set_id($video_id[1]);
        $object->set_title($video['name']);
        $object->set_description(nl2br($video['description']));
        $object->set_created(strtotime($video['created_time']));
        $object->set_modified(strtotime($video['modified_time']));
        $object->set_duration($video['duration']);
        $video_user = explode('/users/', $video['user']['uri']);
        $object->set_owner_id($video_user[1]);
        $object->set_owner_name($video['user']['name']);
        $object->set_urls($video['link']);

        foreach ($video['tags'] as $tag)
        {
            $tags[] = $tag['name'];
        }
        $object->set_tags($tags);

        $object->set_thumbnail($video['pictures']['sizes'][4]['link']);

        $object->set_rights($this->determine_rights($video));

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
        explode('/videos/', $video_id);
        $video_id = $video_id[1];
        $response = $this->vimeo->request(
            '/videos',
            array('description' => $values['description'], 'video_id' => $video_id),
            'PATCH');
        if (! $response->stat == 'ok')
        {
            return false;
        }
        else
        {
            $response = $this->vimeo->request(
                '/videos',
                array('name' => $values['title'], 'video_id' => $video_id),
                'PATCH');
            if (! $response->stat == 'ok')
            {
                return false;
            }
            else
            {
                $response = $this->vimeo->request('/videos', array('video_id' => $video_id));
                if ($response->stat == 'ok')
                {
                    $response = $this->vimeo->call(
                        '/videos',
                        array('video_id' => $video_id, 'tags' => $values['tags']),
                        'PUT');
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

        $response = $this->vimeo->request(
            '/videos',
            array('description' => $content_object->get_description(), 'video_id' => $video_id),
            'PATCH');
        if (! $response->stat == 'ok')
        {
            return false;
        }
        else
        {
            $response = $this->vimeo->call(
                '/videos',
                array('name' => $content_object->get_title(), 'video_id' => $video_id),
                PATCH);
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
        return $this->vimeo->request('/videos', array('video_id' => $id), 'DELETE');
    }
}
