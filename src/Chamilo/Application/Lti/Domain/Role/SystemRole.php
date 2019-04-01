<?php

namespace Chamilo\Application\Lti\Domain\Role;

/**
 * Class SystemRole
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SystemRole extends Role
{
    const ROLE_SYSTEM_ADMINISTRATOR = 'urn:lti:sysrole:ims/lis/SysAdmin';
    const ROLE_SYSTEM_SUPPORT = 'urn:lti:sysrole:ims/lis/SysSupport';
    const ROLE_CREATOR = 'urn:lti:sysrole:ims/lis/Creator';
    const ROLE_ACCOUNT_ADMIN = 'urn:lti:sysrole:ims/lis/AccountAdmin';
    const ROLE_USER = 'urn:lti:sysrole:ims/lis/User';
    const ROLE_ADMINISTRATOR = 'urn:lti:sysrole:ims/lis/Administrator';
    const ROLE_NONE = 'urn:lti:sysrole:ims/lis/None';

    /**
     * @return array
     */
    public function getAvailableRoles()
    {
        return [
            self::ROLE_SYSTEM_ADMINISTRATOR, self::ROLE_SYSTEM_SUPPORT, self::ROLE_CREATOR, self::ROLE_ACCOUNT_ADMIN,
            self::ROLE_USER, self::ROLE_ADMINISTRATOR, self::ROLE_NONE
        ];
    }
}


