<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderFormService
{
    const TAB_METADATA = 'Metadata';

    /**
     *
     * @var \Chamilo\Core\Metadata\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @var \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    private $elementService;

    /**
     *
     * @var \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    private $relationService;

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
     * @var string[]
     */
    private $elementOptions;

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    private $availableSchemas;

    /**
     *
     * @var string[]
     */
    private $elementNames;

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Element[]
     */
    private $schemaElements;

    /**
     *
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function __construct(EntityService $entityService, ElementService $elementService, 
        RelationService $relationService, DataClassEntity $entity, FormValidator $formValidator)
    {
        $this->entityService = $entityService;
        $this->elementService = $elementService;
        $this->relationService = $relationService;
        $this->entity = $entity;
        $this->formValidator = $formValidator;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Service\EntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     */
    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    public function getElementService()
    {
        return $this->elementService;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function setElementService($elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    public function getRelationService()
    {
        return $this->relationService;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     */
    public function setRelationService($relationService)
    {
        $this->relationService = $relationService;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     */
    public function setEntity(DataClassEntity $entity)
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

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    private function getAvailableSchemas()
    {
        if (! isset($this->availableSchemas))
        {
            $this->availableSchemas = $this->getEntityService()->getAvailableSchemasForEntityType(
                $this->getRelationService(), 
                $this->getEntity())->as_array();
        }
        
        return $this->availableSchemas;
    }

    public function addElements()
    {
        $availableSchemas = $this->getAvailableSchemas();
        $tabs_generator = new DynamicFormTabsRenderer('ProviderLinks', $this->getFormValidator());
        
        foreach ($availableSchemas as $availableSchema)
        {
            $tabs_generator->add_tab(
                new DynamicFormTab(
                    'schema-' . $availableSchema->get_id(), 
                    $availableSchema->get_name(), 
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'Tab/' . self::TAB_METADATA), 
                    array($this, 'addElementsForSchema'), 
                    array($availableSchema)));
        }
        
        $tabs_generator->render();
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     */
    public function addElementsForSchema(Schema $schema)
    {
        $elements = $this->getElementsForSchema($schema);
        
        foreach ($elements as $element)
        {
            $this->getFormValidator()->addElement(
                'select', 
                $this->getElementName($schema, $element), 
                $element->get_display_name(), 
                $this->getElementOptions());
        }
    }

    private function getElementsForSchema($schema)
    {
        if (! isset($this->schemaElements[$schema->get_id()]))
        {
            $this->schemaElements[$schema->get_id()] = $this->getElementService()->getElementsForSchema($schema)->as_array();
        }
        
        return $this->schemaElements[$schema->get_id()];
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     */
    private function getElementName(Schema $schema, Element $element)
    {
        if (! isset($this->elementNames[$schema->get_id()][$element->get_id()]))
        {
            $this->elementNames[$schema->get_id()][$element->get_id()] = EntityService::PROPERTY_METADATA_SCHEMA . '[' .
                 $schema->get_id() . '][' . $element->get_id() . ']';
        }
        
        return $this->elementNames[$schema->get_id()][$element->get_id()];
    }

    private function getElementOptions()
    {
        if (! isset($this->elementOptions))
        {
            $this->elementOptions[0] = Translation::get('NoProvidedValue', null, 'Chamilo\Core\Metadata\Provider');
            
            $propertyProviderService = new PropertyProviderService($this->getEntity());
            $providerRegistrations = $propertyProviderService->getProviderRegistrationsForEntity();
            
            while ($providerRegistration = $providerRegistrations->next_result())
            {
                $translationNamespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                    $providerRegistration->get_provider_class(), 
                    2);
                $translationVariable = StringUtilities::getInstance()->createString(
                    $providerRegistration->get_property_name())->upperCamelize();
                
                $this->elementOptions[$providerRegistration->get_id()] = Translation::get(
                    $translationVariable, 
                    null, 
                    $translationNamespace);
            }
        }
        
        return $this->elementOptions;
    }

    public function setDefaults()
    {
        $defaults = array();
        $availableSchemas = $this->getAvailableSchemas();
        
        foreach ($availableSchemas as $availableSchema)
        {
            $defaults = array_merge($defaults, $this->getDefaultsForSchema($availableSchema));
        }
        
        $this->getFormValidator()->setDefaults($defaults);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     */
    public function getDefaultsForSchema(Schema $schema)
    {
        $defaults = array();
        $elements = $this->getElementsForSchema($schema);
        
        foreach ($elements as $element)
        {
            $providerLink = $this->getElementProviderLink($element);
            
            if ($providerLink instanceof ProviderLink)
            {
                $defaults[$this->getElementName($schema, $element)] = $providerLink->get_provider_registration_id();
            }
        }
        
        return $defaults;
    }

    /**
     *
     * @param Element $element
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink
     */
    private function getElementProviderLink(Element $element)
    {
        $entityProviderLinks = $this->getEntityProviderLinks();
        return $entityProviderLinks[$element->get_id()];
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink[]
     */
    private function getEntityProviderLinks()
    {
        if (! isset($this->entityProviderLinks))
        {
            $propertyProviderService = new PropertyProviderService($this->getEntity());
            $entityProviderLinks = $propertyProviderService->getProviderLinksForEntity();
            
            while ($entityProviderLink = $entityProviderLinks->next_result())
            {
                $this->entityProviderLinks[$entityProviderLink->get_element_id()] = $entityProviderLink;
            }
        }
        
        return $this->entityProviderLinks;
    }
}
