<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Platform\Session\Session;

// YoutubeKey :
// AI39si4OLUsiI2mK0_k8HxqOtv0ctON-PzekhP_56JDkdph6wZ9tW2XqzDD7iVYY0GXKdMKlPSJyYZotNQGleVfRPDZih41Tug
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $youtube;

    private $session_token;
    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';

    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $key = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'developer_key',
            $this->get_external_repository_instance_id());

        $this->client = new \Google_Client();
        $this->client->setDeveloperKey($key);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_VARIABLE),
            new StaticConditionVariable('session_token'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_USER_ID),
            new StaticConditionVariable(Session :: get_user_id()));
        $condition = new AndCondition($conditions);

        $setting = DataManager :: retrieve(Setting :: class_name(), new DataClassRetrieveParameters($condition));
        if ($setting instanceof Setting)
        {
            $this->client->setAccessToken($setting->get_value());
        }

        $this->youtube = new \Google_Service_YouTube($this->client);
    }

    public function login()
    {
        $client_id = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_id',
            $this->get_external_repository_instance_id());
        $client_secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'client_secret',
            $this->get_external_repository_instance_id());

        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setScopes('https://www.googleapis.com/auth/youtube');

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: package(),
                Manager :: PARAM_ACTION => Manager :: ACTION_LOGIN,
                Manager :: PARAM_EXTERNAL_REPOSITORY => $this->get_external_repository_instance_id()));

        $this->client->setRedirectUri($redirect->getUrl());

        $this->youtube = new \Google_Service_YouTube($this->client);

        $code = Request :: get('code');

        if (isset($code))
        {
            $this->client->authenticate($code);
            $token = $this->client->getAccessToken();
            $this->client->setAccessToken($token);

            if (!$token)
            {
                $refresh_token = $this->client->getRefreshToken();
                $this->client->refreshToken($refresh_token);
            }


            $user_setting = new Setting();
            $user_setting->set_user_id(Session :: get_user_id());
            $user_setting->set_variable('session_token');
            $user_setting->set_value($token);

            return $user_setting->create();
        }
        else
        {
            $url = $this->client->createAuthUrl('https://www.googleapis.com/auth/youtube');
            header('Location: ' . $url);
            exit();
        }
    }

    public static function get_sort_properties()
    {
        // return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
        return array();
    }

    public function is_editable($id)
    {
        $videoEntry = $this->get_youtube_video_entry($id);
        if ($videoEntry->getEditLink() !== null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function translate_search_query($query)
    {
        return $query;
    }

    public function retrieve_categories()
    {
        $categories = $this->youtube->videoCategories->listVideoCategories('id,snippet', array('id' => 'test'));
        return $categories['modelData']['items'];
    }

    public function get_upload_token($values)
    {
        // $video_entry = new Zend_Gdata_YouTube_VideoEntry();

        // $video_entry->setVideoTitle($values[ExternalObjectForm :: VIDEO_TITLE]);
        // $video_entry->setVideoCategory($values[ExternalObjectForm :: VIDEO_CATEGORY]);
        // $video_entry->setVideoTags($values[ExternalObjectForm :: VIDEO_TAGS]);
        // $video_entry->setVideoDescription($values[ExternalObjectForm :: VIDEO_DESCRIPTION]);

        // $token_handler_url = 'http://gdata.youtube.com/action/GetUploadToken';
        // $token_array = $this->youtube->getFormUploadToken($video_entry, $token_handler_url);
        // $token_value = $token_array['token'];
        // $post_url = $token_array['url'];

        // return $token_array;
        return array();
    }

    public function get_video_feed($query)
    {
        $feed = Request :: get(Manager :: PARAM_FEED_TYPE);
        switch ($feed)
        {
            case Manager :: FEED_TYPE_GENERAL :
                return @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
                break;
            case Manager :: FEED_TYPE_MYVIDEOS :
                return $this->youtube->getUserUploads('default', $query->getQueryUrl(2));
                break;
            case Manager :: FEED_STANDARD_TYPE :
                $identifier = Request :: get(Manager :: PARAM_FEED_IDENTIFIER);
                if (! $identifier || ! in_array($identifier, $this->get_standard_feeds()))
                {
                    $identifier = 'most_viewed';
                }
                $new_query = $this->youtube->newVideoQuery(
                    'http://gdata.youtube.com/feeds/api/standardfeeds/' . $identifier);
                $new_query->setOrderBy($query->getOrderBy());
                $new_query->setVideoQuery($query->getVideoQuery());
                $new_query->setStartIndex($query->getStartIndex());
                $new_query->setMaxResults($query->getMaxResults());
                return @ $this->youtube->getVideoFeed($new_query->getQueryUrl(2));
            default :
                // return $this->youtube->getUserUploads('default',
                // $query->getQueryUrl(2));
                return @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
        }
    }

    public function get_standard_feeds()
    {
        $standard_feeds = array();
        $standard_feeds[] = 'most_viewed';
        $standard_feeds[] = 'top_rated';
        $standard_feeds[] = 'recently_featured';
        $standard_feeds[] = 'watch_on_mobile';
        $standard_feeds[] = 'most_discussed';
        $standard_feeds[] = 'top_favorite';
        $standard_feeds[] = 'most_responded';
        $standard_feeds[] = 'most_recent';
        return $standard_feeds;
    }

    public function retrieve_external_repository_objects($query, $order_property, $offset, $count)
    {
        if (($count + $offset) >= 900)
        {
            $temp = ($offset + $count) - 900;
            $max_result = $count - $temp;
        }
        else
        {
            $max_result = $count;
        }

        $searchResponse = $this->youtube->search->listSearch(
            'id,snippet',
            array('type' => 'video', 'q' => $query, 'maxResults' => $max_result));

        foreach ($searchResponse['modelData']['items'] as $response)
        {
            $videosResponse = $this->youtube->videos->listVideos(
                'snippet, status, contentDetails, statistics',
                array('id' => $response['id']['videoId']));

            $object = new ExternalObject();
            $object->set_id($response['id']['videoId']);
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($response['snippet']['title']);
            $object->set_description(nl2br($response['snippet']['description']));
            $object->set_created(strtotime($response['snippet']['publishedAt']));
            $object->set_modified(strtotime($response['snippet']['publishedAt']));
            $object->set_owner_id($videosResponse['modelData']['items'][0]['snippet']['channelId']);
            $object->set_owner_name($videosResponse['modelData']['items'][0]['snippet']['channelTitle']);

            $iso_duration = $videosResponse['modelData']['items'][0]['contentDetails']['duration'];
            $parts_duration = array();
            preg_match_all('/(\d+)/', $iso_duration, $parts_duration);

            $object->set_duration($parts_duration[0][0] * 60 + $parts_duration[0][1]);

            if (count($response['snippet']['thumbnails']) > 0)
            {
                $thumbnail = $response['snippet']['thumbnails']['default']['url'];
                $object->set_url($response['snippet']['thumbnails']['default']['url']);
            }
            else
            {
                $thumbnail = null;
            }

            $object->set_thumbnail($thumbnail);

            $videoCategory = $this->youtube->videoCategories->listVideoCategories(
                'id,snippet',
                array('id' => $videosResponse['modelData']['items'][0]['snippet']['categoryId']));
            $category = $videoCategory['modelData']['items'][0]['snippet']['title'];

            $object->set_category($category);

            $object->set_tags($videosResponse['modelData']['items'][0]['snippet']['tags']);

            $object->set_status($videosResponse['modelData']['items'][0]['status']['uploadStatus']);

            $objects[] = $object;
        }

        // $object->set_rights($this->determine_rights($videoEntry));
        return new ArrayResultSet($objects);
    }

    public function count_external_repository_objects($query)
    {
        $searchResponse = $this->youtube->search->listSearch(
            'id,snippet',
            array('type' => 'video', 'q' => $query, 'maxResults' => 50));

        if ($searchResponse['pageInfo']['totalResults'] >= 900)
        {
            return 900;
        }
        else
        {
            return $searchResponse['pageInfo']['totalResults'];
        }
    }

    // public function get_youtube_video_entry($id)
    // {
    // $parameter = Request :: get(Manager :: PARAM_FEED_TYPE);
    // if ($parameter == Manager :: FEED_TYPE_MYVIDEOS)
    // {
    // return $this->youtube->getFullVideoEntry($id);
    // }
    // else
    // {
    // return $this->youtube->getVideoEntry($id);
    // }
    // }
    public function retrieve_external_repository_object($id)
    {
        $videosResponse = $this->youtube->videos->listVideos('snippet, status, contentDetails', array('id' => $id));

        $object = new ExternalObject();
        $object->set_id($videosResponse['modelData']['items'][0]['id']);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($videosResponse['modelData']['items'][0]['snippet']['title']);
        $object->set_description(nl2br($videosResponse['modelData']['items'][0]['snippet']['description']));

        $object->set_owner_id($videosResponse['modelData']['items'][0]['snippet']['channelId']);
        $object->set_owner_name($videosResponse['modelData']['items'][0]['snippet']['channelTitle']);
        $object->set_created(strtotime($videosResponse['modelData']['items'][0]['snippet']['publishedAt']));
        $object->set_modified(strtotime($videosResponse['modelData']['items'][0]['snippet']['publishedAt']));

        $iso_duration = $videosResponse['modelData']['items'][0]['contentDetails']['duration'];
        $parts_duration = array();
        preg_match_all('/(\d+)/', $iso_duration, $parts_duration);

        $object->set_duration($parts_duration[0][0] * 60 + $parts_duration[0][1]);

        $thumbnails = $videosResponse['modelData']['items'][0]['snippet']['thumbnails'];

        if (count($thumbnails) > 0)
        {
            $thumbnail = $thumbnails['default']['url'];
            $object->set_url($thumbnails['default']['url']);
        }
        else
        {
            $thumbnail = null;
        }
        $object->set_thumbnail($thumbnail);

        $videoCategory = $this->youtube->videoCategories->listVideoCategories(
            'id,snippet',
            array('id' => $videosResponse['modelData']['items'][0]['snippet']['categoryId']));
        $category = $videoCategory['modelData']['items'][0]['snippet']['title'];

        $object->set_category($category);

        $object->set_tags($videosResponse['modelData']['items'][0]['snippet']['tags']);

        $object->set_status($videosResponse['modelData']['items'][0]['status']['uploadStatus']);

        $object->set_rights($this->determine_rights());

        return $object;
    }

    public function update_youtube_video($values)
    {
        $video = $this->youtube->videos->listVideos('snippet', array('id' => $values[ExternalObject :: PROPERTY_ID]));

        $video->setVideoTitle($values[ExternalObject :: PROPERTY_TITLE]);
        $video->setVideoCategory($values[ExternalObject :: PROPERTY_CATEGORY]);
        $video->setVideoTags($values[ExternalObject :: PROPERTY_TAGS]);
        $video->setVideoDescription($values[ExternalObject :: PROPERTY_DESCRIPTION]);

        $this->youtube->videos->update('snippet', $video);
        return true;
    }

    public function delete_external_repository_object($id)
    {
        return $this->youtube->videos->delete($id);
    }

    public function export_external_repository_object($object)
    {
        // $file_source = $this->youtube->newMediaFileSource($object->get_full_path());
        // $file_source->setContentType($object->get_mime_type());
        // $file_source->setSlug($object->get_filename());
        // $video_entry->setMediaSource($file_source);
        // $video_entry->setVideoTitle($object->get_title());
        // $video_entry->setVideoDescription(strip_tags($object->get_description()));
        // $video_entry->setVideoCategory('Education');

        // $upload_url = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';
        // try
        // {
        // $new_entry = $this->youtube->insertEntry($video_entry, $upload_url, 'Zend_Gdata_YouTube_VideoEntry');
        // }
        // catch (Zend_Gdata_App_HttpException $httpException)
        // {
        // echo ($httpException->getRawResponseBody());
        // }
        // catch (Zend_Gdata_App_Exception $e)
        // {
        // echo $e->getMessage();
        // }
        return true;
    }

    public function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = true;
        // $rights[ExternalObject :: RIGHT_EDIT] = ($video_entry->getEditLink() !== null ? true : false);
        // $rights[ExternalObject :: RIGHT_DELETE] = ($video_entry->getEditLink() !== null ? true : false);
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }
}
