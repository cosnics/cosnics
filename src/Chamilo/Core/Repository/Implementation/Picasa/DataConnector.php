<?php
namespace Chamilo\Core\Repository\Implementation\Picasa;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Zend_Gdata_AuthSub;
use Zend_Gdata_Media_Extension_MediaGroup;
use Zend_Gdata_Media_Extension_MediaKeywords;
use Zend_Gdata_Photos;
use Zend_Gdata_Photos_PhotoEntry;
use Zend_Gdata_Photos_PhotoQuery;
use Zend_Gdata_Photos_UserQuery;
use Zend_Loader;

class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var Zend_Gdata_Photos
     */
    private $picasa;
    const PHOTOS_MINE = 'mine';
    const PHOTOS_PUBLIC = 'public';

    /**
     * The id of the user on Picasa
     *
     * @var string
     */
    private $user_id;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $session_token = Setting :: get('session_token', $this->get_external_repository_instance_id());

        Zend_Loader :: loadClass('Zend_Gdata_Photos');
        Zend_Loader :: loadClass('Zend_Gdata_Photos_PhotoQuery');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');

        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                $redirect = new Redirect();
                $currentUrl = $redirect->getCurrentUrl();

                $scope = 'http://picasaweb.google.com/data';
                $secure = false;
                $session = true;
                $redirect_url = Zend_Gdata_AuthSub :: getAuthSubTokenUri($currentUrl, $scope, $secure, $session);

                header('Location: ' . $redirect_url);
            }
            else
            {
                $session_token = Zend_Gdata_AuthSub :: getAuthSubSessionToken($_GET['token']);

                if ($session_token)
                {
                    $setting = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_setting_from_variable_name(
                        'session_token',
                        $this->get_external_repository_instance_id());
                    $user_setting = new Setting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($session_token);
                    $user_setting->create();
                }
            }
        }

        $httpClient = Zend_Gdata_AuthSub :: getHttpClient($session_token);
        $application = PlatformSetting :: get('site_name');
        $this->picasa = new Zend_Gdata_Photos($httpClient, $application);
    }

    private function process_photo_entry(Zend_Gdata_Photos_PhotoEntry $photo_entry)
    {
        $object = new ExternalObject();

        $published = $photo_entry->getPublished()->getText();
        $published_timestamp = strtotime($published);

        $modified = $photo_entry->getUpdated()->getText();
        $modified_timestamp = strtotime($modified);

        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($photo_entry->getTitle()->getText());
        $object->set_description($photo_entry->getSummary()->getText());

        $object->set_created($published_timestamp);
        $object->set_modified($modified_timestamp);

        $original = array_shift($photo_entry->getMediaGroup()->getContent());
        $thumbnail = array_shift($photo_entry->getMediaGroup()->getThumbnail());

        $medium_url = str_replace('/s72/', '/s500/', $thumbnail->getUrl());
        $medium_info = getimagesize($medium_url);

        $photo_urls = array();
        $photo_urls[ExternalObject :: SIZE_THUMBNAIL] = array(
            'source' => $thumbnail->getUrl(),
            'width' => $thumbnail->getWidth(),
            'height' => $thumbnail->getHeight());
        $photo_urls[ExternalObject :: SIZE_MEDIUM] = array(
            'source' => $medium_url,
            'width' => $medium_info[0],
            'height' => $medium_info[1]);
        $photo_urls[ExternalObject :: SIZE_ORIGINAL] = array(
            'source' => $original->getUrl(),
            'width' => $original->getWidth(),
            'height' => $original->getHeight());
        $object->set_urls($photo_urls);

        $object->set_type(str_replace('/', '_', $original->getType()));

        // $license = $photo_entry->getGphotoLicense();
        // $object->set_license(
        // array('id' => $license->getId(), 'name' => $license->getName(), 'url' => $license->getUrl()));

        if ($photo_entry->getMediaGroup()->getKeywords() instanceof Zend_Gdata_Media_Extension_MediaKeywords)
        {
            $tags = array();
            $tags_elements = explode(',', $photo_entry->getMediaGroup()->getKeywords()->getText());

            foreach ($tags_elements as $tag)
            {
                $tags[] = $tag;
            }

            $object->set_tags($tags);
        }

        $object->set_album_id($photo_entry->getGphotoAlbumId());

        return $object;
    }

    /**
     *
     * @param $id string
     */
    public function retrieve_external_repository_object($id)
    {
        $identifiers = explode(':', $id);

        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");

        $user_query = new Zend_Gdata_Photos_UserQuery();
        $user_query->setUser($identifiers[0]);
        $user_query->setType("entry");

        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_user = $this->picasa->getUserEntry($user_query);

        $object = $this->process_photo_entry($photo_entry);
        $object->set_owner_id($photo_user->getGphotoUser()->getText());
        $object->set_owner($photo_user->getGphotoNickname()->getText());
        $object->set_id(
            $photo_user->getGphotoUser() . ':' . $photo_entry->getGphotoAlbumId()->getText() . ':' .
                 $photo_entry->getGphotoId()->getText());
        $object->set_rights($this->determine_rights($object->get_owner_id()));

        return $object;
    }

    /**
     *
     * @param $id string
     */
    public function delete_external_repository_object($id)
    {
        $identifiers = explode(':', $id);

        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");

        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_entry->delete();

        return true;
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function export_external_repository_object($content_object)
    {
    }

    /**
     *
     * @return array
     */
    public static function get_sort_properties()
    {
        return array();
    }

    /**
     *
     * @param $query mixed
     * @return mixed
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#count_external_repository_objects()
     */
    public function count_external_repository_objects($condition)
    {
        return $this->get_photos_feed($condition, null, 0, 1)->getTotalResults()->getText();
    }

    private function get_special_folder_names()
    {
        return array(self :: PHOTOS_MINE);
    }

    private function get_photos_feed($condition, $order_property = null, $offset = null, $count = null)
    {
        $folder = Request :: get(Manager :: PARAM_FOLDER);

        if (! $folder)
        {
            $folder = self :: PHOTOS_MINE;
        }

        if ($folder == self :: PHOTOS_MINE)
        {
            $query = $this->picasa->newUserQuery();
            $query->setUser('default');
            $query->setKind('photo');
        }
        elseif ($folder == self :: PHOTOS_PUBLIC)
        {
            $query = $this->picasa->newQuery("http://picasaweb.google.com/data/feed/api/all");
            $query->setParam("kind", "photo");

            if (! empty($condition))
            {
                $query->setQuery($condition);
            }
        }

        $query->setMaxResults($count);

        if ($offset)
        {
            $query->setStartIndex($offset + 1);
        }

        return $this->picasa->getUserFeed(null, $query);
    }

    /*
     * (non-PHPdoc) @see
     * common/extensions/external_repository_manager/ManagerConnector#retrieve_external_repository_objects()
     */
    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $user_feed = $this->get_photos_feed($condition, $order_property, $offset, $count);

        $objects = array();
        foreach ($user_feed as $photo_entry)
        {
            $author = array_shift($photo_entry->getAuthor());

            if ($author)
            {
                $author_id = $author->getEmail()->getText();
                $author_name = $author->getName()->getText();
            }
            else
            {
                $author_id = $this->get_authenticated_user();
                $author_name = $this->get_authenticated_user();
            }

            $object = $this->process_photo_entry($photo_entry);
            $object->set_owner_id($author_id);
            $object->set_owner($author_name);
            $object->set_id(
                $object->get_owner_id() . ':' . $photo_entry->getGphotoAlbumId()->getText() . ':' .
                     $photo_entry->getGphotoId()->getText());
            $object->set_rights($this->determine_rights($object->get_owner_id()));

            $objects[] = $object;
        }

        return new ArrayResultSet($objects);
    }

    /**
     *
     * @param $owner_id string
     * @return array
     */
    public function determine_rights($owner_id)
    {
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = true;

        if ($owner_id == $this->get_authenticated_user())
        {
            $rights[ExternalObject :: RIGHT_EDIT] = true;
            $rights[ExternalObject :: RIGHT_DELETE] = true;
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = true;
        }
        else
        {
            $rights[ExternalObject :: RIGHT_EDIT] = false;
            $rights[ExternalObject :: RIGHT_DELETE] = false;
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = false;
        }
        return $rights;
    }

    private function get_authenticated_user()
    {
        if (! isset($this->user_id))
        {
            $user_query = new Zend_Gdata_Photos_UserQuery();
            $user_query->setUser('default');
            $user_query->setType('entry');

            $this->user_id = $this->picasa->getUserEntry($user_query)->getGphotoUser();
        }

        return $this->user_id;
    }

    public function get_authenticated_user_albums()
    {
        $albums = array();
        $albums['default'] = Translation :: get('PicasaDropBoxAlbum');

        $user_feed = $this->picasa->getUserFeed('default');

        foreach ($user_feed as $album)
        {
            $albums[$album->getGphotoId()->getText()] = $album->getTitle()->getText() . ' (' .
                 $album->getGphotoNumPhotos() . ')';
        }

        return $albums;
    }

    /**
     *
     * @param $values array
     * @param $photo_path string
     * @return mixed
     */
    public function create_external_repository_object($values, $photo)
    {
        $media_source = $this->picasa->newMediaFileSource($photo['tmp_name']);
        $media_source->setContentType($photo["type"]);

        $entry = $this->picasa->newPhotoEntry();
        $entry->setMediaSource($media_source);
        $entry->setTitle($this->picasa->newTitle($photo['name']));
        $entry->setSummary($this->picasa->newSummary($values[ExternalObject :: PROPERTY_TITLE]));

        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText($values[ExternalObject :: PROPERTY_TAGS]);
        $entry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
        $entry->mediaGroup->keywords = $keywords;

        $album_query = $this->picasa->newAlbumQuery();
        $album_query->setUser($this->get_authenticated_user());
        $album_query->setAlbumId($values[ExternalObject :: PROPERTY_ALBUM_ID]);

        $entry = $this->picasa->insertPhotoEntry($entry, $album_query->getQueryUrl());
        return $this->get_authenticated_user() . ':' . $entry->getGphotoAlbumId()->getText() . ':' .
             $entry->getGphotoId()->getText();
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        $identifiers = explode(':', $values[ExternalObject :: PROPERTY_ID]);

        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");

        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_entry->summary->text = $values[ExternalObject :: PROPERTY_TITLE];

        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText($values[ExternalObject :: PROPERTY_TAGS]);
        $photo_entry->mediaGroup->keywords = $keywords;
        $updated_entry = $photo_entry->save();

        if ($updated_entry instanceof Zend_Gdata_Photos_PhotoEntry)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
