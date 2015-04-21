<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectRelTag extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_TAG_ID = 'tag_id';

    /**
     * Get the default properties of all content object attachments.
     * 
     * @param array $extended_property_names
     *
     * @return array
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_TAG_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @return int
     */
    public function get_tag_id()
    {
        return $this->get_default_property(self :: PROPERTY_TAG_ID);
    }

    /**
     *
     * @param int $tag_id
     */
    public function set_tag_id($tag_id)
    {
        $this->set_default_property(self :: PROPERTY_TAG_ID, $tag_id);
    }
}