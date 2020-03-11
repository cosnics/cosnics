<?php
namespace Chamilo\Core\User\Picture;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface UserPictureUpdateProviderInterface
 * @package Chamilo\Core\User\Picture
 */
interface UserPictureUpdateProviderInterface
{

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     */
    public function deleteUserPicture(User $targetUser, User $requestUser);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     * @param string[] $fileInformation
     */
    public function setUserPicture(User $targetUser, User $requestUser, array $fileInformation);
}