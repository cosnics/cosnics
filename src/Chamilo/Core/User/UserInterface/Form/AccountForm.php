<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeableUsernameInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_category;
use HTML_QuickForm_static;

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

        $allowedToChangeFirstName =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeGivenName');
        $allowedToChangeLastName = $this->getContainer()->getParameter('cosnics.application.user.rights.changeSurname');
        $allowedToChangeUsername =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeUsername') &&
            $authentication instanceof ChangeableUsernameInterface;
        $allowedToChangeEmailAddress =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeEmail');
        $allowedToChangeOfficialCode =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeOfficialCode');
        $allowedToChangePassword =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changePassword') &&
            $authentication instanceof ChangeablePasswordInterface;

        $requireEmail = $this->getContainer()->getParameter('cosnics.application.user.resuire.email');
        $requireOfficialCode = $this->getContainer()->getParameter('cosnics.application.user.require.officialCode');

        $this->buildPersonalDetailsCategoryForm(
            $allowedToChangeFirstName, $allowedToChangeLastName, $allowedToChangeUsername, $requireEmail,
            $allowedToChangeEmailAddress, $requireOfficialCode, $allowedToChangeOfficialCode
        );

        $this->buildPasswordCategoryForm($allowedToChangePassword, false, true, true);

        $this->buildSecurityTokenForm();

        if ($this->canUserChangeAnything()) {
            $this->addSaveResetButtons();
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildSecurityTokenForm(bool $includeCategoryTitle = true): void
    {
        $translator = $this->getTranslator();

        if ($includeCategoryTitle) {
            $this->addElement(HTML_QuickForm_category::class, $translator->trans('Other'));
        }
        $this->addElement(
            HTML_QuickForm_static::class, User::PROPERTY_SECURITY_TOKEN, $translator->trans('SecurityToken')
        );
    }

    protected function canUserChangeAnything(): bool
    {
        $authentication =
            $this->authenticationValidator->getAuthenticationByType($this->user->getAuthenticationSource());

        $allowedToChangeFirstName =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeGivenName');
        $allowedToChangeLastName = $this->getContainer()->getParameter('cosnics.application.user.rights.changeSurname');
        $allowedToChangeUsername =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeUsername') &&
            $authentication instanceof ChangeableUsernameInterface;
        $allowedToChangeEmailAddress =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeEmail');
        $allowedToChangeOfficialCode =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changeOfficialCode');
        $allowedToChangePassword =
            $this->getContainer()->getParameter('cosnics.application.user.rights.changePassword') &&
            $authentication instanceof ChangeablePasswordInterface;

        return $allowedToChangeFirstName || $allowedToChangeLastName || $allowedToChangeUsername ||
            $allowedToChangeEmailAddress || $allowedToChangeOfficialCode || $allowedToChangePassword;
    }

    /**
     * @throws \QuickformException
     */
    public function checkAllowedToChangePassword($exportValues): true|array
    {
        $newPassword = $exportValues[User::PROPERTY_PASSWORD];

        if (empty($newPassword)) {
            return true;
        }

        if (empty($this->exportValue(self::PROPERTY_CURRENT_PASSWORD))) {
            return [
                User::PROPERTY_PASSWORD => $this->getTranslator()->trans('EnterCurrentPassword', [], Manager::CONTEXT)
            ];
        }

        return true;
    }

    /**
     * @param string[] $defaultValues
     *
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $user = $this->user;

        $defaultValues[DataClass::PROPERTY_ID] = $user->getId();
        $defaultValues[User::PROPERTY_SURNAME] = $user->getSurname();
        $defaultValues[User::PROPERTY_GIVEN_NAME] = $user->getGivenName();
        $defaultValues[User::PROPERTY_EMAIL] = $user->getEmail();
        $defaultValues[User::PROPERTY_USERNAME] = $user->getUsername();
        $defaultValues[User::PROPERTY_OFFICIAL_CODE] = $user->getOfficialCode();
        $defaultValues[User::PROPERTY_SECURITY_TOKEN] = $user->getSecurityToken();

        parent::setDefaults($defaultValues);
    }
}
