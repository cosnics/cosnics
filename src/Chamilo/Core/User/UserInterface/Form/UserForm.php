<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Rule\HTML_QuickForm_Rule_Filetype;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Rule\HTML_QuickForm_Rule_Username;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_Rule_Compare;
use HTML_QuickForm_Rule_Email;

abstract class UserForm extends FormValidator
{
    public const string PROPERTY_CONFIRM_PASSWORD = 'confirm_password';
    public const string PROPERTY_CURRENT_PASSWORD = 'current_password';
    public const string PROPERTY_GENERATE_PASSWORD = 'generate_password';
    public const string PROPERTY_SEND_MAIL = 'send_mail';

    /**
     * @throws \QuickformException
     */
    public function __construct(string $formName, string $action)
    {
        parent::__construct($formName, self::FORM_METHOD_POST, $action);

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildAccountCategoryForm(bool $isLockoutRisk = false): void
    {
        $translator = $this->getTranslator();

        if ($isLockoutRisk) {
            $this->addWarningMessage(
                $translator->trans('LockOutWarningMessage', [], Manager::CONTEXT)
            );
        }

        $this->addCheckbox(User::PROPERTY_ACTIVE, $translator->trans('Active', [], Manager::CONTEXT));
        $this->addCheckbox(
            User::PROPERTY_PLATFORM_ADMINISTRATOR, $translator->trans('PlatformAdministrator', [], Manager::CONTEXT)
        );
    }

    abstract public function buildForm(): void;

    /**
     * @throws \QuickformException
     */
    public function buildOtherCategoryForm(): void
    {
        $this->addCheckbox(
            self::PROPERTY_SEND_MAIL, $this->getTranslator()->trans('SendMailToUser', [], Manager::CONTEXT)
        );
    }

    /**
     * @throws \QuickformException
     */
    public function buildPasswordCategoryForm(
        bool $allowedToChangePassword = true, bool $allowedToGeneratePassword = true,
        bool $requiresCurrentPassword = false, bool $requiresPasswordConfirmation = false
    ): void
    {
        if ($allowedToChangePassword) {
            $translator = $this->getTranslator();

            if ($allowedToGeneratePassword) {
                $this->addCheckbox(
                    self::PROPERTY_GENERATE_PASSWORD, $translator->trans('AutoGeneratePassword', [], Manager::CONTEXT)
                );
            }

            if ($requiresCurrentPassword) {
                $this->addElement(
                    HTML_QuickForm_password::class, self::PROPERTY_CURRENT_PASSWORD,
                    $translator->trans('CurrentPassword', [], Manager::CONTEXT),
                    ['autocomplete' => 'off', 'class' => 'form-control']
                );

                $this->addFormRule([$this, 'checkCurrentPasswordEntered']);
            }

            $this->addElement(
                HTML_QuickForm_password::class, User::PROPERTY_PASSWORD,
                $translator->trans('Password', [], Manager::CONTEXT),
                ['autocomplete' => 'off', 'class' => 'form-control']
            );

            if ($requiresPasswordConfirmation) {
                $this->addElement(
                    HTML_QuickForm_password::class, self::PROPERTY_CONFIRM_PASSWORD,
                    $translator->trans('PasswordConfirmation', [], Manager::CONTEXT),
                    ['autocomplete' => 'off', 'class' => 'form-control']
                );

                $this->addRule(
                    [User::PROPERTY_PASSWORD, self::PROPERTY_CONFIRM_PASSWORD],
                    $translator->trans('PassTwo', [], Manager::CONTEXT), HTML_QuickForm_Rule_Compare::class
                );
            }

            $this->addElement(
                HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
                $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) . 'Jquery/jquery.jpassword.js'
            )
            );

            $this->addFormRule([$this, 'checkPasswordRequirements']);
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildPersonalDetailsCategoryForm(
        bool $allowedToChangeFirstName = true, bool $allowedToChangeLastName = true,
        bool $allowedToChangeUsername = true, bool $requiresEmail = true, bool $allowedToChangeEmailAddress = true,
        bool $requiresOfficialCode = true, bool $allowedToChangeOfficialCode = true
    ): void
    {
        $translator = $this->getTranslator();

        // Firstname
        $this->addTextfield(
            User::PROPERTY_GIVEN_NAME, $translator->trans('FirstName', [], Manager::CONTEXT), $allowedToChangeFirstName
        );

        if (!$allowedToChangeFirstName) {
            $this->freeze([User::PROPERTY_GIVEN_NAME]);
            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_GIVEN_NAME);
        }
        else {
            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'trim');
        }

