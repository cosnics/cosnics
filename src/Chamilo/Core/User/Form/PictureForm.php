<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PictureForm extends UserForm
{
    private User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $user, string $action)
    {
        $this->user = $user;

        parent::__construct('user_picture', $action);
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $user = $this->getUser();

        $encodedUserPicture = $this->getUserPictureProvider()->getUserPictureAsBase64String(
            $user, $user
        );

        $this->buildPictureCategoryForm($encodedUserPicture, $user->get_fullname(), false);
        $this->addSaveResetButtons();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }
}
