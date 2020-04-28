<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertyProviderService
{

    /**
     * @var \Chamilo\Core\Metadata\Service\EntityService
     */
    private $entityService;

    /**
     * @var \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    private $elementService;

    /**
     * @var \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    private $relationService;

    /**
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     */
    public function __construct(
        EntityService $entityService, ElementService $elementService, RelationService $relationService
    )
    {
        $this->entityService = $entityService;
        $this->elementService = $elementService;
        $this->relationService = $relationService;
    }

    /**
     * @return \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    public function getElementService(): ElementService
    {
        return $this->elementService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function setElementService(ElementService $elementService): void
    {
        $this->elementService = $elementService;
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityService
     */
    public function getEntityService(): EntityService
    {
        return $this->entityService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     */
    public function setEntityService(EntityService $entityService): void
    {
        $this->entityService = $entityService;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration $registration
     *
     * @return \Chamilo\Core\Metadata\Provider\PropertyProviderInterface
     */
    public function getPropertyProviderFromRegistration(ProviderRegistration $registration)
    {
        $className = $registration->get_provider_class();

        return new $className();
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return string
     * @throws \Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getPropertyValues(DataClassEntity $entity, Element $element)
    {
        $providerLink = $this->getProviderLinkForElement($entity, $element);
        $providerRegistration = $providerLink->getProviderRegistration();
        $provider = $this->getPropertyProviderFromRegistration($providerRegistration);

        return $provider->renderProperty(
            $providerRegistration->get_property_name(), $entity->getDataClass()
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink
     * @throws \Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getProviderLinkForElement(DataClassEntity $entity, Element $element)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink::class, ProviderLink::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity->getDataClassName())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink::class, ProviderLink::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId())
        );

        $condition = new AndCondition($conditions);

        $providerLink = DataManager::retrieve(ProviderLink::class, new DataClassRetrieveParameters($condition));

        if ($providerLink instanceof ProviderLink)
        {
            return $providerLink;
        }
        else
        {
            throw new NoProviderAvailableException();
        }
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getProviderLinksForEntity(DataClassEntity $entity)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink::class, ProviderLink::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity->getDataClassName())
        );

        $parameters = new DataClassRetrievesParameters($condition);

        return DataManager::retrieves(ProviderLink::class, $parameters);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration[]
     * @throws \Exception
     */
    public function getProviderRegistrationsForEntity(DataClassEntity $entity)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ProviderRegistration::class, ProviderRegistration::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entity->getDataClassName())
        );

        $parameters = new DataClassRetrievesParameters($condition);

        return DataManager::retrieves(ProviderRegistration::class, $parameters);
    }

    /**
     * @return \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    public function getRelationService(): RelationService
    {
        return $this->relationService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     */
    public function setRelationService(RelationService $relationService): void
    {
        $this->relationService = $relationService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param $submittedProviderRegistrationId
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updateEntityProviderLinkForElement(
        DataClassEntity $entity, Element $element, $submittedProviderRegistrationId
    )
    {
        if ($submittedProviderRegistrationId)
        {
            try
            {
                $providerLink = $this->getProviderLinkForElement($entity, $element);
                $providerLink->set_provider_registration_id($submittedProviderRegistrationId);

                return $providerLink->update();
            }
            catch (NoProviderAvailableException $exception)
            {
                $providerLink = new ProviderLink();
                $providerLink->set_entity_type($entity->getDataClassName());
                $providerLink->set_element_id($element->getId());
                $providerLink->set_provider_registration_id($submittedProviderRegistrationId);

                return $providerLink->create();
            }
        }
        else
        {
            try
            {
                $providerLink = $this->getProviderLinkForElement($entity, $element);

                return $providerLink->delete();
            }
            catch (NoProviderAvailableException $exception)
            {
                return true;
            }
        }
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $submittedProviderLinkValues
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function updateEntityProviderLinks(DataClassEntity $entity, $submittedProviderLinkValues)
    {
        $availableSchemas = $this->getEntityService()->getAvailableSchemasForEntityType($entity)->as_array();

        foreach ($availableSchemas as $availableSchema)
        {
            if (isset($submittedProviderLinkValues[$availableSchema->get_id()]))
            {
                if (!$this->updateEntityProviderLinksForSchema(
                    $entity, $availableSchema, $submittedProviderLinkValues[$availableSchema->get_id()]
                ))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param $submittedSchemaValues
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updateEntityProviderLinksForSchema(DataClassEntity $entity, Schema $schema, $submittedSchemaValues)
    {
        $elements = $this->getElementService()->getElementsForSchema($schema);

        while ($element = $elements->next_result())
        {
            if (isset($submittedSchemaValues[$element->get_id()]))
            {
                if (!$this->updateEntityProviderLinkForElement(
                    $entity, $element, $submittedSchemaValues[$element->get_id()]
                ))
                {
                    return false;
                }
            }
        }

        return true;
    }
}