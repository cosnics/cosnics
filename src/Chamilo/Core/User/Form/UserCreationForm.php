<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\StringUtilities;

class UserCreationForm extends FormValidator
{
    public const PROPERTY_GENERATE_PASSWORD = 'generate_password';
    public const PROPERTY_SEND_MAIL = 'send_mail';

    protected User $actionUser;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $actionUser, string $action)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->actionUser = $actionUser;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $translator = $this->getTranslator();
        $configurationConsulter = $this->getConfigurationConsulter();

        $fieldRequiredMessage = $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES);

        $this->addElement('category', $translator->trans('PersonalDetails', [], Manager::CONTEXT));

        // Lastname
        $this->addElement(
            'text', User::PROPERTY_LASTNAME, $translator->trans('LastName', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(User::PROPERTY_LASTNAME, $fieldRequiredMessage, 'required');

        // Firstname
        $this->addElement(
            'text', User::PROPERTY_FIRSTNAME, $translator->trans('FirstName', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(User::PROPERTY_FIRSTNAME, $fieldRequiredMessage, 'required');

        // Email
        $this->addElement(
            'text', User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT), ['size' => '50']
        );

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']))
        {
            $this->addRule(User::PROPERTY_EMAIL, $fieldRequiredMessage, 'required');
        }
        $this->addRule(User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT), 'email');

        // Username
        $this->addElement(
            'text', User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(User::PROPERTY_USERNAME, $fieldRequiredMessage, 'required');

        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            ['size' => '50']
        );

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']) &&
            $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']) == 1)
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE, $fieldRequiredMessage, 'required'
            );
        }

        // Phone Number
        $this->addElement(
            'text', User::PROPERTY_PHONE, $translator->trans('PhoneNumber', [], Manager::CONTEXT), ['size' => '50']
        );

        $this->addElement(
            'toggle', self::PROPERTY_GENERATE_PASSWORD, $translator->trans('AutoGeneratePassword', [], Manager::CONTEXT)
        );
        $this->addElement(
            'password', User::PROPERTY_PASSWORD, $translator->trans('Password', [], Manager::CONTEXT),
            ['autocomplete' => 'off']
        );

        $this->addFormRule([$this, 'checkPasswordRequirements']);

        $this->addElement('category', $translator->trans('PictureTitle', [], Manager::CONTEXT));

        // Picture URI
        $this->addElement('file', User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture'));
        $this->addRule(
            User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT), 'filetype',
            ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF']
        );
        $this->addElement(
            'static', 'allowed_profile_image_formats', null,
            $translator->trans('AllowedProfileImageFormats', [], Manager::CONTEXT)
        );

        $this->addElement('category', $translator->trans('AccountProperties', [], Manager::CONTEXT));

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
        $this->addElement(
            'text', User::PROPERTY_DISK_QUOTA, $translator->trans('DiskQuota', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(
            User::PROPERTY_DISK_QUOTA, $translator->trans('ThisFieldMustBeNumeric', [], StringUtilities::LIBRARIES),
            'numeric'
        );

        $this->addElement('category', $translator->trans('Other', [], Manager::CONTEXT));

        $this->addElement(
            'toggle', self::PROPERTY_SEND_MAIL, $translator->trans('SendMailToNewUser', [], Manager::CONTEXT)
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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

    public function getActionUser(): User
    {
        return $this->actionUser;
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults($defaultValues = [], $filter = null)
    {
        $defaultValues[self::PROPERTY_TIME_PERIOD_FOREVER] = 1;

        $defaultValues[User::PROPERTY_EXPIRATION_DATE] = strtotime(
            '+ ' . intval($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'days_valid'])) . 'Days',
            time()
        );

        $defaultValues[User::PROPERTY_DATABASE_QUOTA] = '300';
        $defaultValues[User::PROPERTY_DISK_QUOTA] = '209715200';
        $defaultValues[User::PROPERTY_PLATFORMADMIN] = 0;
        $defaultValues[User::PROPERTY_ACTIVE] = 1;
        $defaultValues[User::PROPERTY_STATUS] = User::STATUS_STUDENT;
        $defaultValues[self::PROPERTY_SEND_MAIL] = 0;
        $defaultValues[self::PROPERTY_GENERATE_PASSWORD] = 1;

        parent::setDefaults($defaultValues);
    }
}
