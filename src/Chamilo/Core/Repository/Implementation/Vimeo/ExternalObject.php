<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'vimeo';
    const PROPERTY_URLS = 'urls';
    const PROPERTY_TAGS = 'tags';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_URLS, self::PROPERTY_TAGS));
    }

    public function get_urls()
    {
        return $this->getDefaultProperty(self::PROPERTY_URLS);
    }

    public function set_urls($urls)
    {
        return $this->setDefaultProperty(self::PROPERTY_URLS, $urls);
    }

    public function get_tags()
    {
        return $this->getDefaultProperty(self::PROPERTY_TAGS);
    }

    public function set_tags($tags)
    {
        return $this->setDefaultProperty(self::PROPERTY_TAGS, $tags);
    }

    public function get_tags_string()
    {
        return implode(', ', $this->get_tags());
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function is_usable()
    {
        return $this->get_right(self::RIGHT_USE);
    }
}
