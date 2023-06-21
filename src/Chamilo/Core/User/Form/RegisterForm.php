<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package user.lib.forms
 */
class RegisterForm extends FormValidator
{
    use DependencyInjectionContainerTrait;

    // Constants
    public const RESULT_ERROR = 'UserUpdateFailed';

    public const RESULT_SUCCESS = 'UserUpdated';

    public const TYPE_CREATE = 1;

    public const TYPE_EDIT = 2;

    private $parent;

    private $unencryptedpass;

    private $user;

    /**
     * Creates a new RegisterForm Used for a guest to register him/herself
     */
    public function __construct($user, $action)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->initializeContainer();
        $this->user = $user;
        $this->build_creation_form();
        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    public function build_basic_form()
    {
        $this->addElement('category', Translation::get('Basic'));
        // Lastname
        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_LASTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        // Firstname
        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_FIRSTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), ['size' => '50']);

        if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'require_email']))
        {
            $this->addRule(
                User::PROPERTY_EMAIL, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }

        $this->addRule(User::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        // Username
        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_USERNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        // pw
        $group = [];
        $group[] = &$this->createElement('radio', 'pass', null, Translation::get('AutoGeneratePassword') . '<br />', 1);
        $group[] = &$this->createElement('radio', 'pass', null, null, 0);
        $group[] = &$this->createElement('password', User::PROPERTY_PASSWORD, null, null);
        $this->addGroup($group, 'pw', Translation::get('Password'), '');

        $this->addElement('category', Translation::get('Additional'));

        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, Translation::get('OfficialCode'), ['size' => '50']
        );

        if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'require_official_code']))
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }

        // Picture URI
        if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_change_user_picture']))
        {
            $this->addElement('file', User::PROPERTY_PICTURE_URI, Translation::get('AddPicture'));
        }
        $allowed_picture_types = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        $this->addRule(
            User::PROPERTY_PICTURE_URI, Translation::get('OnlyImagesAllowed'), 'filetype', $allowed_picture_types
        );
        // Phone Number
        $this->addElement('text', User::PROPERTY_PHONE, Translation::get('PhoneNumber'), ['size' => '50']);

        // Status
        if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_teacher_registration']))
        {
            $status = [];
            $status[5] = Translation::get('Student');
            $status[1] = Translation::get('CourseAdmin');
            $this->addElement('select', User::PROPERTY_STATUS, Translation::get('Status'), $status);
        }
        // Send email
        $group = [];
        $group[] = $this->createElement(
            'radio', 'send_mail', null, Translation::get('ConfirmYes', null, StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', 'send_mail', null, Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'mail', Translation::get('SendMailToNewUser'), '&nbsp;');
        // Submit button
        // $this->addElement('submit', 'user_settings', 'OK');

        if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'enable_terms_and_conditions']))
        {
            $this->addElement('category', Translation::get('Information'));
            $this->addElement(
                'textarea', 'conditions', Translation::get('TermsAndConditions'),
                ['cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;']
            );
            $this->addElement('checkbox', 'conditions_accept', '', Translation::get('IAccept'));
            $this->addRule(
                'conditions_accept', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Register'), null, null, new FontAwesomeGlyph('user')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates a creation form
     */
    public function build_creation_form()
    {
        $this->build_basic_form();
    }

    /**
     * Creates the user
     */
    public function create_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        $password = $values['pw']['pass'] == '1' ? Text::generate_password() : $values['pw'][User::PROPERTY_PASSWORD];

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
            $user->set_official_code($values[User::PROPERTY_OFFICIAL_CODE]);
            $user->set_phone($values[User::PROPERTY_PHONE]);

            if (!Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_teacher_registration']))
            {
                $values[User::PROPERTY_STATUS] = User::STATUS_STUDENT;
            }

            $user->set_status(intval($values[User::PROPERTY_STATUS]));

            $code = Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'days_valid']);

            if ($code == 0)
            {
                $user->set_active(1);
            }
            else
            {
                $user->set_activation_date(time());
                $user->set_expiration_date(strtotime('+' . $code . ' days', time()));
            }

            $user->set_registration_date(time());
            $send_mail = intval($values['mail']['send_mail']);

            if ($send_mail)
            {
                $this->send_email($user);
            }

            if (Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_registration']) == 2)
            {
                $user->set_approved(0);
                $user->set_active(0);

                return $user->create();
            }

            if ($user->create())
            {
                $this->getSession()->set(Manager::SESSION_USER_ID, intval($user->get_id()));
                Event::trigger(
                    'Register', Manager::CONTEXT,
                    ['target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
                {
                    if ($_FILES[User::PROPERTY_PICTURE_URI] &&
                        file_exists($_FILES[User::PROPERTY_PICTURE_URI]['tmp_name']))
                    {
                        $userPictureProvider->setUserPicture($user, $user, $_FILES[User::PROPERTY_PICTURE_URI]);

                        if (!$user->update())
                        {
                            return false;
                        }
                    }
                }

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return - 1;
        }
    }

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    /**
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    /**
     * @return \Chamilo\Core\User\Picture\UserPictureProviderInterface
     */
    public function getUserPictureProvider()
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }

    /**
     * Sends an email to the registered/created user
     */
    public function send_email($user)
    {
        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->unencryptedpass;
        $options['site_name'] = Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'site_name']);
        $options['site_url'] = $this->getWebPathBuilder()->getBasePath();
        $options['admin_firstname'] = Configuration::getInstance()->get_setting(
            ['Chamilo\Core\Admin', 'administrator_firstname']
        );
        $options['admin_surname'] = Configuration::getInstance()->get_setting(
            ['Chamilo\Core\Admin', 'administrator_surname']
        );
        $options['admin_telephone'] = Configuration::getInstance()->get_setting(
            ['Chamilo\Core\Admin', 'administrator_telephone']
        );
        $options['admin_email'] = Configuration::getInstance()->get_setting(
            ['Chamilo\Core\Admin', 'administrator_email']
        );

        $subject = Translation::get('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'email_template']);
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
        catch (Exception $ex)
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
        if ($this->form_type == self::TYPE_EDIT)
        {
            $defaults['pw']['pass'] = 2;
            $defaults[User::PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
            $defaults[User::PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
        }
        else
        {
            $defaults['pw']['pass'] = $user->get_password();
            $defaults[User::PROPERTY_DATABASE_QUOTA] = '300';
            $defaults[User::PROPERTY_DISK_QUOTA] = '209715200';
        }

        $defaults['admin'][User::PROPERTY_PLATFORMADMIN] = $user->getPlatformAdmin();
        $defaults['mail']['send_mail'] = 1;
        $defaults[User::PROPERTY_ID] = $user->get_id();
        $defaults[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User::PROPERTY_EMAIL] = $user->get_email();
        $defaults[User::PROPERTY_USERNAME] = $user->get_username();
        $defaults[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User::PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaults[User::PROPERTY_PHONE] = $user->get_phone();
        $defaults['conditions'] =
            implode(PHP_EOL, file($this->getSystemPathBuilder()->getBasePath() . 'files/documentation/license.txt'));
        parent::setDefaults($defaults);
    }
}
