<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTab;
use Chamilo\Libraries\Format\Tabs\Form\FormTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
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
     * @var \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    private $propertyProviderService;

    /**
     *
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function __construct(
        EntityService $entityService, ElementService $elementService, RelationService $relationService,
        PropertyProviderService $propertyProviderService
    )
    {
        $this->entityService = $entityService;
        $this->elementService = $elementService;
        $this->relationService = $relationService;
        $this->propertyProviderService = $propertyProviderService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function addElements(DataClassEntity $entity, FormValidator $formValidator)
    {
        $availableSchemas = $this->getAvailableSchemas($entity);
        $tabs_generator = new FormTabsRenderer('ProviderLinks', $formValidator);

        foreach ($availableSchemas as $availableSchema)
        {
            $tabs_generator->addTab(
                new FormTab(
                    'schema-' . $availableSchema->get_id(), $availableSchema->get_name(),
                    new FontAwesomeGlyph('info-circle', array('fa-lg'), null, 'fas'),
                    array($this, 'addElementsForSchema'), array($entity, $formValidator, $availableSchema)
                )
            );
        }

        $tabs_generator->render();
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     *
     * @throws \Exception
     */
    public function addElementsForSchema(DataClassEntity $entity, FormValidator $formValidator, Schema $schema)
    {
        $elements = $this->getElementsForSchema($schema);

        foreach ($elements as $element)
        {
            $formValidator->addElement(
                'select', $this->getElementName($schema, $element), $element->get_display_name(),
                $this->getElementOptions($entity)
            );
        }
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Schema[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    private function getAvailableSchemas(DataClassEntity $entity)
    {
        return $this->getEntityService()->getAvailableSchemasForEntityType($entity);
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     *
     * @return integer[]
     * @throws \Exception
     */
    public function getDefaultsForSchema(DataClassEntity $entity, Schema $schema)
    {
        $defaults = [];
        $elements = $this->getElementsForSchema($schema);

        foreach ($elements as $element)
        {
            $providerLink = $this->getElementProviderLink($entity, $element);

            if ($providerLink instanceof ProviderLink)
            {
                $defaults[$this->getElementName($schema, $element)] = $providerLink->get_provider_registration_id();
            }
        }

        return $defaults;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return string
     */
    private function getElementName(Schema $schema, Element $element)
    {
        return EntityService::PROPERTY_METADATA_SCHEMA . '[' . $schema->getId() . '][' . $element->getId() . ']';
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return string[]
     * @throws \Exception
     */
    private function getElementOptions(DataClassEntity $entity)
    {
        $elementOptions = [];

        $elementOptions[0] = Translation::get('NoProvidedValue', null, 'Chamilo\Core\Metadata\Provider');

        $providerRegistrations = $this->getPropertyProviderService()->getProviderRegistrationsForEntity($entity);

        foreach($providerRegistrations as $providerRegistration)
        {
            $translationNamespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $providerRegistration->get_provider_class(), 2
            );
            $translationVariable = StringUtilities::getInstance()->createString(
                $providerRegistration->get_property_name()
            )->upperCamelize();

            $elementOptions[$providerRegistration->get_id()] = Translation::get(
                $translationVariable, null, $translationNamespace
            );
        }

        return $elementOptions;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink
     * @throws \Exception
     */
    private function getElementProviderLink(DataClassEntity $entity, Element $element)
    {
        $entityProviderLinks = $this->getEntityProviderLinks($entity);

        return $entityProviderLinks[$element->getId()];
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
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element
     * @throws \Exception
     */
    private function getElementsForSchema(Schema $schema)
    {
        return $this->getElementService()->getElementsForSchema($schema);
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink[]
     * @throws \Exception
     */
    private function getEntityProviderLinks(DataClassEntity $entity)
    {
        $entityProviderLinks = [];

        $entityProviderLinksResultSet = $this->getPropertyProviderService()->getProviderLinksForEntity($entity);

        foreach($entityProviderLinksResultSet as $entityProviderLink)
        {
            $entityProviderLinks[$entityProviderLink->get_element_id()] = $entityProviderLink;
        }

        return $entityProviderLinks;
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
     * @return \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    public function getPropertyProviderService(): PropertyProviderService
    {
        return $this->propertyProviderService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function setPropertyProviderService(PropertyProviderService $propertyProviderService): void
    {
        $this->propertyProviderService = $propertyProviderService;
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
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Exception
     */
    public function setDefaults(DataClassEntity $entity, FormValidator $formValidator)
    {
        $defaults = [];
        $availableSchemas = $this->getAvailableSchemas($entity);

        foreach ($availableSchemas as $availableSchema)
        {
            $defaults = array_merge($defaults, $this->getDefaultsForSchema($entity, $availableSchema));
        }

        $formValidator->setDefaults($defaults);
    }
}
