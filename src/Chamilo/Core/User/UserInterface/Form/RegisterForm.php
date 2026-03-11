<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_category;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_textarea;

/**
 * @package Chamilo\Core\User\Form
 */
class RegisterForm extends UserForm
{
    public const string PROPERTY_ACCEPT_CONDITIONS = 'accept_conditions';
    public const string PROPERTY_CONDITIONS = 'conditions';

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
        if ($this->getContainer()->getParameter('cosnics.application.user.enableTermsAndConditions')) {
            $translator = $this->getTranslator();

            $this->addElement(HTML_QuickForm_category::class, $translator->trans('Information', [], Manager::CONTEXT));
            $this->addElement(
                HTML_QuickForm_textarea::class, 'conditions',
                $translator->trans('TermsAndConditions', [], Manager::CONTEXT),
                ['cols' => 80, 'rows' => 10, 'disabled' => 'disabled', 'style' => 'background-color: white;']
            );
            $this->addCheckbox(
                self::PROPERTY_ACCEPT_CONDITIONS, $translator->trans('IAccept', [], Manager::CONTEXT)
            );
            $this->addRule(
                self::PROPERTY_ACCEPT_CONDITIONS,
                $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                HTML_QuickForm_Rule_Required::class
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $requireEmail = $this->getContainer()->getParameter('cosnics.application.user.require.email');
        $requireOfficialCode = $this->getContainer()->getParameter('cosnics.application.user.require.officialCode');

        $this->buildPersonalDetailsCategoryForm(true, true, true, $requireEmail, true, $requireOfficialCode);
        $this->buildPasswordCategoryForm();
        $this->buildPictureCategoryForm();
        $this->buildConditionsCategoryForm();
        $this->buildOtherCategoryForm();
        $this->addSaveResetButtons();
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $defaults[UserForm::PROPERTY_SEND_MAIL] = 1;
        $defaults[self::PROPERTY_CONDITIONS] =
            file_get_contents($this->getSystemPathBuilder()->getRootPath() . 'LICENSE');

        parent::setDefaults($defaults);
    }
}
