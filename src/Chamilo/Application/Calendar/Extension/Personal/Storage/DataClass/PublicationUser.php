<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationUser extends DataClass
{
    // Properties
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_PUBLICATION, self::PROPERTY_USER));
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
        return $this->get_default_property(self::PROPERTY_PUBLICATION);
    }

    /**
     *
     * @param int $publication
     */
    public function set_publication($publication)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION, $publication);
    }

    /**
     *
     * @return int
     */
    public function get_user()
    {
        return $this->get_default_property(self::PROPERTY_USER);
    }

    /**
     *
     * @param int $user
     */
    public function set_user($user)
    {
        $this->set_default_property(self::PROPERTY_USER, $user);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'calendar_personal_publication_user';
    }
}
