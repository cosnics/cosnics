<?php
namespace Chamilo\Core\User\Picture;

use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Core\User\Picture
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserPictureUpdateProviderInterface
{

    public function deleteUserPicture(User $targetUser, User $requestUser): bool;

    public function setUserPicture(User $targetUser, User $requestUser, ?UploadedFile $fileInformation = null): bool;

    public function updateUserPictureFromParameters(
        User $targetUser, User $requestUser, ?UploadedFile $fileInformation = null, bool $removeExistingPicture = false
    ): bool;
}