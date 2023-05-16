<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectAttachment extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_TYPE = 'type';

    private $attachment_object;

    /**
     * Get the default properties of all content object attachments.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_CONTENT_OBJECT_ID, self::PROPERTY_ATTACHMENT_ID, self::PROPERTY_TYPE]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_content_object_attachment';
    }

    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function get_attachment_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
    }

    public function get_attachment_object()
    {
        if (!isset($this->attachment_object))
        {
            $this->attachment_object = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_attachment_id()
            );
        }

        return $this->attachment_object;
    }

    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @deprecated User ContentObjectAttachment::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    public function set_attachment_id($attachment_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachment_id);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * @deprecated Use ContentObjectAttachment::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }
}
