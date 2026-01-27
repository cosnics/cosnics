<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
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

        $this->buildPictureCategoryForm($encodedUserPicture, $user->getFullName(), false);
        $this->addSaveResetButtons();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param class-string<\Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface> $className
     */
    public function getUserPictureProvider(string $className = 'Chamilo\Core\User\Picture\UserPictureProvider'
    ): UserPictureProviderInterface
    {
        return $this->getService($className);
    }
}
