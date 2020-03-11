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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function doesUserHavePicture(User $user);

    /**
     * Downloads the user picture
     *
     * @param User $targetUser
     * @param User $requestUser
     */
    public function downloadUserPicture(User $targetUser, User $requestUser);

    /**
     * Downloads the user picture
     *
     * @param User $targetUser
     * @param User $requestUser
     *
     * @return string
     */
    public function getUserPictureAsBase64String(User $targetUser, User $requestUser);
}