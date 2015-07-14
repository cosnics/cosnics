<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: register_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package user.lib.forms
 */
class RegisterForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'UserUpdated';
    const RESULT_ERROR = 'UserUpdateFailed';

    private $parent;

    private $user;

    private $unencryptedpass;

    private $adminDM;

    /**
     * Creates a new RegisterForm Used for a guest to register him/herself
     */
    public function __construct($user, $action)
    {
        parent :: __construct('user_settings', 'post', $action);
        
        $this->adminDM = \Chamilo\Core\Admin\Storage\DataManager :: get_instance();
        $this->user = $user;
        $this->build_creation_form();
        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    public function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Basic'));
        // Lastname
        $this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(
            User :: PROPERTY_LASTNAME, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        // Firstname
        $this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'), array("size" => "50"));
        $this->addRule(
            User :: PROPERTY_FIRSTNAME, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        // Email
        $this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        if (PlatformSetting :: get('require_email', Manager :: context()))
        {
            $this->addRule(
                User :: PROPERTY_EMAIL, 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
        }
        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
        // Username
        $this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'), array("size" => "50"));
        $this->addRule(
            User :: PROPERTY_USERNAME, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        // pw
        $group = array();
        $group[] = & $this->createElement(
            'radio', 
            'pass', 
            null, 
            Translation :: get('AutoGeneratePassword') . '<br />', 
            1);
        $group[] = & $this->createElement('radio', 'pass', null, null, 0);
        $group[] = & $this->createElement('password', User :: PROPERTY_PASSWORD, null, null);
        $this->addGroup($group, 'pw', Translation :: get('Password'), '');
        
        $this->addElement('category');
        $this->addElement('category', Translation :: get('Additional'));
        
        // Official Code
        $this->addElement(
            'text', 
            User :: PROPERTY_OFFICIAL_CODE, 
            Translation :: get('OfficialCode'), 
            array("size" => "50"));
        if (PlatformSetting :: get('require_official_code', Manager :: context()))
        {
            $this->addRule(
                User :: PROPERTY_OFFICIAL_CODE, 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
        }
        // Picture URI
        if (PlatformSetting :: get('allow_change_user_picture', Manager :: context()))
        {
            $this->addElement('file', User :: PROPERTY_PICTURE_URI, Translation :: get('AddPicture'));
        }
        $allowed_picture_types = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
        $this->addRule(
            User :: PROPERTY_PICTURE_URI, 
            Translation :: get('OnlyImagesAllowed'), 
            'filetype', 
            $allowed_picture_types);
        // Phone Number
        $this->addElement('text', User :: PROPERTY_PHONE, Translation :: get('PhoneNumber'), array("size" => "50"));
        
        // Status
        if (PlatformSetting :: get('allow_teacher_registration', Manager :: context()))
        {
            $status = array();
            $status[5] = Translation :: get('Student');
            $status[1] = Translation :: get('CourseAdmin');
            $this->addElement('select', User :: PROPERTY_STATUS, Translation :: get('Status'), $status);
        }
        // Send email
        $group = array();
        $group[] = & $this->createElement(
            'radio', 
            'send_mail', 
            null, 
            Translation :: get('ConfirmYes', null, Utilities :: COMMON_LIBRARIES), 
            1);
        $group[] = & $this->createElement(
            'radio', 
            'send_mail', 
            null, 
            Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES), 
            0);
        $this->addGroup($group, 'mail', Translation :: get('SendMailToNewUser'), '&nbsp;');
        // Submit button
        // $this->addElement('submit', 'user_settings', 'OK');
        
        $this->addElement('category');
        
        if (PlatformSetting :: get('enable_terms_and_conditions', Manager :: context()))
        {
            $this->addElement('category', Translation :: get('Information'));
            $this->addElement(
                'textarea', 
                'conditions', 
                Translation :: get('TermsAndConditions'), 
                array('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;'));
            $this->addElement('checkbox', 'conditions_accept', '', Translation :: get('IAccept'));
            $this->addRule(
                'conditions_accept', 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
            $this->addElement('category');
        }
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Register'), 
            array('class' => 'positive register'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
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
        
        $password = $values['pw']['pass'] == '1' ? Text :: generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];
        if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
        {
            $user->set_picture_file($_FILES[User :: PROPERTY_PICTURE_URI]);
        }
        
        if (\Chamilo\Core\User\Storage\DataManager :: is_username_available(
            $values[User :: PROPERTY_USERNAME], 
            $values[User :: PROPERTY_ID]))
        {
            $user->set_id($values[User :: PROPERTY_ID]);
            $user->set_lastname($values[User :: PROPERTY_LASTNAME]);
            $user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
            $user->set_email($values[User :: PROPERTY_EMAIL]);
            $user->set_username($values[User :: PROPERTY_USERNAME]);
            $user->set_password(Hashing :: hash($password));
            $this->unencryptedpass = $password;
            $user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
            $user->set_phone($values[User :: PROPERTY_PHONE]);
            if (! PlatformSetting :: get('allow_teacher_registration', Manager :: context()))
            {
                $values[User :: PROPERTY_STATUS] = STUDENT;
            }
            $user->set_status(intval($values[User :: PROPERTY_STATUS]));
            
            $code = PlatformSetting :: get('days_valid');
            
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
            
            if (PlatformSetting :: get('allow_registration', Manager :: context()) == 2)
            {
                $user->set_approved(0);
                $user->set_active(0);
                return $user->create();
            }
            
            if ($user->create())
            {
                \Chamilo\Libraries\Platform\Session\Session :: register('_uid', intval($user->get_id()));
                Event :: trigger(
                    'register', 
                    Manager :: context(), 
                    array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
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

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $user = $this->user;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $defaults['pw']['pass'] = 2;
            $defaults[User :: PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
            $defaults[User :: PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
        }
        else
        {
            $defaults['pw']['pass'] = $user->get_password();
            $defaults[User :: PROPERTY_DATABASE_QUOTA] = '300';
            $defaults[User :: PROPERTY_DISK_QUOTA] = '209715200';
        }
        
        $defaults['admin'][User :: PROPERTY_PLATFORMADMIN] = $user->get_platformadmin();
        $defaults['mail']['send_mail'] = 1;
        $defaults[User :: PROPERTY_ID] = $user->get_id();
        $defaults[User :: PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User :: PROPERTY_EMAIL] = $user->get_email();
        $defaults[User :: PROPERTY_USERNAME] = $user->get_username();
        $defaults[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User :: PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaults[User :: PROPERTY_PHONE] = $user->get_phone();
        $defaults['conditions'] = Manager :: get_terms_and_conditions();
        parent :: setDefaults($defaults);
    }

    /**
     * Sends an email to the registered/created user
     */
    public function send_email($user)
    {
        $options = array();
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->unencryptedpass;
        $options['site_name'] = PlatformSetting :: get('site_name');
        $options['site_url'] = Path :: getInstance()->getBasePath(true);
        $options['admin_firstname'] = PlatformSetting :: get('administrator_firstname');
        $options['admin_surname'] = PlatformSetting :: get('administrator_surname');
        $options['admin_telephone'] = PlatformSetting :: get('administrator_telephone');
        $options['admin_email'] = PlatformSetting :: get('administrator_email');
        
        $subject = Translation :: get('YourRegistrationOn') . $options['site_name'];
        
        $body = PlatformSetting :: get('email_template', Manager :: context());
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }
        
        $mail = Mail :: factory(
            $subject, 
            $body, 
            $user->get_email(), 
            array(
                Mail :: NAME => $options['admin_firstname'] . ' ' . $options['admin_surname'], 
                Mail :: EMAIL => $options['admin_email']));
        $mail->send();
    }
}
