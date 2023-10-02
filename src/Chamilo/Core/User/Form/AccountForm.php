<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePasswordInterface;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsernameInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
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

    protected AuthenticationValidator $authenticationValidator;

    private User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $user, string $action, AuthenticationValidator $authenticationValidator)
    {
        parent::__construct('user_account', self::FORM_METHOD_POST, $action);

        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;

        $this->buildEditingForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildBasicForm(): void
    {
        $translator = $this->getTranslator();
        $configurationConsulter = $this->getConfigurationConsulter();
        $authentication =
            $this->authenticationValidator->getAuthenticationByType($this->user->getAuthenticationSource());

        $personalDataPlaceholder = $configurationConsulter->getSetting([Manager::CONTEXT, 'personal_data_placeholder']);
        $requiredFieldMessage = $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES);

        $allowChangePlaceholderData =
            $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_placeholder_data']);
        $allowChangeFirstName = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_firstname']);
        $allowChangeLastName = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_lastname']);
        $allowChangeUsername = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_username']);
        $allowChangeEmailAddress = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_email']);
        $allowChangeOfficialCode =
            $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_official_code']);
        $allowChangePassword = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_password']);

        $requireEmail = $configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']);
        $requireOfficialCode = $configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']);

        $this->addElement('category', $translator->trans('PersonalDetails', [], Manager::CONTEXT));
        // Name
        $this->addElement(
            'text', User::PROPERTY_LASTNAME, $translator->trans('LastName', [], Manager::CONTEXT), ['size' => '50']
        );
        $this->addElement(
            'text', User::PROPERTY_FIRSTNAME, $translator->trans('FirstName', [], Manager::CONTEXT), ['size' => '50']
        );

        if ($allowChangeFirstName == 0)
        {
            if (!($allowChangePlaceholderData == 1 && $this->user->get_firstname() == $personalDataPlaceholder))
            {
                $this->freeze([User::PROPERTY_FIRSTNAME]);
            }
        }
        if ($allowChangeLastName == 0)
        {
            if (!($allowChangePlaceholderData == 1 && $this->user->get_lastname() == $personalDataPlaceholder))
            {
                $this->freeze([User::PROPERTY_LASTNAME]);
            }
        }

        $this->applyFilter([User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME], 'stripslashes');
        $this->applyFilter([User::PROPERTY_LASTNAME, User::PROPERTY_FIRSTNAME], 'trim');

        if (($allowChangeLastName == 1) ||
            ($allowChangePlaceholderData == 1 && $this->user->get_lastname() == $personalDataPlaceholder))
        {
            $this->addRule(
                User::PROPERTY_LASTNAME, $requiredFieldMessage, 'required'
            );
        }
        if (($allowChangeFirstName == 1) ||
            ($allowChangePlaceholderData == 1 && $this->user->get_firstname() == $personalDataPlaceholder))
        {
            $this->addRule(
                User::PROPERTY_FIRSTNAME, $requiredFieldMessage, 'required'
            );
        }
        // Official Code
        $this->addElement(
            'text', User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            ['size' => '50']
        );

        if ($allowChangeOfficialCode == 0)
        {
            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
        }

        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
        $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');

        if ($requireOfficialCode && $allowChangeOfficialCode == 1)
        {
            $this->addRule(User::PROPERTY_OFFICIAL_CODE, $requiredFieldMessage, 'required');
        }

        // Email
        $this->addElement(
            'text', User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT), ['size' => '50']
        );

        if ($allowChangeEmailAddress == 0)
        {
            if (!($allowChangePlaceholderData == 1 && $this->user->get_email() == $personalDataPlaceholder))
            {
                $this->freeze(User::PROPERTY_EMAIL);
            }
        }
        elseif ($requireEmail ||
            ($allowChangePlaceholderData == 1 && $this->user->get_email() == $personalDataPlaceholder))
        {
            $this->addRule(User::PROPERTY_EMAIL, $requiredFieldMessage, 'required');
            $this->addRule(User::PROPERTY_EMAIL, $translator->trans('EmailWrong', [], Manager::CONTEXT), 'email');
        }

        $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
        $this->applyFilter(User::PROPERTY_EMAIL, 'trim');

        // Username
        $this->addElement('text', User::PROPERTY_USERNAME, $translator->trans('Username'), [], Manager::CONTEXT,
            ['size' => '50']);

        if ($allowChangeUsername == 0 || !$authentication instanceof ChangeableUsernameInterface)
        {
            $this->freeze(User::PROPERTY_USERNAME);
        }

        if ($allowChangeUsername == 1)
        {
            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
            $this->addRule(
                User::PROPERTY_USERNAME, $requiredFieldMessage, 'required'
            );
            $this->addRule(
                User::PROPERTY_USERNAME, $translator->trans('UsernameWrong', [], Manager::CONTEXT), 'username'
            );
        }

        // Password
        if ($allowChangePassword == 1 && $authentication instanceof ChangeablePasswordInterface)
        {
            $this->addElement('category', $translator->trans('ChangePassword', [], Manager::CONTEXT));

            $this->addElement(
                'static', null, null,
                '<em>' . $translator->trans('EnterCurrentPassword', [], Manager::CONTEXT) . '</em>'
            );
            $this->addElement(
                'password', User::PROPERTY_PASSWORD, $translator->trans('CurrentPassword', [], Manager::CONTEXT),
                ['size' => 40, 'autocomplete' => 'off']
            );
            $this->addElement(
                'static', null, null,
                '<em>' . $translator->trans('EnterNewPasswordTwice', [], Manager::CONTEXT) . '</em>'
            );
            $this->addElement(
                'password', self::NEW_PASSWORD, $translator->trans('NewPassword', [], Manager::CONTEXT),
                ['size' => 40, 'autocomplete' => 'off', 'id' => 'new_password', 'pattern' => '.{6,}']
            );
            $this->addElement(
                'password', self::NEW_PASSWORD_CONFIRMATION,
                $translator->trans('PasswordConfirmation', [], Manager::CONTEXT),
                ['size' => 40, 'autocomplete' => 'off']
            );
            $this->addRule(
                [self::NEW_PASSWORD, self::NEW_PASSWORD_CONFIRMATION],
                $translator->trans('PassTwo', [], Manager::CONTEXT), 'compare'
            );

            $this->addFormRule([$this, 'checkPasswordRequirements']);
            $this->addFormRule([$this, 'checkAllowedToChangePassword']);

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
            $this->addElement('category', $translator->trans('Other'));
            $this->addElement('static', User::PROPERTY_SECURITY_TOKEN, $translator->trans('SecurityToken'));
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildEditingForm(): void
    {
        $translator = $this->getTranslator();
        $this->buildBasicForm();

        if ($this->canUserChangeAnything())
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
            );

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    protected function canUserChangeAnything(): bool
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
     * @throws \QuickformException
     */
    public function checkAllowedToChangePassword($exportValues): true|array
    {
        $newPassword = $exportValues[self::NEW_PASSWORD];

        if (empty($newPassword))
        {
            return true;
        }

        if (empty($this->exportValue(User::PROPERTY_PASSWORD)))
        {
            return [self::NEW_PASSWORD => $this->getTranslator()->trans('EnterCurrentPassword', [], Manager::CONTEXT)];
        }

        return true;
    }

    public function checkPasswordRequirements($exportValues): true|array
    {
        $customPassword = $exportValues['pw']['pass'] == 0;

        if (!$customPassword)
        {
            return true;
        }

        $newPassword = $exportValues['pw'][User::PROPERTY_PASSWORD];

        if (strlen($newPassword) < 6)
        {
            return ['pw' => $this->getTranslator()->trans('PasswordRequirements', [], Manager::CONTEXT)];
        }

        return true;
    }

    /**
     * @param string[] $defaultValues
     *
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $user = $this->user;

        $defaultValues[DataClass::PROPERTY_ID] = $user->getId();
        $defaultValues[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $defaultValues[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaultValues[User::PROPERTY_EMAIL] = $user->get_email();
        $defaultValues[User::PROPERTY_USERNAME] = $user->get_username();
        $defaultValues[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaultValues[User::PROPERTY_SECURITY_TOKEN] = $user->get_security_token();

        parent::setDefaults($defaultValues);
    }
}
