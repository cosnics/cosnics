<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePasswordInterface;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsernameInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package user.lib.forms
 */
class AccountForm extends FormValidator
{
    use DependencyInjectionContainerTrait;

    public const NEW_PASSWORD = 'new_password';
    public const NEW_PASSWORD_CONFIRMATION = 'new_password_confirmation';

    public const RESULT_ERROR = 'UserUpdateFailed';
    public const RESULT_SUCCESS = 'UserUpdated';

    public const TYPE_EDIT = 2;

    /**
     * @var AuthenticationValidator
     */
    protected $authenticationValidator;

    private $parent;

    private $unencryptedpass;

    private $user;

    /**
     * @param string $form_type
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $action
     * @param \Chamilo\Libraries\Authentication\AuthenticationValidator $authenticationValidator
     *
     * @throws \Exception
     */
    public function __construct($form_type, $user, $action, AuthenticationValidator $authenticationValidator)
    {
        parent::__construct('user_account', self::FORM_METHOD_POST, $action);

        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;

        $this->build_editing_form();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function build_basic_form()
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $this->addElement('category', Translation::get('PersonalDetails'));
        // Name
        $this->addElement('text', User::PROPERTY_LASTNAME, Translation::get('LastName'), ['size' => '50']);
        $this->addElement('text', User::PROPERTY_FIRSTNAME, Translation::get('FirstName'), ['size' => '50']);

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_firstname']) == 0)
        {
            if (!(($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_placeholder_data']) == 1) &&
                ($this->user->get_firstname() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
            {
                $this->freeze([User::PROPERTY_FIRSTNAME]);
            }
        }
        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_lastname']) == 0)
        {
            if (!(($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_placeholder_data']) == 1) &&
                ($this->user->get_lastname() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
            {
                $this->freeze([User::PROPERTY_LASTNAME]);
            }
        }

        $this->applyFilter([User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME], 'stripslashes');
        $this->applyFilter([User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME], 'trim');

        if (($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_lastname']) == 1) ||
            (($configurationConsulter->getSetting(
                        [Manager::CONTEXT, 'allow_change_placeholder_data']
                    ) == 1) && ($this->user->get_lastname() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
        {
            $this->addRule(
                User::PROPERTY_LASTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }
        if (($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_firstname']) == 1) ||
            (($configurationConsulter->getSetting(
                        [Manager::CONTEXT, 'allow_change_placeholder_data']
                    ) == 1) && ($this->user->get_firstname() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
        {
            $this->addRule(
                User::PROPERTY_FIRSTNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }
        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, Translation::get('OfficialCode'), ['size' => '50']
        );

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']) == 0)
        {
            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
        }

        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']) &&
            $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']) == 1)
        {
            $this->addRule(
                User::PROPERTY_OFFICIAL_CODE, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
        }

        // Email
        $this->addElement('text', User::PROPERTY_EMAIL, Translation::get('Email'), ['size' => '50']);

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_email']) == 0)
        {
            if (!(($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_placeholder_data']) == 1) &&
                ($this->user->get_email() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
            {
                $this->freeze(User::PROPERTY_EMAIL);
            }
        }
        elseif ($configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']) ||
            (($configurationConsulter->getSetting(
                        [Manager::CONTEXT, 'allow_change_placeholder_data']
                    ) == 1) && ($this->user->get_email() ==
                    $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']))))
        {
            $this->addRule(
                User::PROPERTY_EMAIL, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
            $this->addRule(User::PROPERTY_EMAIL, Translation::get('EmailWrong'), 'email');
        }

        $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
        $this->applyFilter(User::PROPERTY_EMAIL, 'trim');

        // Username
        $this->addElement('text', User::PROPERTY_USERNAME, Translation::get('Username'), ['size' => '50']);

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_username']) == 0 ||
            !$this->authenticationValidator->getAuthenticationByType($this->user->get_auth_source()) instanceof
                ChangeableUsernameInterface)
        {
            $this->freeze(User::PROPERTY_USERNAME);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_username']) == 1)
        {
            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
            $this->addRule(
                User::PROPERTY_USERNAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );
            $this->addRule(User::PROPERTY_USERNAME, Translation::get('UsernameWrong'), 'username');
        }

        // Password
        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_password']) == 1 &&
            $this->authenticationValidator->getAuthenticationByType($this->user->get_auth_source()) instanceof
            ChangeablePasswordInterface)
        {
            $this->addElement('category', Translation::get('ChangePassword'));

            $this->addElement('static', null, null, '<em>' . Translation::get('EnterCurrentPassword') . '</em>');
            $this->addElement(
                'password', User::PROPERTY_PASSWORD, Translation::get('CurrentPassword'),
                ['size' => 40, 'autocomplete' => 'off']
            );
            $this->addElement('static', null, null, '<em>' . Translation::get('EnterNewPasswordTwice') . '</em>');
            $this->addElement(
                'password', self::NEW_PASSWORD, Translation::get('NewPassword'),
                ['size' => 40, 'autocomplete' => 'off', 'id' => 'new_password', 'pattern' => '.{6,}']
            );
            $this->addElement(
                'password', self::NEW_PASSWORD_CONFIRMATION, Translation::get('PasswordConfirmation'),
                ['size' => 40, 'autocomplete' => 'off']
            );
            $this->addRule(
                [self::NEW_PASSWORD, self::NEW_PASSWORD_CONFIRMATION], Translation::get('PassTwo'), 'compare'
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
                'html', $this->getResourceManager()->getResourceHtml(
                $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) . 'Jquery/jquery.jpassword.js'
            )
            );
            $this->addElement(
                'html', $this->getResourceManager()->getResourceHtml(
                $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'Password.js'
            )
            );
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'show_personal_token']))
        {
            $this->addElement('category', Translation::get('Other'));
            $this->addElement('static', User::PROPERTY_SECURITY_TOKEN, Translation::get('SecurityToken'));
        }
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
                'style_submit_button', 'submit', Translation::get('Save', null, StringUtilities::LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
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
        $configurationConsulter = $this->getConfigurationConsulter();

        $settings = [
            'allow_change_firstname',
            'allow_change_lastname',
            'allow_change_official_code',
            'allow_change_email',
            'allow_change_username',
            'allow_change_user_picture',
            'allow_change_password'
        ];

        foreach ($settings as $setting)
        {
            if ($configurationConsulter->getSetting([Manager::CONTEXT, $setting]))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $newPassword
     *
     * @return bool
     * @throws \Exception
     */
    public function checkAllowedToChangePassword($newPassword = null)
    {
        if (empty($newPassword))
        {
            return true;
        }

        if (empty($this->exportValue(User::PROPERTY_PASSWORD)))
        {
            return false;
        }

        return true;
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
        if (empty($newPassword))
        {
            return true;
        }

        if (strlen($newPassword) < 6)
        {
            return false;
        }

        return true;
    }

    /**
     * @return \Chamilo\Core\User\Picture\UserPictureProviderInterface
     */
    public function getUserPictureProvider()
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }

    /**
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = [], $filter = null)
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

    /**
     * Builds an update form
     */
    public function update_account()
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $user = $this->user;
        $values = $this->exportValues();

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_firstname']))
        {
            $user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_lastname']))
        {
            $user->set_lastname($values[User::PROPERTY_LASTNAME]);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']))
        {
            $user->set_official_code($values[User::PROPERTY_OFFICIAL_CODE]);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_email']))
        {
            $user->set_email($values[User::PROPERTY_EMAIL]);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_username']) &&
            $this->authenticationValidator->getAuthenticationByType($this->user->get_auth_source()) instanceof
            ChangeableUsernameInterface)
        {
            $user->set_username($values[User::PROPERTY_USERNAME]);
        }

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_password']) &&
            strlen($values[User::PROPERTY_PASSWORD]) &&
            $this->authenticationValidator->getAuthenticationByType($this->user->get_auth_source()) instanceof
            ChangeablePasswordInterface)
        {
            $result =
                $this->authenticationValidator->getAuthenticationByType($this->user->get_auth_source())->changePassword(
                    $user, $values[User::PROPERTY_PASSWORD], $values[self::NEW_PASSWORD]
                );
            if (!$result)
            {
                return false;
            }
        }

        $value1 = true;

        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
        {

            if (isset($_FILES[User::PROPERTY_PICTURE_URI]) && strlen($_FILES[User::PROPERTY_PICTURE_URI]['name']) > 0)
            {
                if (!$_FILES[User::PROPERTY_PICTURE_URI]['error'])
                {
                    $userPictureProvider->setUserPicture($user, $user, $_FILES[User::PROPERTY_PICTURE_URI]);
                }
                else
                {
                    $value1 &= false;
                }
            }
            if (isset($values['remove_picture']))
            {
                $userPictureProvider->deleteUserPicture($user, $user);
            }
        }

        $value = $user->update();

        if ($value)
        {
            Event::trigger(
                'Update', Manager::CONTEXT, [
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $user->get_id()
                ]
            );
        }

        return $value && $value1;
    }
}
