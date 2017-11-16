<?php
namespace Chamilo\Core\Metadata\Relation\Form;

use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the element
 */
class RelationForm extends FormValidator
{

    /**
     *
     * @var Relation
     */
    private $relation;

    /**
     *
     * @var \Chamilo\Core\Metadata\Service\EntityTranslationFormService
     */
    private $entityTranslationFormService;

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param Relation $relation
     */
    public function __construct(Relation $relation, EntityTranslationFormService $entityTranslationFormServic, $form_url)
    {
        parent::__construct('relation', 'post', $form_url);
        
        $this->relation = $relation;
        $this->entityTranslationFormService = $entityTranslationFormServic;
        $this->entityTranslationFormService->setFormValidator($this);
        
        $this->buildForm();
        $this->setFormDefaults();
    }

    /**
     * Builds this form
     */
    protected function buildForm()
    {
        $this->addElement('category', Translation::get('General'));
        
        $this->addElement(
            'text', 
            Relation::PROPERTY_NAME, 
            Translation::get('Name', null, Utilities::COMMON_LIBRARIES));
        $this->addRule(
            Relation::PROPERTY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
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
        $this->setDefaults(array(Relation::PROPERTY_NAME => $this->relation->get_name()));
        
        $this->entityTranslationFormService->setFormDefaults();
    }
}