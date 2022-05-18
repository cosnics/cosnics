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
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_CONTENT_OBJECT_ID, self::PROPERTY_ATTACHMENT_ID, self::PROPERTY_TYPE));
    }

    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_attachment_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
    }

    public function set_attachment_id($attachment_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachment_id);
    }

    public function get_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function set_type($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    public function get_attachment_object()
    {
        if (! isset($this->attachment_object))
        {
            $this->attachment_object = DataManager::retrieve_by_id(
                ContentObject::class,
                $this->get_attachment_id());
        }
        return $this->attachment_object;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_content_object_attachment';
    }
}
