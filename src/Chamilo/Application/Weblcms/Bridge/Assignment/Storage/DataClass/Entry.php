<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Entry extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
{
    const ENTITY_TYPE_COURSE_GROUP = 1;
    const ENTITY_TYPE_PLATFORM_GROUP = 2;
    const ENTITY_TYPE_USER = 0;

    const PROPERTY_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';

    /**
     *
     * @return int
     */
    public function getContentObjectPublicationId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID);
    }

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_assignment_entry';
    }

    /**
     *
     * @param int $contentObjectPublicationId
     */
    public function setContentObjectPublicationId($contentObjectPublicationId)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID, $contentObjectPublicationId);
    }
}