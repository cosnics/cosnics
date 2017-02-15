<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: user_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.forms
 */
class UserForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'UserUpdated';
    const RESULT_ERROR = 'UserUpdateFailed';
    const PARAM_FOREVER = 'forever';

    private $parent;

    private $user;

    private $form_user;

    private $unencryptedpass;

    private $adminDM;

    /**
     * Creates a new UserForm Used by the admin to create/update a user
     */
    public function __construct($form_type, $user, $form_user, $action)
    {
        parent::__construct('user_settings', 'post', $action);

        $this->adminDM = \Chamilo\Core\Admin\Storage\DataManager::getInstance();
        $this->user = $user;
        $this->form_user = $form_user;

        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    /**
     * Creates a basic form
     */
    public function build_basic_form()
    {
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $this->user->get_id()
            )
        );

        $this->addElement(
            'html',
            '<img src="' . $profilePhotoUrl->getUrl() . '" alt="' . $this->user->get_fullname() .
            '" style="position:absolute; right: 10px; z-index:1; border:1px solid black; max-width: 150px;"/>'
        );
        // Lastname
        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_LASTNAME,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );
        // Firstname
        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_FIRSTNAME,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );
        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), array("size" => "50"));
        if (Configuration::getInstance()->get_setting(array(Manager::context(), 'require_email')))
        {
            $this->addRule(
                User::PROPERTY_EMAIL,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }
        $this->addRule(User::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        // Username
        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_USERNAME,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $group = array();
        $group[] = &$this->createElement(
            'radio',
            User::PROPERTY_ACTIVE,
            null,
            Translation::get('ConfirmYes', null, Utilities::COMMON_LIBRARIES),
            1
        );
        $group[] = &$this->createElement(
            'radio',
            User::PROPERTY_ACTIVE,
            null,
            Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES),
            0
        );
        $this->addGroup($group, 'active', Translation::get('Active'), '&nbsp;');

        // pw
        $group = array();
        if ($this->form_type == self::TYPE_EDIT)
        {
            $group[] = &$this->createElement('radio', 'pass', null, Translation::get('KeepPassword') . '<br />', 2);
        }
        $group[] = &$this->createElement('radio', 'pass', null, Translation::get('AutoGeneratePassword') . '<br />', 1);
        $group[] = &$this->createElement('radio', 'pass', null, null, 0);
        $group[] = &$this->createElement(
            'password',
            User::PROPERTY_PASSWORD,
            null,
            array('autocomplete' => 'off')
        );
        $this->addGroup($group, 'pw', Translation::get('Password'), '');

        $this->registerRule('checkPasswordRequirements', 'function', 'checkPasswordRequirements', $this);

        $this->addGroupRule(
            'pw', array(
                User::PROPERTY_PASSWORD => array(
                    array(
                        Translation::getInstance()->getTranslation('PasswordRequirements', null, 'Chamilo\Core\User'),
                        'checkPasswordRequirements'
                    )
                )
            )
        );

        // $this->add_forever_or_expiration_date_window(User :: PROPERTY_EXPIRATION_DATE, 'ExpirationDate');
        $this->add_forever_or_timewindow(User::PROPERTY_EXPIRATION_DATE, 'ExpirationDate');

        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, Translation::get('OfficialCode'), array("size" => "50")
        );
        // put restrictions on the official code
        if (Configuration::getInstance()->get_setting(array(Manager::context(), 'require_official_code')) &&
            Configuration::getInstance()->get_setting(array(Manager::context(), 'allow_change_official_code')) == 1
        )
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }

        // Picture URI
        $this->addElement('file', User::PROPERTY_PICTURE_URI, Translation::get('AddPicture'));
        $allowed_picture_types = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
        $this->addRule(
            User::PROPERTY_PICTURE_URI,
            Translation::get('OnlyImagesAllowed'),
            'filetype',
            $allowed_picture_types
        );
        $this->addElement('static', null, null, Translation::get('AllowedProfileImageFormats'));
        // Phone Number
        $this->addElement('text', User::PROPERTY_PHONE, Translation::get('PhoneNumber'), array("size" => "50"));

        // Disk Quota
        $this->addElement('text', User::PROPERTY_DISK_QUOTA, Translation::get('DiskQuota'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_DISK_QUOTA,
            Translation::get('ThisFieldMustBeNumeric', null, Utilities::COMMON_LIBRARIES),
            'numeric',
            null,
            'server'
        );
        // Database Quota
        $this->addElement(
            'text',
            User::PROPERTY_DATABASE_QUOTA,
            Translation::get('DatabaseQuota'),
            array("size" => "50")
        );
        $this->addRule(
            User::PROPERTY_DATABASE_QUOTA,
            Translation::get('ThisFieldMustBeNumeric', null, Utilities::COMMON_LIBRARIES),
            'numeric',
            null,
            'server'
        );

        // Status
        $status = array();
        $status[5] = Translation::get('Student');
        $status[1] = Translation::get('CourseAdmin');
        $this->addElement('select', User::PROPERTY_STATUS, Translation::get('Status'), $status);
        // Platform admin
        if ($this->user->is_platform_admin() && $this->user->get_id() == $this->form_user->get_id() &&
            $this->form_type == self::TYPE_EDIT
        )
        {
            $this->add_warning_message('admin_lockout_message', null, Translation::get('LockOutWarningMessage'));
        }
        $group = array();
        $group[] = &$this->createElement(
            'radio',
            User::PROPERTY_PLATFORMADMIN,
            null,
            Translation::get('ConfirmYes', null, Utilities::COMMON_LIBRARIES),
            1
        );
        $group[] = &$this->createElement(
            'radio',
            User::PROPERTY_PLATFORMADMIN,
            null,
            Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES),
            0
        );
        $this->addGroup($group, 'admin', Translation::get('PlatformAdministrator'), '&nbsp;');

        // Send email
        $group = array();
        $group[] = &$this->createElement(
            'radio',
            'send_mail',
            null,
            Translation::get('ConfirmYes', null, Utilities::COMMON_LIBRARIES),
            1
        );
        $group[] = &$this->createElement(
            'radio',
            'send_mail',
            null,
            Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES),
            0
        );
        $this->addGroup($group, 'mail', Translation::get('SendMailToNewUser'), '&nbsp;');
    }

    /**
     * Makes sure that users can't change their password to an unsafe value. Password must contain at least 8 characters
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

        if (strlen($newPassword) < 8)
        {
            return false;
        }

        return true;
    }

    /**
     * Creates an editing form
     */
    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', User::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES),
            null,
            null,
            'arrow-right'
        );
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates a creating form
     */
    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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
            $this->unencryptedpass =
                $values['pw']['pass'] == '1' ? Text::generate_password() : $values['pw'][User::PROPERTY_PASSWORD];
            $password = Hashing::hash($this->unencryptedpass);
            $user->set_password($password);
        }

        if ($_FILES[User::PROPERTY_PICTURE_URI] && file_exists($_FILES[User::PROPERTY_PICTURE_URI]['tmp_name']))
        {
            $user->set_picture_file($_FILES[User::PROPERTY_PICTURE_URI]);
        }

        $user->set_lastname($values[User::PROPERTY_LASTNAME]);
        $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
        $user->set_email($values[User::PROPERTY_EMAIL]);
        $user->set_username($values[User::PROPERTY_USERNAME]);

        if ($values['ExpirationDateforever'] != 0)
        {
            $user->set_expiration_date(0);
            $user->set_activation_date(0);
        }
        else
        {
            $act_date = DatetimeUtilities::time_from_datepicker($values['ExpirationDatefrom_date']);
            $exp_date = DatetimeUtilities::time_from_datepicker($values['ExpirationDateto_date']);
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
                'Update',
                Manager::context(),
                array(
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $this->form_user->get_id()
                )
            );
        }

        return $value;
    }

    /**
     * Creates the user, and stores it in the database
     */
    public function create_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        $password = $values['pw']['pass'] == '1' ? Text::generate_password() : $values['pw'][User::PROPERTY_PASSWORD];

        if ($_FILES[User::PROPERTY_PICTURE_URI] && file_exists($_FILES[User::PROPERTY_PICTURE_URI]['tmp_name']))
        {
            $user->set_picture_file($_FILES[User::PROPERTY_PICTURE_URI]);
        }

        if (\Chamilo\Core\User\Storage\DataManager::is_username_available(
            $values[User::PROPERTY_USERNAME],
            $values[User::PROPERTY_ID]
        )
        )
        {
            $user->set_id($values[User::PROPERTY_ID]);
            $user->set_lastname($values[User::PROPERTY_LASTNAME]);
            $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
            $user->set_email($values[User::PROPERTY_EMAIL]);
            $user->set_username($values[User::PROPERTY_USERNAME]);
            $user->set_password(Hashing::hash($password));
            $this->unencryptedpass = $password;

            if ($values['ExpirationDateforever'] != 0)
            {
                $user->set_expiration_date(0);
                $user->set_activation_date(0);
            }
            else
            {
                $act_date = DatetimeUtilities::time_from_datepicker($values['ExpirationDatefrom_date']);
                $exp_date = DatetimeUtilities::time_from_datepicker($values['ExpirationDateto_date']);
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

            if ($value)
            {
                Event::trigger(
                    'Create',
                    Manager::context(),
                    array('target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id())
                );
            }

            return $value;
        }
        else
        {
            return - 1; // Username not available
        }
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $user = $this->user;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $expiration_date = $user->get_expiration_date();
            if ($expiration_date != 0)
            {
                $defaults['ExpirationDate' . self::PARAM_FOREVER] = 0;
                $defaults['ExpirationDatefrom_date'] = $user->get_activation_date();
                $defaults['ExpirationDateto_date'] = $user->get_expiration_date();
            }
            else
            {
                $defaults['ExpirationDate' . self::PARAM_FOREVER] = 1;
            }

            $defaults['pw']['pass'] = 2;
            $defaults[User::PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
            $defaults[User::PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
        }
        else
        {
            $defaults['ExpirationDate' . self::PARAM_FOREVER] = 1;

            $defaults['ExpirationDate' . 'to_date'] = strtotime(
                '+ ' . intval(Configuration::getInstance()->get_setting(array(Manager::context(), 'days_valid'))) .
                'Days',
                time()
            );
            $defaults['pw']['pass'] = $user->get_password();

            $defaults[User::PROPERTY_DATABASE_QUOTA] = '300';
            $defaults[User::PROPERTY_DISK_QUOTA] = '209715200';
        }

        $defaults['admin'][User::PROPERTY_PLATFORMADMIN] = $user->get_platformadmin();
        $defaults['mail']['send_mail'] = 0;
        $defaults[User::PROPERTY_ID] = $user->get_id();
        $defaults[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User::PROPERTY_EMAIL] = $user->get_email();
        $defaults[User::PROPERTY_USERNAME] = $user->get_username();
        $defaults[User::PROPERTY_EXPIRATION_DATE] = $user->get_expiration_date();
        $defaults[User::PROPERTY_ACTIVATION_DATE] = $user->get_activation_date();
        $defaults[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User::PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaults[User::PROPERTY_PHONE] = $user->get_phone();
        $defaults[User::PROPERTY_STATUS] = $user->get_status();
        $defaults['active'][User::PROPERTY_ACTIVE] = !is_null($user->get_active()) ? $user->get_active() : 1;

        parent::setDefaults($defaults);
    }

    /**
     * Sends an email to the updated/new user
     */
    public function send_email($user)
    {
        $options = array();
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->unencryptedpass;
        $options['site_name'] = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        $options['site_url'] = Path::getInstance()->getBasePath(true);
        $options['admin_firstname'] = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'administrator_firstname')
        );
        $options['admin_surname'] = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'administrator_surname')
        );
        $options['admin_telephone'] = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'administrator_telephone')
        );
        $options['admin_email'] = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'administrator_email')
        );

        $subject = Translation::get('YourRegistrationOn') . $options['site_name'];

        $body = Configuration::getInstance()->get_setting(array(Manager::context(), 'email_template'));
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject,
            $body,
            $user->get_email(),
            true,
            array(),
            array(),
            $options['admin_firstname'] . ' ' . $options['admin_surname'],
            $options['admin_email']
        );

        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (\Exception $ex)
        {
        }
    }
}
