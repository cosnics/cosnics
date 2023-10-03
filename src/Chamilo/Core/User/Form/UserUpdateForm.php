<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;

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
        $this->buildPictureCategoryForm($encodedUserPicture, $user->get_fullname());
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
    public function setDefaults($defaultValues = [], $filter = null)
    {
        $user = $this->getUser();

        $expirationDate = $user->get_expiration_date();

        $defaultValues[self::PROPERTY_TIME_PERIOD_FOREVER] = $expirationDate != 0 ? 0 : 1;
        $defaultValues[self::PROPERTY_GENERATE_PASSWORD] = 0;
        $defaultValues[self::PROPERTY_SEND_MAIL] = 0;

        if ($expirationDate != 0)
        {
            $defaultValues[User::PROPERTY_ACTIVATION_DATE] = $user->get_activation_date();
            $defaultValues[User::PROPERTY_EXPIRATION_DATE] = $user->get_expiration_date();
        }

        $defaultValues[User::PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
        $defaultValues[User::PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
        $defaultValues[User::PROPERTY_PLATFORMADMIN] = $user->getPlatformAdmin();
        $defaultValues[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaultValues[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaultValues[User::PROPERTY_EMAIL] = $user->get_email();
        $defaultValues[User::PROPERTY_USERNAME] = $user->get_username();
        $defaultValues[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaultValues[User::PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaultValues[User::PROPERTY_PHONE] = $user->get_phone();
        $defaultValues[User::PROPERTY_STATUS] = $user->get_status();
        $defaultValues[User::PROPERTY_ACTIVE] = $user->get_active() ? 1 : 0;

        parent::setDefaults($defaultValues);
    }
}
