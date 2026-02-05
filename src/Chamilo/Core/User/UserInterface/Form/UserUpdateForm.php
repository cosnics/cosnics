<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Form
 */
class UserUpdateForm extends UserForm
{

    protected bool $isLockoutRisk;

    protected User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $user, bool $isLockoutRisk, string $action)
    {
        $this->user = $user;
        $this->isLockoutRisk = $isLockoutRisk;

        parent::__construct('user_update', $action);
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

        $this->buildPersonalDetailsCategoryForm();
        $this->buildPasswordCategoryForm();
        $this->buildPictureCategoryForm($encodedUserPicture, $user->getFullName());
        $this->buildAccountCategoryForm($this->isLockoutRisk());
        $this->buildOtherCategoryForm();
        $this->addSaveResetButtons();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService(UserPictureProviderInterface::class);
    }

    public function isLockoutRisk(): bool
    {
        return $this->isLockoutRisk;
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults($defaultValues = [], $filter = null): void
    {
        $user = $this->getUser();

        $defaultValues[self::PROPERTY_GENERATE_PASSWORD] = 0;
        $defaultValues[self::PROPERTY_SEND_MAIL] = 0;

        $defaultValues[User::PROPERTY_PLATFORM_ADMINISTRATOR] = $user->getPlatformAdmin();
        $defaultValues[User::PROPERTY_SURNAME] = $user->getSurname();
        $defaultValues[User::PROPERTY_GIVEN_NAME] = $user->getGivenName();
        $defaultValues[User::PROPERTY_EMAIL] = $user->getEmail();
        $defaultValues[User::PROPERTY_USERNAME] = $user->getUsername();
        $defaultValues[User::PROPERTY_OFFICIAL_CODE] = $user->getOfficialCode();
        $defaultValues[User::PROPERTY_PICTURE_URI] = $user->getPictureUri();
        $defaultValues[User::PROPERTY_ACTIVE] = $user->getActive() ? 1 : 0;

        parent::setDefaults($defaultValues);
    }
}
