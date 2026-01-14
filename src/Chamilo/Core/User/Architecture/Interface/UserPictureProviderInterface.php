<?php
namespace Chamilo\Core\User\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Architecture\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserPictureProviderInterface
{

    public function doesUserHavePicture(User $user): bool;

    public function downloadUserPicture(User $targetUser, User $requestUser): void;

    public function getUserPictureAsBase64String(User $targetUser, User $requestUser): string;
}