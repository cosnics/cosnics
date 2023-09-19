<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Dieter De Neef
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupRelUser extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_GROUP_ID = 'group_id';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties of all groups.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_GROUP_ID, self::PROPERTY_USER_ID]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'group_group_rel_user';
    }

    public function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_group_id($group_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $group_id);
    }

    public function set_user_id($user_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
