<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'vimeo';
    const PROPERTY_URLS = 'urls';
    const PROPERTY_TAGS = 'tags';

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_URLS, self :: PROPERTY_TAGS));
    }

    public function get_urls()
    {
        return $this->get_default_property(self :: PROPERTY_URLS);
    }

    public function set_urls($urls)
    {
        return $this->set_default_property(self :: PROPERTY_URLS, $urls);
    }

    public function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }

    public function set_tags($tags)
    {
        return $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    public function get_tags_string($include_links = true)
    {
        $tags = array();
        foreach ($this->get_tags() as $tag)
        {
            if ($include_links)
            {
                $tags[] = '<a href="http://www.vimeo.com/tag:' . $tag->normalized . '">' . $tag->_content . '</a>';
            }
            else
            {
                $tags[] = $tag->_content;
            }
        }
        
        return implode(', ', $tags);
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function is_usable()
    {
        return $this->get_right(self :: RIGHT_USE);
    }
}
