<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePasswordInterface;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsernameInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccountForm extends UserForm
{
    protected AuthenticationValidator $authenticationValidator;

    private User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $user, string $action, AuthenticationValidator $authenticationValidator)
    {
        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;

        parent::__construct('user_account', $action);
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $authentication =
            $this->authenticationValidator->getAuthenticationByType($this->user->getAuthenticationSource());

        $allowedToChangeFirstName = $this->getSetting('allow_change_firstname');
        $allowedToChangeLastName = $this->getSetting('allow_change_lastname');
        $allowedToChangeUsername =
            $this->getSetting('allow_change_username') && $authentication instanceof ChangeableUsernameInterface;
        $allowedToChangeEmailAddress = $this->getSetting('allow_change_email');
        $allowedToChangeOfficialCode = $this->getSetting('allow_change_official_code');
        $allowedToChangePassword =
            $this->getSetting('allow_change_password') && $authentication instanceof ChangeablePasswordInterface;

        $requireEmail = $this->getSetting('require_email');
        $requireOfficialCode = $this->getSetting('require_official_code');

        $this->buildPersonalDetailsCategoryForm(
            true, $allowedToChangeFirstName, $allowedToChangeLastName, $allowedToChangeUsername, $requireEmail,
            $allowedToChangeEmailAddress, $requireOfficialCode, $allowedToChangeOfficialCode
        );

        $this->buildPasswordCategoryForm($allowedToChangePassword, false, true, true);

        $this->buildSecurityTokenForm();

        if ($this->canUserChangeAnything())
        {
            $this->addSaveResetButtons();
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildSecurityTokenForm(bool $includeCategoryTitle = true): void
    {
        if ($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'show_personal_token']))
        {
            $translator = $this->getTranslator();

            if ($includeCategoryTitle)
            {
                $this->addElement('category', $translator->trans('Other'));
            }
            $this->addElement('static', User::PROPERTY_SECURITY_TOKEN, $translator->trans('SecurityToken'));
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
        $newPassword = $exportValues[User::PROPERTY_PASSWORD];

        if (empty($newPassword))
        {
            return true;
        }

        if (empty($this->exportValue(self::PROPERTY_CURRENT_PASSWORD)))
        {
            return [
                User::PROPERTY_PASSWORD => $this->getTranslator()->trans('EnterCurrentPassword', [], Manager::CONTEXT)
            ];
        }

        return true;
    }

    public function getSetting(string $variable): bool
    {
        return (bool) $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, $variable]);
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
