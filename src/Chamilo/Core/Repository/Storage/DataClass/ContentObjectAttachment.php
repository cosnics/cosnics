<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectAttachment extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    const PROPERTY_TYPE = 'type';

    private $attachment_object;

    /**
     * Get the default properties of all content object attachments.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_CONTENT_OBJECT_ID, self::PROPERTY_ATTACHMENT_ID, self::PROPERTY_TYPE));
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_attachment_id()
    {
        return $this->get_default_property(self::PROPERTY_ATTACHMENT_ID);
    }

    public function set_attachment_id($attachment_id)
    {
        $this->set_default_property(self::PROPERTY_ATTACHMENT_ID, $attachment_id);
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function get_attachment_object()
    {
        if (! isset($this->attachment_object))
        {
            $this->attachment_object = DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $this->get_attachment_id());
        }
        return $this->attachment_object;
    }
}
