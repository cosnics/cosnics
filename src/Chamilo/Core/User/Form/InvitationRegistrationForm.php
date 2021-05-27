<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 *
 * @package user.lib.forms
 */
class InvitationRegistrationForm extends FormValidator
{
    use DependencyInjectionContainerTrait;

    // Constants
    const PASSWORD = 'password';
    const PASSWORD_CONFIRMATION = 'password_confirmation';

    /**
     *
     * @var Invitation
     */
    private $invitation;

    /**
     * Creates a new RegisterForm Used for a guest to register him/herself
     */
    public function __construct($action, $invitation)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->initializeContainer();
        $this->build_basic_form();
        $this->invitation = $invitation;
        $this->setDefaults();
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    /**
     * Creates a new basic form
     */
    public function build_basic_form()
    {
        $this->add_information_message(
            'introduction', null, Translation::get('InvitedUserRegistrationIntroduction'), true
        );

        $this->addElement('category', Translation::get('Profile'));

        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_USERNAME, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );
        $this->addRule(User::PROPERTY_USERNAME, Translation::get('UsernameNotAvailable'), 'username_available');

        // $this->add_warning_message('password_requirements', null, Translation::get('GeneralPasswordRequirements'));

        $this->addElement(
            'password', self::PASSWORD, Translation::get('Password'),
            array('size' => 40, 'autocomplete' => 'off', 'id' => 'password')
        );
        $this->addElement(
            'password', self::PASSWORD_CONFIRMATION, Translation::get('PasswordConfirmation'),
            array('size' => 40, 'autocomplete' => 'off')
        );
        $this->addRule(array(self::PASSWORD, self::PASSWORD_CONFIRMATION), Translation::get('PassTwo'), 'compare');
        $this->addRule(
            self::PASSWORD, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );
        $this->addRule(
            self::PASSWORD_CONFIRMATION, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );


        $this->addElement('category', Translation::get('BasicProfile'));

        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_FIRSTNAME, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), array("size" => "50"));
        $this->addRule(
            User::PROPERTY_LASTNAME, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), array("size" => "50"));
        // $this->addRule(User::PROPERTY_EMAIL, Translation::get('ThisFieldIsRequired', null, Utilities ::
        // COMMON_LIBRARIES), 'required');
        // $this->addRule(User::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        $this->freeze(User::PROPERTY_EMAIL);


        if (Configuration::getInstance()->get_setting(array(Manager::context(), 'enable_terms_and_conditions')))
        {
            $this->addElement('category', Translation::get('Information'));
            $this->addElement(
                'textarea', 'conditions', Translation::get('TermsAndConditions'),
                array('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;')
            );
            $this->addElement('checkbox', 'conditions_accept', '', Translation::get('IAccept'));
            $this->addRule(
                'conditions_accept', Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('CreateAccount'));
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates the user
     */
    public function create_user()
    {
        $invitation = $this->invitation;
        $values = $this->exportValues();

        $user = new User();
        $user->set_username($values[User::PROPERTY_USERNAME]);
        $user->set_password($this->getHashingUtilities()->hashString($values[self::PASSWORD]));
        $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
        $user->set_lastname($values[User::PROPERTY_LASTNAME]);
        $user->set_email($values[User::PROPERTY_EMAIL]);
        $user->set_active(1);
        $user->set_registration_date(time());
        $user->set_activation_date($invitation->get_date());
        $user->set_expiration_date($invitation->get_expiration_date());

        if ($user->create())
        {
            $invitation->set_user_created(1);
            $invitation->update();

            $this->send_mail($user);

            Session::register('_uid', intval($user->get_id()));
            Event::trigger(
                'Register', Manager::context(),
                array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id())
            );
            Event::trigger('Login', Manager::context(), array('server' => $_SERVER, 'user' => $user));

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = [])
    {
        $invitation = $this->invitation;
        $defaults[User::PROPERTY_EMAIL] = $invitation->get_email();
        $defaults['conditions'] = Manager::get_terms_and_conditions();
        parent::setDefaults($defaults);
    }

    /**
     * Sends an email to the registered/created user
     */
    public function send_mail($user)
    {
        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->exportValue(self::PASSWORD);
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

        $subject = Translation::get('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = Configuration::getInstance()->get_setting(array(Manager::context(), 'email_template'));
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail($subject, $body, $user->get_email());

        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }
    }
}
