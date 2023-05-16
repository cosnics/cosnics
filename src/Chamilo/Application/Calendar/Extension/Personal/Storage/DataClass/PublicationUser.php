<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationUser extends DataClass
{
    public const CONTEXT = Manager::class;

    public const PROPERTY_PUBLICATION = 'publication_id';
    public const PROPERTY_USER = 'user_id';

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
        return parent::getDefaultPropertyNames([self::PROPERTY_PUBLICATION, self::PROPERTY_USER]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'calendar_personal_publication_user';
    }

    /**
     * @return int
     */
    public function get_publication()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION);
    }

    /**
     * @return int
     */
    public function get_user()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER);
    }

    /**
     * @param int $publication
     */
    public function set_publication($publication)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION, $publication);
    }

    /**
     * @param int $user
     */
    public function set_user($user)
    {
        $this->setDefaultProperty(self::PROPERTY_USER, $user);
    }
}
