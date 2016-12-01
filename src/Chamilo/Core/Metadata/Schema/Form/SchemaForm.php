<?php
namespace Chamilo\Core\Metadata\Schema\Form;

use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the schema
 * 
 * @package Chamilo\Core\Metadata\Schema\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema
     */
    private $schema;

    /**
     *
     * @var \Chamilo\Core\Metadata\Service\EntityTranslationFormService
     */
    private $entityTranslationFormService;

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param \Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema $schema
     */
    public function __construct(Schema $schema, EntityTranslationFormService $entityTranslationFormService, $formUrl)
    {
        parent::__construct('schema', 'post', $formUrl);
        
        $this->schema = $schema;
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
        $this->addElement('category', Translation::get('General'));
        $this->addElement('text', Schema::PROPERTY_NAMESPACE, Translation::get('Namespace'), array("size" => "50"));
        
        $this->addRule(
            Schema::PROPERTY_NAMESPACE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Schema::PROPERTY_NAME, Translation::get('Name'), array("size" => "50"));
        
        $this->addRule(
            Schema::PROPERTY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Schema::PROPERTY_URL, Translation::get('Url'), array("size" => "50"));
        
        $this->addRule(
            Schema::PROPERTY_URL, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('category');
        
        $this->entityTranslationFormService->addFieldsToForm();
        $this->addSaveResetButtons();
    }

    /**
     * Sets the default values
     * 
     * @param \Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema $schema
     */
    protected function setFormDefaults()
    {
        $defaults = array();
        
        $defaults[Schema::PROPERTY_NAMESPACE] = $this->schema->get_namespace();
        $defaults[Schema::PROPERTY_NAME] = $this->schema->get_name();
        $defaults[Schema::PROPERTY_URL] = $this->schema->get_url();
        
        $this->setDefaults($defaults);
        
        $this->entityTranslationFormService->setFormDefaults();
    }
}