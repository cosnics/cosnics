<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationGroup extends DataClass
{
    public const CONTEXT = Manager::class;

    public const PROPERTY_GROUP_ID = 'group_id';
    public const PROPERTY_PUBLICATION = 'publication_id';

    /**
     * @return string[]
     */
    public static function getCacheablePropertyNames(array $cacheablePropertyNames = []): array
    {
        return [];
    }

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_PUBLICATION, self::PROPERTY_GROUP_ID]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'calendar_personal_publication_group';
    }

    /**
     * @return int
     */
    public function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    /**
     * @return int
     */
    public function get_publication()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION);
    }

    /**
     * @param int $group_id
     */
    public function set_group_id($group_id)
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * @param int
     */
    public function set_publication($publication)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION, $publication);
    }
}
