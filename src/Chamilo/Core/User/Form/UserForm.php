<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;

class UserForm extends FormValidator
{
    public const RESULT_ERROR = 'UserUpdateFailed';
    public const RESULT_SUCCESS = 'UserUpdated';

    public const TYPE_CREATE = 1;
    public const TYPE_EDIT = 2;

    protected User $actionUser;

    protected User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $actionUser, string $action, ?User $user = null)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->actionUser = $actionUser;
        $this->user = $user;

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

        // Lastname
        $this->addElement(
            'text', User::PROPERTY_LASTNAME, $translator->trans('LastName', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(
            User::PROPERTY_LASTNAME, $fieldRequiredMessage, 'required'
        );
        // Firstname
        $this->addElement(
            'text', User::PROPERTY_FIRSTNAME, $translator->trans('FirstName', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(
            User::PROPERTY_FIRSTNAME, $fieldRequiredMessage, 'required'
        );
        // Email
        $this->addElement(
            'text', User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT), ['size' => '50']
        );
        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']))
        {
            $this->addRule(
                User::PROPERTY_EMAIL, $fieldRequiredMessage, 'required'
            );
        }
        $this->addRule(User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT), 'email');
        // Username
        $this->addElement(
            'text', User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(User::PROPERTY_USERNAME, $fieldRequiredMessage, 'required');

        $group = [];
        $group[] = $this->createElement(
            'radio', User::PROPERTY_ACTIVE, null, $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', User::PROPERTY_ACTIVE, null, $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'active', $translator->trans('Active', [], Manager::CONTEXT), '&nbsp;');

        // pw
        $group = [];

        if ($this->formType == self::TYPE_EDIT)
        {
            $group[] = $this->createElement(
                'radio', 'pass', null, $translator->trans('KeepPassword', [], Manager::CONTEXT) . '<br />', 2
            );
        }

        $group[] = $this->createElement(
            'radio', 'pass', null, $translator->trans('AutoGeneratePassword', [], Manager::CONTEXT) . '<br />', 1
        );
        $group[] = $this->createElement('radio', 'pass', null, null, 0);
        $group[] = $this->createElement('password', User::PROPERTY_PASSWORD, null, ['autocomplete' => 'off']);
        $this->addGroup($group, 'pw', $translator->trans('Password', [], Manager::CONTEXT), '');

        $this->addFormRule([$this, 'checkPasswordRequirements']);

        $this->addTimePeriodSelection('ExpirationDate', User::PROPERTY_ACTIVATION_DATE, User::PROPERTY_EXPIRATION_DATE);

        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            ['size' => '50']
        );
        // put restrictions on the official code
        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']) &&
            $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']) == 1)
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE, $fieldRequiredMessage, 'required'
            );
        }

        $userPictureProvider = $this->getUserPictureProvider();

        // Show user picture
        $this->addElement(
            'static', null, $translator->trans('CurrentImage', [], Manager::CONTEXT),
            '<img class="my-account-photo" src="' .
            $userPictureProvider->getUserPictureAsBase64String($this->user, $this->user) . '" alt="' .
            $this->user->get_fullname() . '" />'
        );

        // Picture URI
        $this->addElement('file', User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture'));
        $allowed_picture_types = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        $this->addRule(
            User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT), 'filetype',
            $allowed_picture_types
        );
        $this->addElement('static', null, null, $translator->trans('AllowedProfileImageFormats', [], Manager::CONTEXT));

        // Phone Number
        $this->addElement(
            'text', User::PROPERTY_PHONE, $translator->trans('PhoneNumber', [], Manager::CONTEXT), ['size' => '50']
        );

        // Disk Quota
        $this->addElement(
            'text', User::PROPERTY_DISK_QUOTA, $translator->trans('DiskQuota', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addRule(
            User::PROPERTY_DISK_QUOTA, $translator->trans('ThisFieldMustBeNumeric', [], StringUtilities::LIBRARIES),
            'numeric'
        );
        // Database Quota
        $this->addElement(
            'text', User::PROPERTY_DATABASE_QUOTA, $translator->trans('DatabaseQuota', [], Manager::CONTEXT),
            ['size' => '50']
        );
        $this->addRule(
            User::PROPERTY_DATABASE_QUOTA, $translator->trans('ThisFieldMustBeNumeric', [], StringUtilities::LIBRARIES),
            'numeric'
        );

        // Status
        $status = [];
        $status[5] = $translator->trans('Student', [], Manager::CONTEXT);
        $status[1] = $translator->trans('CourseAdmin', [], Manager::CONTEXT);
        $this->addElement('select', User::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT), $status);
        // Platform admin
        if ($this->user->isPlatformAdmin() && $this->user->get_id() == $this->actionUser->get_id() &&
            $this->formType == self::TYPE_EDIT)
        {
            $this->add_warning_message(
                'admin_lockout_message', null, $translator->trans('LockOutWarningMessage', [], Manager::CONTEXT)
            );
        }
        $group = [];
        $group[] = $this->createElement(
            'radio', User::PROPERTY_PLATFORMADMIN, null,
            $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', User::PROPERTY_PLATFORMADMIN, null,
            $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'admin', $translator->trans('PlatformAdministrator', [], Manager::CONTEXT), '&nbsp;');

        // Send email
        $group = [];
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'mail', $translator->trans('SendMailToNewUser', [], Manager::CONTEXT), '&nbsp;');

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Makes sure that users can't change their password to an unsafe value.
     * Password must contain at least 8 characters
     *
     * @return bool
     */
    public function checkPasswordRequirements()
    {
        $exportValues = $this->exportValues();
        $customPassword = $exportValues['pw']['pass'] == 0;

        if (!$customPassword)
        {
            return true;
        }

        $newPassword = $exportValues['pw'][User::PROPERTY_PASSWORD];

        if (strlen($newPassword) < 6)
        {
            return false;
        }

        return true;
    }

    /**
     * Creates the user, and stores it in the database
     */
    public function create_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        $password = $values['pw']['pass'] == '1' ? $this->getPasswordGenerator()->generatePassword() :
            $values['pw'][User::PROPERTY_PASSWORD];

        if (DataManager::is_username_available(
            $values[User::PROPERTY_USERNAME], $values[User::PROPERTY_ID]
        ))
        {
            $user->set_id($values[User::PROPERTY_ID]);
            $user->set_lastname($values[User::PROPERTY_LASTNAME]);
            $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
            $user->set_email($values[User::PROPERTY_EMAIL]);
            $user->set_username($values[User::PROPERTY_USERNAME]);
            $user->set_password($this->getHashingUtilities()->hashString($password));
            $this->unencryptedpass = $password;

            if ($values[self::PROPERTY_TIME_PERIOD_FOREVER] != 0)
            {
                $user->set_expiration_date(0);
                $user->set_activation_date(0);
            }
            else
            {
                $act_date =
                    DatetimeUtilities::getInstance()->timeFromDatepicker($values[User::PROPERTY_ACTIVATION_DATE]);
                $exp_date =
                    DatetimeUtilities::getInstance()->timeFromDatepicker($values[User::PROPERTY_EXPIRATION_DATE]);
                $user->set_activation_date($act_date);
                $user->set_expiration_date($exp_date);
            }

            $user->set_official_code($values[User::PROPERTY_OFFICIAL_CODE]);
            $user->set_phone($values[User::PROPERTY_PHONE]);
            $user->set_status(intval($values[User::PROPERTY_STATUS]));
            if ($values[User::PROPERTY_DATABASE_QUOTA] != '')
            {
                $user->set_database_quota(intval($values[User::PROPERTY_DATABASE_QUOTA]));
            }
            if ($values[User::PROPERTY_DISK_QUOTA] != '')
            {
                $user->set_disk_quota(intval($values[User::PROPERTY_DISK_QUOTA]));
            }

            $user->set_platformadmin(intval($values['admin'][User::PROPERTY_PLATFORMADMIN]));
            $send_mail = intval($values['mail']['send_mail']);
            if ($send_mail)
            {
                $this->send_email($user);
            }

            $user->set_active(intval($values['active'][User::PROPERTY_ACTIVE]));
            $user->set_registration_date(time());

            $value = $user->create();

            foreach ($values['rights_templates']['template'] as $rights_template_id)
            {
                $user->add_rights_template_link($rights_template_id);
            }

            $userPictureProvider = $this->getUserPictureProvider();

            if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
            {
                if ($_FILES[User::PROPERTY_PICTURE_URI] && file_exists($_FILES[User::PROPERTY_PICTURE_URI]['tmp_name']))
                {
                    $userPictureProvider->setUserPicture($user, $user, $_FILES[User::PROPERTY_PICTURE_URI]);

                    if (!$user->update())
                    {
                        return false;
                    }
                }
            }

            if ($value)
            {
                Event::trigger(
                    'Create', Manager::CONTEXT,
                    ['target_user_id' => $user->get_id(), 'action_user_id' => $this->actionUser->get_id()]
                );
            }

            return $value;
        }
        else
        {
            return - 1; // Username not available
        }
    }

    public function getActionUser(): User
    {
        return $this->actionUser;
    }

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    public function getHashingUtilities(): HashingUtilities
    {
        return $this->getService(HashingUtilities::class);
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->getService(PasswordGeneratorInterface::class);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }

    /**
     * Sends an email to the updated/new user
     */
    public function send_email($user)
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->unencryptedpass;
        $options['site_name'] = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
        $options['site_url'] = $this->getWebPathBuilder()->getBasePath();
        $options['admin_firstname'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_firstname']
        );
        $options['admin_surname'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_surname']
        );
        $options['admin_telephone'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_telephone']
        );
        $options['admin_email'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_email']
        );

        $subject = $this->getTranslator()->trans('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = $configurationConsulter->getSetting([Manager::CONTEXT, 'email_template']);
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject, $body, $user->get_email(), true, [], [],
            $options['admin_firstname'] . ' ' . $options['admin_surname'], $options['admin_email']
        );

        $mailer = $this->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception)
        {
        }
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $user = $this->user;

        if ($this->formType == self::TYPE_EDIT)
        {
            $expiration_date = $user->get_expiration_date();
            if ($expiration_date != 0)
            {
                $defaults[self::PROPERTY_TIME_PERIOD_FOREVER] = 0;
                $defaults[User::PROPERTY_ACTIVATION_DATE] = $user->get_activation_date();
                $defaults[User::PROPERTY_EXPIRATION_DATE] = $user->get_expiration_date();
            }
            else
            {
                $defaults[self::PROPERTY_TIME_PERIOD_FOREVER] = 1;
            }

            $defaults['pw']['pass'] = 2;
            $defaults[User::PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
            $defaults[User::PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
        }
        else
        {
            $defaults[self::PROPERTY_TIME_PERIOD_FOREVER] = 1;

            $defaults[User::PROPERTY_EXPIRATION_DATE] = strtotime(
                '+ ' . intval($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'days_valid'])) .
                'Days', time()
            );
            $defaults['pw']['pass'] = $user->get_password();

            $defaults[User::PROPERTY_DATABASE_QUOTA] = '300';
            $defaults[User::PROPERTY_DISK_QUOTA] = '209715200';
        }

        $defaults['admin'][User::PROPERTY_PLATFORMADMIN] = $user->getPlatformAdmin();
        $defaults['mail']['send_mail'] = 0;
        $defaults[DataClass::PROPERTY_ID] = $user->get_id();
        $defaults[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User::PROPERTY_EMAIL] = $user->get_email();
        $defaults[User::PROPERTY_USERNAME] = $user->get_username();
        $defaults[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User::PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaults[User::PROPERTY_PHONE] = $user->get_phone();
        $defaults[User::PROPERTY_STATUS] = $user->get_status();
        $defaults['active'][User::PROPERTY_ACTIVE] = !is_null($user->get_active()) ? $user->get_active() : 1;

        parent::setDefaults($defaults);
    }

    /**
     * Updates the user with the new data
     */
    public function update_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        if ($values['pw']['pass'] != '2')
        {
            $this->unencryptedpass = $values['pw']['pass'] == '1' ? $this->getPasswordGenerator()->generatePassword() :
                $values['pw'][User::PROPERTY_PASSWORD];
            $password = $this->getHashingUtilities()->hashString($this->unencryptedpass);
            $user->set_password($password);
        }

        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
        {
            if ($_FILES[User::PROPERTY_PICTURE_URI] && file_exists($_FILES[User::PROPERTY_PICTURE_URI]['tmp_name']))
            {
                $userPictureProvider->setUserPicture($user, $user, $_FILES[User::PROPERTY_PICTURE_URI]);
            }
        }

        $user->set_lastname($values[User::PROPERTY_LASTNAME]);
        $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
        $user->set_email($values[User::PROPERTY_EMAIL]);
        $user->set_username($values[User::PROPERTY_USERNAME]);

        if ($values[self::PROPERTY_TIME_PERIOD_FOREVER] != 0)
        {
            $user->set_expiration_date(0);
            $user->set_activation_date(0);
        }
        else
        {
            $act_date = DatetimeUtilities::getInstance()->timeFromDatepicker($values[User::PROPERTY_ACTIVATION_DATE]);
            $exp_date = DatetimeUtilities::getInstance()->timeFromDatepicker($values[User::PROPERTY_EXPIRATION_DATE]);
            $user->set_activation_date($act_date);
            $user->set_expiration_date($exp_date);
        }

        $user->set_official_code($values[User::PROPERTY_OFFICIAL_CODE]);
        $user->set_phone($values[User::PROPERTY_PHONE]);
        $user->set_status(intval($values[User::PROPERTY_STATUS]));
        $user->set_database_quota(intval($values[User::PROPERTY_DATABASE_QUOTA]));
        $user->set_disk_quota(intval($values[User::PROPERTY_DISK_QUOTA]));

        $user->set_active(intval($values['active'][User::PROPERTY_ACTIVE]));
        $user->set_platformadmin(intval($values['admin'][User::PROPERTY_PLATFORMADMIN]));
        $send_mail = intval($values['mail']['send_mail']);

        if ($send_mail)
        {
            $this->send_email($user);
        }

        $value = $user->update();

        if ($value)
        {
            Event::trigger(
                'Update', Manager::CONTEXT, [
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $this->actionUser->get_id()
                ]
            );
        }

        return $value;
    }
}
