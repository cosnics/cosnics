<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'vimeo';
    const PROPERTY_URLS = 'urls';
    const PROPERTY_TAGS = 'tags';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_URLS, self::PROPERTY_TAGS));
    }

    public function get_urls()
    {
        return $this->get_default_property(self::PROPERTY_URLS);
    }

    public function set_urls($urls)
    {
        return $this->set_default_property(self::PROPERTY_URLS, $urls);
    }

    public function get_tags()
    {
        return $this->get_default_property(self::PROPERTY_TAGS);
    }

    public function set_tags($tags)
    {
        return $this->set_default_property(self::PROPERTY_TAGS, $tags);
    }

    public function get_tags_string()
    {
        return implode(', ', $this->get_tags());
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }
}
