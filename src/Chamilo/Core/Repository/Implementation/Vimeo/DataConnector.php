<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use ArrayIterator;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
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
        parent::__construct($external_repository_instance);

        $this->consumer_key = Setting::get(
            'consumer_key', $this->get_external_repository_instance_id()
        );
        $this->consumer_secret = Setting::get(
            'consumer_secret', $this->get_external_repository_instance_id()
        );

        $this->vimeo = new Vimeo($this->consumer_key, $this->consumer_secret, 'f0259caafb186aec05e8d0b492584348');
    }

    /**
     *
     * @param $order_properties ObjectTableOrder
     *
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
     * @param $condition mixed
     *
     * @return int
     */
    public function count_external_repository_objects($condition)
    {
        $videos = $this->retrieve_videos($condition, null, 1, 1);

        return $videos['body']['total'];
    }

    /**
     *
     * @param $values array
     * @param $photo_path string
     *
     * @return mixed
     */
    public function create_external_repository_object($values, $video_path)
    {
        $video_id = $this->vimeo->upload($video_path);
        $video_explode = explode('/videos/', $video_id);
        $video_id = $video_explode[1];

        $response = $this->vimeo->request(
            '/videos/' . $video_id, array('description' => $values['description'], 'name' => $values['title']), 'PATCH'
        );

        if ($response['status'] == 200)
        {
            $tags = $values['tags'];

            $tags = explode(', ', $tags);

            foreach ($tags as $tag)
            {
                $tags[] = $tag;
            }
            $tags = json_encode($tags);

            $response = $this->vimeo->request(
                '/videos/' . $video_id . '/tags/', array('body' => json_encode($tags)), 'PUT'
            );

            // if ($response['status'] == 200)
            // {
            // return true;
            // }
            // elseif ($response['status'] == 400)
            // {
            // error_log(Translation::get('InvalidTypeForTags'));
            // return false;
            // }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param $id string
     *
     * @return mixed
     */
    public function delete_external_repository_object($id)
    {
        return $this->vimeo->request('/videos/' . $id, [], 'DELETE');
    }

    /**
     *
     * @param $license int
     * @param $photo_user_id string
     *
     * @return boolean
     */
    public function determine_rights($video_entry)
    {
        $rights = [];

        if (ContentObject::is_available(
            \Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo::class
        ))
        {
            $rights[ExternalObject::RIGHT_USE] = true;
        }

        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = false;

        return $rights;
    }

    /**
     *
     * @param $content_object ContentObject
     *
     * @return mixed
     */
    public function export_external_repository_object($content_object)
    {
        $video_id = $this->vimeo->upload($content_object->get_full_path());

        $response = $this->vimeo->request(
            '/videos/' . $video_id,
            array('description' => $content_object->get_description(), 'name' => $content_object->get_title()), 'PATCH'
        );

        return true;
    }

    /**
     *
     * @param $instance_id int
     *
     * @return DataConnector:
     */
    public static function getInstance($instance_id)
    {
        if (!isset(self::$instance[$instance_id]))
        {
            self::$instance[$instance_id] = new self($instance_id);
        }

        return self::$instance[$instance_id];
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_object()
     */

    /**
     *
     * @return array
     */
    public static function get_sort_properties()
    {
        $feed_type = Request::get(Manager::PARAM_FEED_TYPE);
        $query = ButtonSearchForm::get_query();

        // if ($feed_type == Manager::FEED_TYPE_MY_PHOTOS)
        // {
        // return array(
        // self::SORT_DATE_POSTED,
        // self::SORT_DATE_TAKEN,
        // self::SORT_INTERESTINGNESS,
        // self::SORT_RELEVANCE);
        // }
        // else
        // {
        return [];
        // }
    }

    public function retrieve_external_repository_object($id)
    {
        $video = $this->vimeo->request('/videos/' . $id);

        $video = $video['body'];

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
            $array_tags[] = $tag['tag'];
        }
        $object->set_tags($array_tags);
        $object->set_thumbnail($video['pictures']['sizes'][4]['link']);

        $object->set_rights($this->determine_rights($video));

        return $object;
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property \Chamilo\Libraries\Storage\Query\OrderBy
     * @param $offset int
     * @param $count int
     *
     * @return \ArrayIterator
     */
    public function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        $videos = $this->retrieve_videos($condition, $order_property, $offset, $count);

        $videos_id = [];
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
            $object->set_tags($video['tags']);
            $object->setType('video');
            $object->set_thumbnail($video['pictures']['sizes'][1]['link']);

            $object->set_rights($this->determine_rights($video));
            $objects[] = $object;
        }

        return new ArrayIterator($objects);
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     *
     * @return array
     */
    public function retrieve_videos($condition = null, $order_property, $offset, $count)
    {
        $feed_type = Request::get(Manager::PARAM_FEED_TYPE);

        $offset = (($offset - ($offset % $count)) / $count) + 1;
        $search_parameters = [];
        $search_parameters['per_page'] = $count;
        $search_parameters['page'] = $offset;

        // if ($order_property)
        // {
        // $order_direction = $this->convert_order_property($order_property);

        // if ($order_direction)
        // {
        // $search_parameters['sort'] = $order_direction;
        // }
        // }
        // videos for the current user.
        switch ($feed_type)
        {
            case Manager::FEED_TYPE_MY_PHOTOS :
                if ($condition)
                {
                    $search_parameters['query'] = $condition;
                }

                $videos = $this->vimeo->request('/me/videos', $search_parameters);

                break;
            case Manager::FEED_TYPE_GENERAL :
                $search_parameters['query'] = $condition ?: 'chamilo';

                $videos = $this->vimeo->request('/videos', $search_parameters);
                break;
            default :
                if ($condition)
                {
                    $search_parameters['query'] = $condition;
                }
                else
                {
                    // get all videos
                    $search_parameters['query'] = 'physics';
                }
                $videos = $this->vimeo->request('/videos', $search_parameters);

                break;
        }

        return $videos;
    }

    /**
     *
     * @param $query string
     *
     * @return string
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    /**
     *
     * @param $values array
     *
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        $response = $this->vimeo->request(
            '/videos/' . $values['id'], array('description' => $values['description'], 'name' => $values['title']),
            'PATCH'
        );

        foreach (explode(', ', $values['tags']) as $tag)
        {
            $array_tags[] = $tag;
        }
        $array_tags = (json_encode($array_tags));

        $response = $this->vimeo->request('/videos/' . $values['id'] . '/tags/', array('tags' => $array_tags), 'PUT');

        if ($response['status'] == 200)
        {
            return true;
        }
        if ($response['status'] == 400)
        {
            return error_log(Translation::get('InvalidTypeForTags'));
        }
    }
}
