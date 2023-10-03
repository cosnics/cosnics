<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Form
 */
class RegisterForm extends UserForm
{
    public const PROPERTY_ACCEPT_CONDITIONS = 'accept_conditions';
    public const PROPERTY_CONDITIONS = 'conditions';

    /**
     * @throws \QuickformException
     */
    public function __construct(string $action)
    {
        parent::__construct('register', $action);
    }

    /**
     * @throws \QuickformException
     */
    public function buildConditionsCategoryForm(): void
    {
        if ($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'enable_terms_and_conditions']))
        {
            $translator = $this->getTranslator();

            $this->addElement('category', $translator->trans('Information', [], Manager::CONTEXT));
            $this->addElement(
                'textarea', 'conditions', $translator->trans('TermsAndConditions', [], Manager::CONTEXT),
                ['cols' => 80, 'rows' => 10, 'disabled' => 'disabled', 'style' => 'background-color: white;']
            );
            $this->addElement(
                'checkbox', self::PROPERTY_ACCEPT_CONDITIONS, '', $translator->trans('IAccept', [], Manager::CONTEXT)
            );
            $this->addRule(
                self::PROPERTY_ACCEPT_CONDITIONS,
                $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $requireEmail = (bool) $configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']);
        $requireOfficialCode = (bool) $configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']);

        $this->buildPersonalDetailsCategoryForm(true, true, true, true, $requireEmail, true, $requireOfficialCode);
        $this->buildPasswordCategoryForm();
        $this->buildPictureCategoryForm();
        $this->buildConditionsCategoryForm();
        $this->buildOtherCategoryForm();
        $this->addSaveResetButtons();
    }

    public function buildPersonalDetailsCategoryForm(
        bool $includeCategoryTitle = true, bool $allowedToChangeFirstName = true, bool $allowedToChangeLastName = true,
        bool $allowedToChangeUsername = true, bool $requiresEmail = true, bool $allowedToChangeEmailAddress = true,
        bool $requiresOfficialCode = true, bool $allowedToChangeOfficialCode = true
    ): void
    {
        parent::buildPersonalDetailsCategoryForm(
            $includeCategoryTitle, $allowedToChangeFirstName, $allowedToChangeLastName, $allowedToChangeUsername,
            $requiresEmail, $allowedToChangeEmailAddress, $requiresOfficialCode, $allowedToChangeOfficialCode
        );

        if ($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'allow_teacher_registration']))
        {
            $translator = $this->getTranslator();

            $status = [];

            $status[5] = $translator->trans('Student');
            $status[1] = $translator->trans('CourseAdmin');

            $this->addElement(
                'select', User::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT), $status
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $defaults[User::PROPERTY_DATABASE_QUOTA] = '300';
        $defaults[User::PROPERTY_DISK_QUOTA] = '209715200';
        $defaults[UserForm::PROPERTY_SEND_MAIL] = 1;
        $defaults[self::PROPERTY_CONDITIONS] =
            file_get_contents($this->getSystemPathBuilder()->getRootPath() . 'LICENSE');

        parent::setDefaults($defaults);
    }
}
