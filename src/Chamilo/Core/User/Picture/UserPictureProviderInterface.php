<?php
namespace Chamilo\Core\User\Picture;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Describes the necessary functions needed for a user picture provider
 *
 * @package Chamilo\Core\User\Picture
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserPictureProviderInterface
{

    public function doesUserHavePicture(User $user): bool;

    public function downloadUserPicture(User $targetUser, User $requestUser): void;

    public function getUserPictureAsBase64String(User $targetUser, User $requestUser): string;
}