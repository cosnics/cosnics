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
use Chamilo\Core\Repository\Implementation\Youtube\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Youtube\Storage\DataClass\PlayList;
use Chamilo\Core\Repository\Implementation\Youtube\Storage\DataClass\ExternalObject;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $youtube;

    private $session_token;

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

            $user_setting = new Setting();
            $user_setting->set_user_id(Session :: get_user_id());
            $user_setting->set_variable('session_token');
            $user_setting->set_value($token);
            $user_setting->set_external_id($this->get_external_repository_instance_id());


            return $user_setting->create();
        }
        else
        {
            $url = $this->client->createAuthUrl('https://www.googleapis.com/auth/youtube');
            header('Location: ' . $url);
            exit();
        }
    }

    public function create_playlist(PlayList $playlist/*, $video*/)
    {
        $playlistSnippet = new \Google_Service_YouTube_PlaylistSnippet();
        $playlistSnippet->setTitle($playlist->get_title() . $playlist->get_date());
        $playlistSnippet->setDescription($playlist->get_description());

        $playlistStatus = new \Google_Service_YouTube_PlaylistStatus();
        $playlistStatus->setPrivacyStatus('private');

        $youTubePlaylist = new \Google_Service_YouTube_Playlist();
        $youTubePlaylist->setSnippet($playlistSnippet);
        $youTubePlaylist->setStatus($playlistStatus);

        $playlistResponse = $this->youtube->playlists->insert('snippet,status', $youTubePlaylist);
        $playlistId = $playlistResponse['id'];

        $resourceId = new \Google_Service_YouTube_ResourceId();
        $resourceId->setVideoId('SZj6rAYkYOg');
        $resourceId->setKind('youtube#video');

        $playlistItemSnippet = new \Google_Service_YouTube_PlaylistItemSnippet();
        $playlistItemSnippet->setTitle('First video in the test playlist');
        $playlistItemSnippet->setPlaylistId($playlistId);
        $playlistItemSnippet->setResourceId($resourceId);

        $playlistItem = new \Google_Service_YouTube_PlaylistItem();
        $playlistItem->setSnippet($playlistItemSnippet);
        $playlistItemResponse = $this->youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
        return $playlistItemResponse;
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
        $categories = $this->youtube->videoCategories->listVideoCategories('snippet', array('regionCode' => 'BE'));
        $list_categories = array();
        foreach ($categories['modelData']['items'] as $category)
        {
            $list_categories[(int) $category['id']] = $category['snippet']['title'];
        }

        asort($list_categories);

        return $list_categories;
    }

    public function upload_video($values, $_video_file)
    {
        $snippet = new \Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($values[ExternalObjectForm :: VIDEO_TITLE]);
        $snippet->setDescription($values[ExternalObjectForm :: VIDEO_DESCRIPTION]);
        $snippet->setTags($values[ExternalObjectForm :: VIDEO_TAGS]);
        $snippet->setCategoryId($values[ExternalObjectForm :: VIDEO_CATEGORY]);

        $status = new \Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = "private";

        $video = new \Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $chunkSizeBytes = 1 * 1024 * 1024;
        $this->client->setDefer(true);

        $insertRequest = $this->youtube->videos->insert('snippet, status, contentDetails', $video);
        $media = new \Google_Http_MediaFileUpload($this->client, $insertRequest, 'video/*', null, true, $chunkSizeBytes);
        $media->setFileSize(filesize($_video_file['tmp_name']));
        $status = false;
        $handle = fopen($_video_file['tmp_name'], "rb");
        while (! $status && ! feof($handle))
        {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);
        $this->client->setDefer(false);

        $playlist = new PlayList();
        $playlist->set_title('test');
        $playlist->set_description('test descr');
        $playlist->set_date(date("Y-m-d H:i:s"));

        $this->create_playlist($playlist);

        return $media;
    }

    public function get_video_feeds()
    {
        if ($this->client->getAccessToken())
        {
            $channelsResponse = $this->youtube->channels->listChannels('contentDetails', array('mine' => 'true'));
            foreach ($channelsResponse['items'] as $channel)
            {
                $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

                $playlistItemsResponse = $this->youtube->playlistItems->listPlaylistItems(
                    'snippet',
                    array('playlistId' => $uploadsListId, 'maxResults' => 50));
                foreach ($playlistItemsResponse['items'] as $playlistItem)
                {
                    $list[] = $playlistItem['snippet']['title'];
                }
            }
            return $list;
        }
        else
        {
            return array();
        }
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

        $pageNumber = ($offset / $count) + 1;
        $pageToken = PageTokenGenerator :: getInstance()->getToken($count, $pageNumber);

        $searchResponse = $this->youtube->search->listSearch(
            'id,snippet',
            array('type' => 'video', 'q' => $query, 'maxResults' => $max_result, 'pageToken' => $pageToken));

        foreach ($searchResponse['modelData']['items'] as $response)
        {
            $videosResponse = $this->youtube->videos->listVideos(
                'snippet, status, contentDetails',
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
        $video = $this->youtube->videos->listVideos(
            'snippet,status, contentDetails',
            array('id' => $values[ExternalObject :: PROPERTY_ID]));

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
        $videoPath = $object->get_full_path();
        $snippet = new \Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($object->get_title());
        $snippet->setDescription(strip_tags($object->get_description()));
        $snippet->setTags(array("tag1", "tag2"));

        $snippet->setCategoryId('Education');
        $status = new \Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = "public";

        $video = new \Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);
        $chunkSizeBytes = 1 * 1024 * 1024;
        $this->client->setDefer(true);
        $insertRequest = $this->youtube->videos->insert("status,snippet", $video);

        $media = new \Google_Http_MediaFileUpload($this->client, $insertRequest, 'video/*', null, true, $chunkSizeBytes);
        $media->setFileSize(filesize($videoPath));
        $status = false;
        $handle = fopen($videoPath, "rb");
        while (! $status && ! feof($handle))
        {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);

        $this->client->setDefer(false);
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
