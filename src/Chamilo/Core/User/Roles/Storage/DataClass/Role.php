<?php

namespace Chamilo\Core\User\Roles\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines a role
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Role extends DataClass
{
    const PROPERTY_ROLE = 'role';

    /**
     * Get the default properties of all data classes.
     *
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_ROLE;

        return $extended_property_names;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->get_default_property(self::PROPERTY_ROLE);
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->set_default_property(self::PROPERTY_ROLE, $role);

        return $this;
    }
}