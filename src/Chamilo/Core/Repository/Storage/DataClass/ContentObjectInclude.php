<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ContentObjectInclude extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_INCLUDE_ID = 'include_id';

    private $include_object;

    /**
     * Get the default properties of all content object attachments.
     *
     * @param array $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_CONTENT_OBJECT_ID, self::PROPERTY_INCLUDE_ID]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_content_object_include';
    }

    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_include_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_INCLUDE_ID);
    }

    public function get_include_object()
    {
        if (!isset($this->include_object))
        {
            $this->include_object = DataManager::retrieve_by_id(ContentObject::class, $this->get_include_id());
        }

        return $this->include_object;
    }

    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function set_include_id($include_id)
    {
        $this->setDefaultProperty(self::PROPERTY_INCLUDE_ID, $include_id);
    }
}
