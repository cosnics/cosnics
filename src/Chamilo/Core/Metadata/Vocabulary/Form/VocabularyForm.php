<?php
namespace Chamilo\Core\Metadata\Vocabulary\Form;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Metadata\Service\EntityTranslationFormService;

/**
 * Form for the element
 */
class VocabularyForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary
     */
    private $vocabulary;

    /**
     *
     * @var \Chamilo\Core\Metadata\Service\EntityTranslationFormService
     */
    private $entityTranslationFormService;

    /**
     *
     * @param \Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary $vocabulary
     * @param \Chamilo\Core\Metadata\Service\EntityTranslationFormService $entityTranslationFormService
     * @param string $form_url
     */
    public function __construct(Vocabulary $vocabulary, EntityTranslationFormService $entityTranslationFormService,
        $formUrl)
    {
        parent :: __construct('vocabulary', 'post', $formUrl);

        $this->vocabulary = $vocabulary;
        $this->entityTranslationFormService = $entityTranslationFormService;
        $this->entityTranslationFormService->setFormValidator($this);

        $this->buildForm();
        $this->setFormDefaults();
    }

    /**
     * Builds this form
     */
    protected function buildForm()
    {
        $element = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_by_id(
            Element :: class_name(),
            $this->vocabulary->get_element_id());

        $this->addElement('category', Translation :: get('General'));
        $this->addElement(
            'static',
            null,
            Translation :: get('Element', null, 'Ehb\Core\Metadata'),
            $element->render_name());

        if ($this->vocabulary->isForEveryone())
        {
            $displayUser = Translation :: get('PredefinedValues', null, 'Ehb\Core\Metadata\Element');
        }
        else
        {
            $user = $this->vocabulary->getUser();

            if ($user instanceof User)
            {
                $displayUser = $user->get_fullname();
            }
            else
            {
                throw new \Exception(Translation :: get('UnknownUser'));
            }
        }

        $this->addElement('static', null, Translation :: get('User', null, 'Ehb\Core\Metadata'), $displayUser);

        $this->addElement(
            'text',
            Vocabulary :: PROPERTY_VALUE,
            Translation :: get('Value', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(
            Vocabulary :: PROPERTY_VALUE,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement('checkbox', Vocabulary :: PROPERTY_DEFAULT_VALUE, Translation :: get('DefaultValue'));

        $this->addElement('category');

        $this->entityTranslationFormService->addFieldsToForm();
        $this->addSaveResetButtons();
    }

    /**
     * Sets the default values
     *
     * @param Element $element
     */
    protected function setFormDefaults()
    {
        $defaults = array();

        $defaults[Vocabulary :: PROPERTY_VALUE] = $this->vocabulary->get_value();
        $defaults[Vocabulary :: PROPERTY_DEFAULT_VALUE] = $this->vocabulary->get_default_value();

        $this->setDefaults($defaults);

        $this->entityTranslationFormService->setFormDefaults();
    }
}