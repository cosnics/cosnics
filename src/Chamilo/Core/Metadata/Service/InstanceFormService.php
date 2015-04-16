<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Core\Metadata\Relation\Service\RelationService;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InstanceFormService
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $formValidator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function __construct(DataClass $entity, FormValidator $formValidator)
    {
        $this->entity = $entity;
        $this->formValidator = $formValidator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function getFormValidator()
    {
        return $this->formValidator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function setFormValidator($formValidator)
    {
        $this->formValidator = $formValidator;
    }

    public function addElements(EntityService $entityService, RelationService $relationService)
    {
        $availableSchemas = $entityService->getAvailableSchemasForEntity($relationService, $this->getEntity());
        
        while ($availableSchema = $availableSchemas->next_result())
        {
            $this->formValidator->addElement(
                'checkbox', 
                InstanceService :: PROPERTY_METADATA_ADD_SCHEMA . '[' . $availableSchema->get_id() . ']', 
                $availableSchema->get_name(), 
                null, 
                null, 
                $availableSchema->get_id());
            
            // $this->formValidator->addGroup($schemaGroup, null, $availableSchema->get_name(), null, false);
        }
        
        // $this->addDependencies();
        
        // $elementService = new ElementService();
        // $elements = $elementService->getElementsForSchemaInstance($this->schemaInstance);
        
        // while ($element = $elements->next_result())
        // {
        // $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' . $this->schemaInstance->get_schema_id() .
        // '][' . $this->schemaInstance->get_id() . '][' . $element->get_id() . ']';
        
        // if ($element->usesVocabulary())
        // {
        // $uniqueIdentifier = UUID :: v4();
        
        // $class = 'metadata-input';
        // if ($element->isVocabularyUserDefined())
        // {
        // $class .= ' metadata-input-new';
        // }
        
        // $tagElementGroup = array();
        // $tagElementGroup[] = $this->formValidator->createElement(
        // 'text',
        // $elementName . '[' . EntityService :: PROPERTY_METADATA_SCHEMA_EXISTING . ']',
        // null,
        // array(
        // 'id' => $uniqueIdentifier,
        // 'class' => $class,
        // 'data-schema-id' => $this->schemaInstance->get_schema_id(),
        // 'data-schema-instance-id' => $this->schemaInstance->get_id(),
        // 'data-element-id' => $element->get_id(),
        // 'data-element-value-limit' => $element->get_value_limit()));
        
        // if ($element->isVocabularyUserDefined())
        // {
        // $tagElementGroup[] = $this->formValidator->createElement(
        // 'hidden',
        // $elementName . '[' . EntityService :: PROPERTY_METADATA_SCHEMA_NEW . ']',
        // null,
        // array('id' => 'new-' . $uniqueIdentifier));
        // }
        
        // $urlRenderer = new Redirect(
        // array(
        // Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: context(),
        // Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: ACTION_SELECT,
        // \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: PARAM_ELEMENT_IDENTIFIER => $uniqueIdentifier,
        // \Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $element->get_id()));
        // $vocabularyUrl = $urlRenderer->getUrl();
        // $onclick = 'vocabulary-selector" onclick="javascript:openPopup(\'' . $vocabularyUrl .
        // '\'); return false;';
        
        // $vocabularyAction = new ToolbarItem(
        // Translation :: get('ShowVocabulary'),
        // Theme :: getInstance()->getImagePath(
        // 'Chamilo\Core\Metadata\Element',
        // 'ValueType/' . $element->get_value_type()),
        // $vocabularyUrl,
        // ToolbarItem :: DISPLAY_ICON,
        // false,
        // $onclick,
        // '_blank');
        
        // $tagElementGroup[] = $this->formValidator->createElement(
        // 'static',
        // null,
        // null,
        // $vocabularyAction->as_html());
        
        // $this->formValidator->addGroup($tagElementGroup, null, $element->get_display_name(), null, false);
        // }
        // else
        // {
        // $this->formValidator->addElement(
        // 'textarea',
        // $elementName,
        // $element->get_display_name(),
        // array('cols' => 60, 'rows' => 6, 'maxlength' => 1000));
        // }
        // }
    }

    public function setDefaults()
    {
        $defaults = array();
        
        $this->formValidator->setDefaults($defaults);
    }
}
