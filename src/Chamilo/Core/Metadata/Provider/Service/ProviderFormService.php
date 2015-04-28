<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
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

    private $elementOptions;

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

    public function addElements()
    {
        $availableSchemas = $this->getEntityService()->getAvailableSchemasForEntityType(
            $this->getRelationService(),
            $this->getEntity());

        $tabs_generator = new DynamicFormTabsRenderer('ProviderLinks', $this->getFormValidator());

        while ($availableSchema = $availableSchemas->next_result())
        {
            $tabs_generator->add_tab(
                new DynamicFormTab(
                    'schema-' . $availableSchema->get_id(),
                    $availableSchema->get_name(),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'Tab/' . self :: TAB_METADATA),
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
        $elements = $this->getElementService()->getElementsForSchema($schema);

        while ($element = $elements->next_result())
        {
            $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' . $schema->get_id() . '][' .
                 $element->get_id() . ']';

            $this->getFormValidator()->addElement(
                'select',
                $elementName,
                $element->get_display_name(),
                $this->getElementOptions());
        }
    }

    public function getElementOptions()
    {
        if (! isset($this->elementOptions))
        {
            $this->elementOptions[0] = Translation :: get('NoProvidedValue', null, 'Chamilo\Core\Metadata\Provider');

            $propertyProviderService = new PropertyProviderService($this->getEntity());
            $providerRegistrations = $propertyProviderService->getProviderRegistrationsForEntity();

            while ($providerRegistration = $providerRegistrations->next_result())
            {
                $translationNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $providerRegistration->get_provider_class(),
                    2);
                $translationVariable = StringUtilities :: getInstance()->createString(
                    $providerRegistration->get_property_name())->upperCamelize();

                $this->elementOptions[$providerRegistration->get_id()] = Translation :: get(
                    $translationVariable,
                    null,
                    $translationNamespace);
            }
        }

        return $this->elementOptions;
    }

    public function setDefaults()
    {
        // $defaults = array();

        // $elementService = new ElementService();
        // $elements = $elementService->getElementsForSchemaInstance($this->getSchemaInstance());

        // $vocabularyService = new VocabularyService();
        // $propertyProviderService = new PropertyProviderService($this->getEntity(), $this->getSchemaInstance());

        // while ($element = $elements->next_result())
        // {
        // try
        // {
        // $providerLink = $propertyProviderService->getProviderLink($element);
        // continue;
        // }
        // catch (NoProviderAvailableException $exception)
        // {
        // if ($element->usesVocabulary())
        // {
        // $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' .
        // $this->getSchemaInstance()->get_schema_id() . '][' . $this->getSchemaInstance()->get_id() . '][' .
        // $element->get_id() . '][' . EntityService :: PROPERTY_METADATA_SCHEMA_EXISTING . ']';

        // $options = array();

        // $elementInstanceVocabularies = $elementService->getElementInstanceVocabulariesForSchemaInstanceAndElement(
        // $this->getSchemaInstance(),
        // $element)->as_array();

        // if (count($elementInstanceVocabularies) == 0)
        // {
        // $elementInstanceVocabularies = $vocabularyService->getDefaultVocabulariesForUserEntitySchemaInstanceElement(
        // $this->getUser(),
        // $this->getSchemaInstance(),
        // $element);
        // }

        // if (count($elementInstanceVocabularies) > 0)
        // {
        // foreach ($elementInstanceVocabularies as $elementInstanceVocabulary)
        // {
        // $item = new \stdClass();
        // $item->id = $elementInstanceVocabulary->get_id();
        // $item->value = $elementInstanceVocabulary->get_value();

        // $options[] = $item;
        // }
        // }

        // $elementValue = json_encode($options);
        // }
        // else
        // {
        // $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' .
        // $this->getSchemaInstance()->get_schema_id() . '][' . $this->getSchemaInstance()->get_id() . '][' .
        // $element->get_id() . ']';
        // $elementInstanceVocabulary = $elementService->getElementInstanceVocabularyForSchemaInstanceAndElement(
        // $this->getSchemaInstance(),
        // $element);

        // if ($elementInstanceVocabulary instanceof Vocabulary && $elementInstanceVocabulary->get_value())
        // {
        // $elementValue = $elementInstanceVocabulary->get_value();
        // }
        // else
        // {
        // $elementValue = '';
        // }
        // }

        // $defaults[$elementName] = $elementValue;
        // }
        // }

        // $this->formValidator->setDefaults($defaults);
    }
}
