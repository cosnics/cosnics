<?php

namespace Chamilo\Core\User\Service\UserPropertiesExtension;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class UserPropertyExtensionManager
 * @package Chamilo\Core\User\Service\UserPropertiesExtension
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class UserPropertiesExtensionManager
{
    /**
     * @var UserPropertiesExtensionInterface[]
     */
    protected $userPropertiesExtensions;

    /**
     * UserPropertyExtensionManager constructor.
     */
    public function __construct()
    {
        $this->userPropertiesExtensions = [];
    }

    /**
     * @param UserPropertiesExtensionInterface $userPropertiesExtension
     */
    public function addUserPropertiesExtension(UserPropertiesExtensionInterface $userPropertiesExtension)
    {
        $this->userPropertiesExtensions[] = $userPropertiesExtension;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getExtendedUserProperties(User $user)
    {
        $extendedUserProperties = [];

        foreach ($this->userPropertiesExtensions as $userPropertiesExtension)
        {
            $extendedUserProperties =
                array_merge($extendedUserProperties, $userPropertiesExtension->getExtendedUserProperties($user));
        }

        return $extendedUserProperties;
    }

}
