<?php
namespace Chamilo\Core\Metadata\Vocabulary\Form;

use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Form for the element
 */
class VocabularyForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary
     */
    private $vocabulary;

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary $vocabulary
     * @param string $formUrl
     *
     * @throws \Exception
     */
    public function __construct(
        Vocabulary $vocabulary, $formUrl
    )
    {
        parent::__construct('vocabulary', self::FORM_METHOD_POST, $formUrl);

        $this->vocabulary = $vocabulary;

        $this->buildForm();
        $this->setFormDefaults();
    }

    /**
     * Builds this form
     *
     * @throws \Exception
     */
    protected function buildForm()
    {
        $element = DataManager::retrieve_by_id(
            Element::class, $this->vocabulary->get_element_id()
        );

        $this->addElement('category', Translation::get('General'));
        $this->addElement(
            'static', null, Translation::get('Element', null, 'Chamilo\Core\Metadata'), $element->render_name()
        );

        if ($this->vocabulary->isForEveryone())
        {
            $displayUser = Translation::get('PredefinedValues', null, 'Chamilo\Core\Metadata\Element');
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
                throw new Exception(Translation::get('UnknownUser'));
            }
        }

        $this->addElement('static', null, Translation::get('User', null, 'Chamilo\Core\Metadata'), $displayUser);

        $this->addElement(
            'text', Vocabulary::PROPERTY_VALUE, Translation::get('Value', null, Utilities::COMMON_LIBRARIES)
        );
        $this->addRule(
            Vocabulary::PROPERTY_VALUE, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('checkbox', Vocabulary::PROPERTY_DEFAULT_VALUE, Translation::get('DefaultValue'));

        $this->getEntityTranslationFormService()->addFieldsToForm($this);
        $this->addSaveResetButtons();
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityTranslationFormService
     */
    protected function getEntityTranslationFormService()
    {
        return $this->getService(EntityTranslationFormService::class);
    }

    /**
     * Sets the default values
     * @throws \Exception
     */
    protected function setFormDefaults()
    {
        $defaults = array();

        $defaults[Vocabulary::PROPERTY_VALUE] = $this->vocabulary->get_value();
        $defaults[Vocabulary::PROPERTY_DEFAULT_VALUE] = $this->vocabulary->get_default_value();

        $this->setDefaults($defaults);

        $this->getEntityTranslationFormService()->setFormDefaults($this, $this->vocabulary);
    }
}