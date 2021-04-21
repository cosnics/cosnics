<?php

namespace Chamilo\Core\User\Service\UserPropertiesExtension;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface UserPropertiesExtensionInterface
 * @package Chamilo\Core\User\Service\UserPropertiesExtension
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface UserPropertiesExtensionInterface
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getExtendedUserProperties(User $user);
}
