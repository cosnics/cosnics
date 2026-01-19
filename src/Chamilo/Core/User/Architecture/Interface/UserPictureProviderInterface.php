<?php
namespace Chamilo\Core\User\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Architecture\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserPictureProviderInterface
{

    public function doesUserHavePicture(User $user): bool;

    public function downloadUserPicture(User $user): Response;

    public function getUserPictureAsBase64String(User $targetUser, User $requestUser): string;
}