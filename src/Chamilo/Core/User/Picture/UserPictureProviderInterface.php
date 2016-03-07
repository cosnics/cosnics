<?php

namespace Chamilo\Core\User\Picture;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Describes the necessary functions needed for a user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserPictureProviderInterface
{
    /**
     * Downloads the user picture
     *
     * @param User $user
     *
     * @return mixed
     */
    public function downloadUserPicture(User $user);
}