        // Lastname
        $this->addTextfield(
            User::PROPERTY_SURNAME, $translator->trans('LastName', [], Manager::CONTEXT), $allowedToChangeLastName
        );

        if (!$allowedToChangeLastName) {
            $this->freeze([User::PROPERTY_SURNAME]);
            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_SURNAME);
        }
        else {
            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'trim');
        }

        // Email
        $this->addTextfield(
            User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT),
            $allowedToChangeEmailAddress && $requiresEmail
        );

        if (!$allowedToChangeEmailAddress) {
            $this->freeze(User::PROPERTY_EMAIL);
            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_EMAIL);
        }
        else {
            $this->addRule(User::PROPERTY_EMAIL, $translator->trans('EmailWrong', [], Manager::CONTEXT),
                HTML_QuickForm_Rule_Email::class);
            $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
            $this->applyFilter(User::PROPERTY_EMAIL, 'trim');
        }

        $this->addRule(User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT),
            HTML_QuickForm_Rule_Email::class);

        // Username
        $this->addTextfield(
            User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), $allowedToChangeUsername
        );

        if (!$allowedToChangeUsername) {
            $this->freeze(User::PROPERTY_USERNAME);
            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_USERNAME);
        }
        else {
            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
            $this->addRule(
                User::PROPERTY_USERNAME, $translator->trans('UsernameWrong', [], Manager::CONTEXT),
                HTML_QuickForm_Rule_Username::class
            );
        }

        // Official Code
        $this->addTextfield(
            User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            $allowedToChangeOfficialCode && $requiresOfficialCode
        );

        if (!$allowedToChangeOfficialCode) {
            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_OFFICIAL_CODE);
        }
        else {
            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildPictureCategoryForm(
        ?string $encodedUserPicture = null, ?string $userFullname = null
    ): void
    {
        $translator = $this->getTranslator();

        if (!is_null($encodedUserPicture)) {
            $html = [];

            $html[] = '<div class="mb-3">';
            $html[] = '<h6>' . $translator->trans('CurrentImage', [], Manager::CONTEXT) . '</h6>';
            $html[] = '<img class="img-thumbnail" src="' . $encodedUserPicture . '" alt="' . $userFullname . '" />';
            $html[] = '</div>';

            $this->addElement(HTML_QuickForm_html::class, implode(PHP_EOL, $html));
        }

        $this->addFile(
            User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture', [], Manager::CONTEXT),
            $translator->trans('AllowedProfileImageFormats', [], Manager::CONTEXT)
        );
        $this->addRule(
            User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT),
            HTML_QuickForm_Rule_Filetype::class, ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF']
        );
    }

    /**
     * @throws \QuickformException
     */
    public function checkCurrentPasswordEntered($exportValues): true|array
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

    public function checkPasswordRequirements($exportValues): true|array
    {
        $customPassword =
            empty($exportValues[User::PROPERTY_PASSWORD]) && !$exportValues[self::PROPERTY_GENERATE_PASSWORD];

        if (!$customPassword) {
            return true;
        }

        $newPassword = $exportValues[User::PROPERTY_PASSWORD];

        if (strlen($newPassword) < 6) {
            return ['pw' => $this->getTranslator()->trans('PasswordRequirements', [], Manager::CONTEXT)];
        }

        return true;
    }
}
