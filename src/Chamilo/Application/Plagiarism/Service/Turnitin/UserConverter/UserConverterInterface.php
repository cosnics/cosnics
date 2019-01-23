<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Using this interface we can define a converter to translate the given user object to a user identifier.
 *
 * Interface UserConverterInterface
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 */
interface UserConverterInterface
{

    public function convertUserToId(User $user);
}