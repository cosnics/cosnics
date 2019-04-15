<?php

namespace Chamilo\Application\Plagiarism\Service\UserConverter;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DefaultUserConverter implements UserConverterInterface
{
    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function convertUserToId(User $user)
    {
        return $user->getId();
    }
}