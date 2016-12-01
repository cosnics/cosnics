<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;
use Chamilo\Libraries\Platform\Translation;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'youtube';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_TAGS = 'tags';
    const STATUS_DELETED = 'deleted';
    const STATUS_FAILED = 'failed';
    const STATUS_PROCESSED = 'processed';
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_REJECTED = 'rejected';
    const YOUTUBE_PLAYER_URI = 'https://www.youtube.com/embed/%s';

    public function get_category()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY);
    }

    public function set_category($category)
    {
        return $this->set_default_property(self::PROPERTY_CATEGORY, $category);
    }

    public function get_tags()
    {
        return $this->get_default_property(self::PROPERTY_TAGS);
    }

    public function get_tags_string()
    {
        return implode(" ", $this->get_tags());
    }

    public function get_type()
    {
        return 'video';
    }

    public function set_tags($tags)
    {
        return $this->set_default_property(self::PROPERTY_TAGS, $tags);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_CATEGORY, self::PROPERTY_TAGS));
    }

    public function get_status_text()
    {
        $status = $this->get_status();
        switch ($status)
        {
            case self::STATUS_REJECTED :
                return Translation::get('Rejected');
                break;
            case self::STATUS_PROCESSED :
                return Translation::get('Processed');
                break;
            case self::STATUS_FAILED :
                return Translation::get('Failed');
                break;
            case self::STATUS_UPLOADED :
                return Translation::get('Uploaded');
                break;
            case self::STATUS_DELETED :
                return Translation::get('Deleted');
                break;
            default :
                return Translation::get('Unknown');
        }
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function get_video_url()
    {
        return sprintf(self::YOUTUBE_PLAYER_URI, $this->get_id());
    }
}
