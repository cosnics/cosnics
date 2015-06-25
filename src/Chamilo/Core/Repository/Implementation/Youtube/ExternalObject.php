<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;
use Chamilo\Libraries\Platform\Translation;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'youtube';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_TAGS = 'tags';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_RESTRICTED = 'restricted';

    public function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    public function set_category($category)
    {
        return $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    public function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
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
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CATEGORY, self :: PROPERTY_TAGS));
    }

    public function get_status_text()
    {
        $status = $this->get_status();
        switch ($status)
        {
            case self :: STATUS_REJECTED :
                return Translation :: get('Rejected');
                break;
            case self :: STATUS_PROCESSING :
                return Translation :: get('Processing');
                break;
            case self :: STATUS_FAILED :
                return Translation :: get('Failed');
                break;
            case self :: STATUS_AVAILABLE :
                return Translation :: get('Available');
                break;
            case self :: STATUS_RESTRICTED :
                return Translation :: get('Restricted');
                break;
            default :
                return Translation :: get('Unknown');
        }
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
