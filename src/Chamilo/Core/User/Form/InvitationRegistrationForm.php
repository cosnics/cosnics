<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\User\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InvitationRegistrationForm extends FormValidator
{
    // Constants
    public const PASSWORD = 'password';
    public const PASSWORD_CONFIRMATION = 'password_confirmation';

    /**
     * @var Invitation
     */
    private $invitation;

    /**
     * Creates a new RegisterForm Used for a guest to register him/herself
     */
    public function __construct($action, $invitation)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->build_basic_form();
        $this->invitation = $invitation;
        $this->setDefaults();
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

        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_USERNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        $this->addRule(User::PROPERTY_USERNAME, Translation::get('UsernameNotAvailable'), 'username_available');

        // $this->add_warning_message('password_requirements', null, Translation::get('GeneralPasswordRequirements'));

        $this->addElement(
            'password', self::PASSWORD, Translation::get('Password'),
            ['size' => 40, 'autocomplete' => 'off', 'id' => 'password']
        );
        $this->addElement(
            'password', self::PASSWORD_CONFIRMATION, Translation::get('PasswordConfirmation'),
            ['size' => 40, 'autocomplete' => 'off']
        );
        $this->addRule([self::PASSWORD, self::PASSWORD_CONFIRMATION], Translation::get('PassTwo'), 'compare');
        $this->addRule(
            self::PASSWORD, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addRule(
            self::PASSWORD_CONFIRMATION, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->addElement('category', Translation::get('BasicProfile'));

        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_FIRSTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), ['size' => '50']);
        $this->addRule(
            User::PROPERTY_LASTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), ['size' => '50']);
        // $this->addRule(User::PROPERTY_EMAIL, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required');
        // $this->addRule(User::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        $this->freeze(User::PROPERTY_EMAIL);

        if ($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'enable_terms_and_conditions']))
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

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('CreateAccount'));
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
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

            $this->getSession()->set(Manager::SESSION_USER_ID, intval($user->getId()));
            Event::trigger(
                'Register', Manager::CONTEXT, ['target_user_id' => $user->getId(), 'action_user_id' => $user->getId()]
            );
            Event::trigger('Login', Manager::CONTEXT, ['server' => $_SERVER, 'user' => $user]);

            return true;
        }
        else
        {
            return false;
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
     * Sends an email to the registered/created user
     */
    public function send_mail($user)
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->exportValue(self::PASSWORD);
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

        $subject = Translation::get('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = $configurationConsulter->getSetting([Manager::CONTEXT, 'email_template']);
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail($subject, $body, $user->get_email());

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
        $invitation = $this->invitation;
        $defaults[User::PROPERTY_EMAIL] = $invitation->get_email();
        $defaults['conditions'] =
            implode(PHP_EOL, file($this->getSystemPathBuilder()->getBasePath() . 'files/documentation/license.txt'));
        parent::setDefaults($defaults);
    }
}
