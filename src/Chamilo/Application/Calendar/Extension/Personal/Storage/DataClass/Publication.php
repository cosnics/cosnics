<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class Publication extends \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
{

    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_PUBLISHER = 'publisher_id';

    /**
     * Get the default properties of all Publications.
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_PUBLISHER, self::PROPERTY_PUBLISHED));
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_publication_object()
    {
        return parent::getContentObject();
    }

    /**
     * @return integer
     */
    public function get_published()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHED);
    }

    /**
     * @return integer
     */
    public function get_publisher()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'calendar_personal_publication';
    }

    /**
     * @param integer $published
     */
    public function set_published($published)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHED, $published);
    }

    /**
     * @param integer $publisher
     */
    public function set_publisher($publisher)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER, $publisher);
    }
}
