<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormFactoryBuilder
{
    /**
     * HTML_QuickForm_advanced_element_finder   ?
     * HTML_QuickForm_button                    ButtonType
     * HTML_QuickForm_button_radio              RadioType
     * HTML_QuickForm_button_reset              ResetType
     * HTML_QuickForm_button_submit             SubmitType
     * HTML_QuickForm_category                  CategoryType
     * HTML_QuickForm_checkbox                  CheckboxType
     * HTML_QuickForm_date                      DateType
     *                                          TimeType
     *                                          DateTimeType
     * HTML_QuickForm_datepicker
     * HTML_QuickForm_group                     ?
     * HTML_QuickForm_hidden                    HiddenType
     * HTML_QuickForm_html                      HtmlType
     *                                          MessageType
     * HTML_QuickForm_image                     ?
     * HTML_QuickForm_link                      UrlType
     * HTML_QuickForm_password                  PasswordType
     * HTML_QuickForm_select                    ChoiceType
     * HTML_QuickForm_static                    HiddenType
     *                                          VisualContentType
     * HTML_QuickForm_stylefile                 FileType
     * HTML_QuickForm_text                      TextType
     * HTML_QuickForm_textarea                  TextAreaType
     *
     * @var \Doctrine\Common\Collections\ArrayCollection<\Symfony\Component\Form\FormTypeInterface>
     */
    protected ArrayCollection $additionalFormTypes;

    public function __construct(
        protected ValidatorInterface $validator, protected CsrfTokenManagerInterface $csrfTokenManager,
        protected Translator $translator
    )
    {
        $this->additionalFormTypes = new ArrayCollection();
    }

    public function addAdditionalFormType(FormTypeInterface $formType): FormFactoryBuilder
    {
        $this->additionalFormTypes->set(get_class($formType), $formType);

        return $this;
    }

    public function createFormFactory(): FormFactoryInterface
    {
        $formFactoryBuilder = Forms::createFormFactoryBuilder();

        $formFactoryBuilder->addExtension(new HttpFoundationExtension());
        $formFactoryBuilder->addExtension(new CsrfExtension($this->csrfTokenManager));
        $formFactoryBuilder->addExtension(
            new ValidatorExtension(validator: $this->validator, translator: $this->translator)
        );
        $formFactoryBuilder->addTypes($this->getAdditionalFormTypes()->toArray());

        return $formFactoryBuilder->getFormFactory();
    }

    public function getAdditionalFormTypes(): ArrayCollection
    {
        return $this->additionalFormTypes;
    }
}