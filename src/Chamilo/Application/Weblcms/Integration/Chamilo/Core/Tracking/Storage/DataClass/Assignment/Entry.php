<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Entry extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
{
    const PROPERTY_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';

    // Entity types
    const ENTITY_TYPE_USER = 0;
    const ENTITY_TYPE_COURSE_GROUP = 1;
    const ENTITY_TYPE_PLATFORM_GROUP = 2;

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function getContentObjectPublicationId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID);
    }

    /**
     *
     * @param int $contentObjectPublicationId
     */
    public function setContentObjectPublicationId($contentObjectPublicationId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID, $contentObjectPublicationId);
    }

    public static function get_table_name()
    {
        return 'tracking_weblcms_assignment_entry';
    }
}