<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class UserForm extends FormValidator
{
    public const PROPERTY_CONFIRM_PASSWORD = 'confirm_password';
    public const PROPERTY_CURRENT_PASSWORD = 'current_password';
    public const PROPERTY_GENERATE_PASSWORD = 'generate_password';
    public const PROPERTY_SEND_MAIL = 'send_mail';

    /**
     * @throws \QuickformException
     */
    public function __construct(string $formName, string $action)
    {
        parent::__construct($formName, self::FORM_METHOD_POST, $action);

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildAccountCategoryForm(bool $isLockoutRisk = false, bool $includeCategoryTitle = true): void
    {
        $translator = $this->getTranslator();

        if ($includeCategoryTitle)
        {
            $this->addElement('category', $translator->trans('AccountProperties', [], Manager::CONTEXT));
        }

        if ($isLockoutRisk)
        {
            $this->add_warning_message(
                'admin_lockout_message', null, $translator->trans('LockOutWarningMessage', [], Manager::CONTEXT)
            );
        }

        $this->addElement('select', User::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT), [
            User::STATUS_STUDENT => $translator->trans('Student', [], Manager::CONTEXT),
            User::STATUS_TEACHER => $translator->trans('CourseAdmin', [], Manager::CONTEXT)
        ]);

        $this->addElement('toggle', User::PROPERTY_ACTIVE, $translator->trans('Active', [], Manager::CONTEXT));

        $this->addTimePeriodSelection('ExpirationDate', User::PROPERTY_ACTIVATION_DATE, User::PROPERTY_EXPIRATION_DATE);

        $this->addElement(
            'toggle', User::PROPERTY_PLATFORMADMIN, $translator->trans('PlatformAdministrator', [], Manager::CONTEXT)
        );

        // Disk Quota
        $this->add_textfield(
            User::PROPERTY_DISK_QUOTA, $translator->trans('DiskQuota', [], Manager::CONTEXT), false
        );

        $this->addRule(
            User::PROPERTY_DISK_QUOTA, $translator->trans('ThisFieldMustBeNumeric', [], StringUtilities::LIBRARIES),
            'numeric'
        );
    }

    abstract public function buildForm(): void;

    /**
     * @throws \QuickformException
     */
    public function buildOtherCategoryForm(bool $includeCategoryTitle = true): void
    {
        $translator = $this->getTranslator();

        if ($includeCategoryTitle)
        {
            $this->addElement('category', $translator->trans('Other', [], Manager::CONTEXT));
        }

        $this->addElement(
            'toggle', self::PROPERTY_SEND_MAIL, $translator->trans('SendMailToUser', [], Manager::CONTEXT)
        );
    }

    /**
     * @throws \QuickformException
     */
    public function buildPasswordCategoryForm(
        bool $allowedToChangePassword = true, bool $allowedToGeneratePassword = true,
        bool $requiresCurrentPassword = false, bool $requiresPasswordConfirmation = false,
        bool $includeCategoryTitle = true
    ): void
    {
        if ($allowedToChangePassword)
        {
            $translator = $this->getTranslator();

            if ($includeCategoryTitle)
            {
                $this->addElement('category', $translator->trans('Password', [], Manager::CONTEXT));
            }

            if ($allowedToGeneratePassword)
            {
                $this->addElement(
                    'toggle', self::PROPERTY_GENERATE_PASSWORD,
                    $translator->trans('AutoGeneratePassword', [], Manager::CONTEXT)
                );
            }

            if ($requiresCurrentPassword)
            {
                $this->addElement(
                    'password', self::PROPERTY_CURRENT_PASSWORD,
                    $translator->trans('CurrentPassword', [], Manager::CONTEXT),
                    ['autocomplete' => 'off', 'class' => 'form-control']
                );

                $this->addFormRule([$this, 'checkCurrentPasswordEntered']);
            }

            $this->addElement(
                'password', User::PROPERTY_PASSWORD, $translator->trans('Password', [], Manager::CONTEXT),
                ['autocomplete' => 'off', 'class' => 'form-control']
            );

            if ($requiresPasswordConfirmation)
            {
                $this->addElement(
                    'password', self::PROPERTY_CONFIRM_PASSWORD,
                    $translator->trans('PasswordConfirmation', [], Manager::CONTEXT),
                    ['autocomplete' => 'off', 'class' => 'form-control']
                );

                $this->addRule(
                    [User::PROPERTY_PASSWORD, self::PROPERTY_CONFIRM_PASSWORD],
                    $translator->trans('PassTwo', [], Manager::CONTEXT), 'compare'
                );
            }

            $this->addElement(
                'html', $this->getResourceManager()->getResourceHtml(
                $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) . 'Jquery/jquery.jpassword.js'
            )
            );

            $this->addFormRule([$this, 'checkPasswordRequirements']);
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildPersonalDetailsCategoryForm(
        bool $includeCategoryTitle = true, bool $allowedToChangeFirstName = true, bool $allowedToChangeLastName = true,
        bool $allowedToChangeUsername = true, bool $requiresEmail = true, bool $allowedToChangeEmailAddress = true,
        bool $requiresOfficialCode = true, bool $allowedToChangeOfficialCode = true
    ): void
    {
        $translator = $this->getTranslator();

        if ($includeCategoryTitle)
        {
            $this->addElement('category', $translator->trans('PersonalDetails', [], Manager::CONTEXT));
        }

        // Firstname
        $this->add_textfield(
            User::PROPERTY_FIRSTNAME, $translator->trans('FirstName', [], Manager::CONTEXT), $allowedToChangeFirstName
        );

        if (!$allowedToChangeFirstName)
        {
            $this->freeze([User::PROPERTY_FIRSTNAME]);
        }
        else
        {
            $this->applyFilter(User::PROPERTY_FIRSTNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_FIRSTNAME, 'trim');
        }

        // Lastname
        $this->add_textfield(
            User::PROPERTY_LASTNAME, $translator->trans('LastName', [], Manager::CONTEXT), $allowedToChangeLastName
        );

        if (!$allowedToChangeLastName)
        {
            $this->freeze([User::PROPERTY_LASTNAME]);
        }
        else
        {
            $this->applyFilter(User::PROPERTY_FIRSTNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_FIRSTNAME, 'trim');
        }

        // Email
        $this->add_textfield(
            User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT),
            $allowedToChangeEmailAddress && $requiresEmail
        );

        if (!$allowedToChangeEmailAddress)
        {
            $this->freeze(User::PROPERTY_EMAIL);
        }
        else
        {
            $this->addRule(User::PROPERTY_EMAIL, $translator->trans('EmailWrong', [], Manager::CONTEXT), 'email');
            $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
            $this->applyFilter(User::PROPERTY_EMAIL, 'trim');
        }

        $this->addRule(User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT), 'email');

        // Username
        $this->add_textfield(
            User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), $allowedToChangeUsername
        );

        if (!$allowedToChangeUsername)
        {
            $this->freeze(User::PROPERTY_USERNAME);
        }
        else
        {
            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
            $this->addRule(
                User::PROPERTY_USERNAME, $translator->trans('UsernameWrong', [], Manager::CONTEXT), 'username'
            );
        }

        // Official Code
        $this->add_textfield(
            User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            $allowedToChangeOfficialCode && $requiresOfficialCode
        );

        if (!$allowedToChangeOfficialCode)
        {
            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
        }
        else
        {
            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');
        }

        // Phone Number
        $this->add_textfield(
            User::PROPERTY_PHONE, $translator->trans('PhoneNumber', [], Manager::CONTEXT), false
        );
    }

    /**
     * @throws \QuickformException
     */
    public function buildPictureCategoryForm(
        ?string $encodedUserPicture = null, ?string $userFullname = null, bool $includeCategoryTitle = true
    ): void
    {
        $translator = $this->getTranslator();

        if ($includeCategoryTitle)
        {
            $this->addElement('category', $translator->trans('PictureTitle', [], Manager::CONTEXT));
        }

        if (!is_null($encodedUserPicture))
        {
            $this->addElement(
                'static', 'current_image', $translator->trans('CurrentImage', [], Manager::CONTEXT),
                '<img class="my-account-photo" src="' . $encodedUserPicture . '" alt="' . $userFullname . '" />'
            );
        }

        $this->addElement('file', User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture'));
        $this->addRule(
            User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT), 'filetype',
            ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF']
        );
        $this->addElement(
            'static', 'allowed_profile_image_formats', null,
            $translator->trans('AllowedProfileImageFormats', [], Manager::CONTEXT)
        );
    }

    /**
     * @throws \QuickformException
     */
    public function checkCurrentPasswordEntered($exportValues): true|array
    {
        $newPassword = $exportValues[User::PROPERTY_PASSWORD];

        if (empty($newPassword))
        {
            return true;
        }

        if (empty($this->exportValue(self::PROPERTY_CURRENT_PASSWORD)))
        {
            return [
                User::PROPERTY_PASSWORD => $this->getTranslator()->trans('EnterCurrentPassword', [], Manager::CONTEXT)
            ];
        }

        return true;
    }

    public function checkPasswordRequirements($exportValues): true|array
    {
        $customPassword =
            empty($exportValues[User::PROPERTY_PASSWORD]) && !$exportValues[self::PROPERTY_GENERATE_PASSWORD];

        if (!$customPassword)
        {
            return true;
        }

        $newPassword = $exportValues[User::PROPERTY_PASSWORD];

        if (strlen($newPassword) < 6)
        {
            return ['pw' => $this->getTranslator()->trans('PasswordRequirements', [], Manager::CONTEXT)];
        }

        return true;
    }
}
