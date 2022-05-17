<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Entry extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry
{
    const PROPERTY_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';

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
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = [])
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    public static function getTableName(): string
    {
        return 'tracking_weblcms_learning_path_assignment_entry';
    }

    /**
     *
     * @param int $contentObjectPublicationId
     */
    public function setContentObjectPublicationId($contentObjectPublicationId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID, $contentObjectPublicationId);
    }
}