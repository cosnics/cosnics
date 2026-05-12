<?php
namespace Chamilo\Core\User\Architecture\Interface;

use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Core\User\Architecture\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserPictureUpdateProviderInterface
{
    public function deleteUserPicture(User $user, ?User $executingUser = null): void;

    public function setUserPicture(User $user, ?UploadedFile $fileInformation = null, ?User $executingUser = null
    ): void;

    public function updateUserPictureFromParameters(
        User $user, ?UploadedFile $fileInformation = null, bool $removeExistingPicture = false,
        ?User $executingUser = null
    ): void;
}