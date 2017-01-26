<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: account_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.forms
 */
class AccountForm extends FormValidator
{
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'UserUpdated';
    const RESULT_ERROR = 'UserUpdateFailed';
    const NEW_PASSWORD = 'new_password';
    const NEW_PASSWORD_CONFIRMATION = 'new_password_confirmation';

    private $parent;

    private $user;

    private $unencryptedpass;

    private $adm;

    /**
     * Creates a new AccountForm
     */
    public function __construct($form_type, $user, $action)
    {
        parent::__construct('user_account', 'post', $action);

        $this->user = $user;
        $this->adm = \Chamilo\Core\Admin\Storage\DataManager::getInstance();

        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }

        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    public function build_basic_form()
    {
        $configurationConsulter = Configuration::getInstance();
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $this->user->get_id()
            )
        );

        $this->addElement('category', Translation::get('PersonalDetails'));
        // Name
        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), array("size" => "50"));
        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), array("size" => "50"));

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_firstname')) == 0)
        {
            if (!(($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_placeholder_data')) ==
                    1) && ($this->user->get_firstname() ==
                    $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
            )
            {
                $this->freeze(array(User::PROPERTY_FIRSTNAME));
            }
        }
        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_lastname')) == 0)
        {
            if (!(($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_placeholder_data')) ==
                    1) && ($this->user->get_lastname() ==
                    $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
            )
            {
                $this->freeze(array(User::PROPERTY_LASTNAME));
            }
        }

        $this->applyFilter(array(User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME), 'stripslashes');
        $this->applyFilter(array(User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME), 'trim');

        if (($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_lastname')) == 1) ||
            (($configurationConsulter->get_setting(
                        array(Manager::context(), 'allow_change_placeholder_data')
                    ) == 1) && ($this->user->get_lastname() ==
                    $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
        )
        {
            $this->addRule(
                User::PROPERTY_LASTNAME,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }
        if (($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_firstname')) == 1) ||
            (($configurationConsulter->get_setting(
                        array(Manager::context(), 'allow_change_placeholder_data')
                    ) == 1) && ($this->user->get_firstname() ==
                    $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
        )
        {
            $this->addRule(
                User::PROPERTY_FIRSTNAME,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }
        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, Translation::get('OfficialCode'), array("size" => "50")
        );

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_official_code')) == 0)
        {
            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
        }

        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');

        if ($configurationConsulter->get_setting(array(Manager::context(), 'require_official_code')) &&
            $configurationConsulter->get_setting(array(Manager::context(), 'allow_change_official_code')) == 1
        )
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
        }

        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), array("size" => "50"));

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_email')) == 0)
        {
            if (!(($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_placeholder_data')) ==
                    1) && ($this->user->get_email() ==
                    $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
            )
            {
                $this->freeze(User::PROPERTY_EMAIL);
            }
        }
        else
        {
            if ($configurationConsulter->get_setting(array(Manager::context(), 'require_email')) ||
                (($configurationConsulter->get_setting(
                            array(Manager::context(), 'allow_change_placeholder_data')
                        ) == 1) && ($this->user->get_email() ==
                        $configurationConsulter->get_setting(array(Manager::context(), 'personal_data_placeholder'))))
            )
            {
                $this->addRule(
                    User::PROPERTY_EMAIL,
                    Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                    'required'
                );
                $this->addRule(User::PROPERTY_EMAIL, Translation::get('EmailWrong'), 'email');
            }
        }

        $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
        $this->applyFilter(User::PROPERTY_EMAIL, 'trim');

        // Username
        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), array("size" => "50"));

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_username')) == 0 ||
            !Authentication::factory($this->user->get_auth_source()) instanceof ChangeableUsername
        )
        {
            $this->freeze(User::PROPERTY_USERNAME);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_username')) == 1)
        {
            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
            $this->addRule(
                User::PROPERTY_USERNAME,
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                'required'
            );
            $this->addRule(User::PROPERTY_USERNAME, Translation::get('UsernameWrong'), 'username');
        }

        // Todo: The rule to check unique username should be updated to the LCMS code api
        // $this->addRule(User :: PROPERTY_USERNAME, Translation :: get('UserTaken'), 'username_available',
        // $user_data['username']);
        $this->addElement('category');

        // Password
        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_password')) == 1 &&
            Authentication::factory($this->user->get_auth_source()) instanceof ChangeablePassword
        )
        {
            $this->addElement('category', Translation::get('ChangePassword'));

            // $password_requirements = Authentication ::
            // factory($this->user->get_auth_source())->getPasswordRequirements();
            // if (! is_null($password_requirements))
            // {
            // $this->add_warning_message('password_requirements', null, $password_requirements);
            // }

            $this->addElement('static', null, null, '<em>' . Translation::get('EnterCurrentPassword') . '</em>');
            $this->addElement(
                'password',
                User::PROPERTY_PASSWORD,
                Translation::get('CurrentPassword'),
                array('size' => 40, 'autocomplete' => 'off')
            );
            $this->addElement('static', null, null, '<em>' . Translation::get('EnterNewPasswordTwice') . '</em>');
            $this->addElement(
                'password',
                self::NEW_PASSWORD,
                Translation::get('NewPassword'),
                array('size' => 40, 'autocomplete' => 'off', 'id' => 'new_password', 'pattern' => '.{8,}')
            );
            $this->addElement(
                'password',
                self::NEW_PASSWORD_CONFIRMATION,
                Translation::get('PasswordConfirmation'),
                array('size' => 40, 'autocomplete' => 'off')
            );
            $this->addRule(
                array(self::NEW_PASSWORD, self::NEW_PASSWORD_CONFIRMATION),
                Translation::get('PassTwo'),
                'compare'
            );

            $this->registerRule('checkPasswordRequirements', 'function', 'checkPasswordRequirements', $this);
            $this->registerRule('checkAllowedToChangePassword', 'function', 'checkAllowedToChangePassword', $this);

            $this->addRule(
                self::NEW_PASSWORD,
                Translation::getInstance()->getTranslation('PasswordRequirements', null, 'Chamilo\Core\User'),
                'checkPasswordRequirements'
            );

            $this->addRule(
                self::NEW_PASSWORD,
                Translation::getInstance()->getTranslation('EnterCurrentPassword', null, 'Chamilo\Core\User'),
                'checkAllowedToChangePassword'
            );
            
            $this->addElement(
                'html',
                ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                    'Plugin/Jquery/jquery.jpassword.js'
                )
            );
            $this->addElement(
                'html',
                ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Password.js'
                )
            );
            $this->addElement('category');
        }

        if ($configurationConsulter->get_setting(array(\Chamilo\Core\User\Manager::context(), 'show_personal_token')))
        {
            $this->addElement('category', Translation::get('Other'));
            $this->addElement('static', User::PROPERTY_SECURITY_TOKEN, Translation::get('SecurityToken'));
            $this->addElement('category');
        }
    }

    /**
     * Makes sure that users can't change their password to an unsafe value. Password must contain at least 8 characters
     *
     * @param string $newPassword
     *
     * @return bool
     */
    public function checkPasswordRequirements($newPassword = null)
    {
        if(empty($newPassword))
        {
            return true;
        }

        if(strlen($newPassword) < 8)
        {
            return false;
        }

        return true;
    }

    /**
     * Makes sure that users can only change their password if they fill in the current password
     *
     * @param string $newPassword
     *
     * @return bool
     */
    public function checkAllowedToChangePassword($newPassword = null)
    {
        if(empty($newPassword))
        {
            return true;
        }

        if(empty($this->exportValue(User::PROPERTY_PASSWORD)))
        {
            return false;
        }

        return true;
    }

    /**
     * Builds an editing form
     */
    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', User::PROPERTY_ID);

        if ($this->canUserChangeAnything())
        {
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation::get('Save', null, Utilities::COMMON_LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button',
                'reset',
                Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
            );

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    /**
     * Determines whether or not a user can change anything
     *
     * @return bool
     */
    protected function canUserChangeAnything()
    {
        $configurationConsulter = Configuration::getInstance();

        $settings = array(
            'allow_change_firstname',
            'allow_change_lastname',
            'allow_change_official_code',
            'allow_change_email',
            'allow_change_username',
            'allow_change_user_picture',
            'allow_change_password'
        );

        foreach ($settings as $setting)
        {
            if ($configurationConsulter->get_setting(array(Manager::context(), $setting)))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Builds an update form
     */
    public function update_account()
    {
        $configurationConsulter = Configuration::getInstance();
        $user = $this->user;
        $values = $this->exportValues();

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_firstname')))
        {
            $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_lastname')))
        {
            $user->set_lastname($values[User::PROPERTY_LASTNAME]);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_official_code')))
        {
            $user->set_official_code($values[User::PROPERTY_OFFICIAL_CODE]);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_email')))
        {
            $user->set_email($values[User::PROPERTY_EMAIL]);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_username')) &&
            Authentication::factory($this->user->get_auth_source()) instanceof ChangeableUsername
        )
        {
            $user->set_username($values[User::PROPERTY_USERNAME]);
        }

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_password')) &&
            strlen($values[User::PROPERTY_PASSWORD]) &&
            Authentication::factory($this->user->get_auth_source()) instanceof ChangeablePassword
        )
        {
            $result = Authentication::factory($this->user->get_auth_source())->changePassword(
                $user,
                $values[User::PROPERTY_PASSWORD],
                $values[self::NEW_PASSWORD]
            );
            if (!$result)
            {
                return false;
            }
        }
        $value1 = true;

        if ($configurationConsulter->get_setting(array(Manager::context(), 'allow_change_user_picture')))
        {

            if (isset($_FILES['picture_uri']) && strlen($_FILES['picture_uri']['name']) > 0)
            {
                if (!$_FILES['picture_uri']['error'])
                {
                    $user->set_picture_file($_FILES['picture_uri']);
                }
                else
                {
                    $value1 &= false;
                }
            }
            if (isset($values['remove_picture']))
            {
                $user->delete_picture();
            }
        }

        $value = $user->update();

        if ($value)
        {
            Event::trigger(
                'Update',
                Manager::context(),
                array(
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $user->get_id()
                )
            );
        }

        return $value && $value1;
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $user = $this->user;
        $defaults[User::PROPERTY_ID] = $user->get_id();
        $defaults[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User::PROPERTY_EMAIL] = $user->get_email();
        $defaults[User::PROPERTY_USERNAME] = $user->get_username();
        $defaults[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User::PROPERTY_SECURITY_TOKEN] = $user->get_security_token();
        parent::setDefaults($defaults);
    }
}
