<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationGroup extends DataClass
{
    // Properties
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_PUBLICATION, self::PROPERTY_GROUP_ID));
    }

    /**
     *
     * @return string[]
     */
    public static function get_cacheable_property_names($cacheablePropertyNames = [])
    {
        return [];
    }

    /**
     *
     * @return int
     */
    public function get_publication()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION);
    }

    /**
     *
     * @param int
     */
    public function set_publication($publication)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION, $publication);
    }

    /**
     *
     * @return int
     */
    public function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    /**
     *
     * @param int $group_id
     */
    public function set_group_id($group_id)
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'calendar_personal_publication_group';
    }
}
