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
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     */
    public function __construct(DataClassEntity $entity)
    {
        $this->entity = $entity;
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
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getPropertyValues(Element $element)
    {
        $providerLink = $this->getProviderLinkForElement($element);
        $providerRegistration = $providerLink->getProviderRegistration();
        $provider = $this->getPropertyProviderFromRegistration($providerRegistration);

        return $provider->renderProperty($providerRegistration->get_property_name(), $this->getEntity()->getDataClass());
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getProviderLinkForElement(Element $element)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink :: class_name(), ProviderLink :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->getEntity()->getDataClassName()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink :: class_name(), ProviderLink :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));

        $condition = new AndCondition($conditions);

        $providerLink = DataManager :: retrieve(
            ProviderLink :: class_name(),
            new DataClassRetrieveParameters($condition));

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
     *
     * @param Registration $registration
     * @return \Chamilo\Core\Metadata\Provider\PropertyProviderInterface
     */
    public function getPropertyProviderFromRegistration(ProviderRegistration $registration)
    {
        $className = $registration->get_provider_class();
        return new $className();
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getProviderRegistrationsForEntity()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ProviderRegistration :: class_name(),
                ProviderRegistration :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->getEntity()->getDataClassName()));

        $parameters = new DataClassRetrievesParameters($condition);
        return DataManager :: retrieves(ProviderRegistration :: class_name(), $parameters);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getProviderLinksForEntity()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink :: class_name(), ProviderLink :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->getEntity()->getDataClassName()));

        $parameters = new DataClassRetrievesParameters($condition);
        return DataManager :: retrieves(ProviderLink :: class_name(), $parameters);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationServic $relationService
     * @param string[] $submittedProviderLinkValues
     * @return boolean
     */
    public function updateEntityProviderLinks(EntityService $entityService, ElementService $elementService,
        RelationService $relationService, $submittedProviderLinkValues)
    {
        $availableSchemas = $entityService->getAvailableSchemasForEntityType($relationService, $this->getEntity())->as_array();

        foreach ($availableSchemas as $availableSchema)
        {
            if (isset($submittedProviderLinkValues[$availableSchema->get_id()]))
            {
                if (! $this->updateEntityProviderLinksForSchema(
                    $elementService,
                    $availableSchema,
                    $submittedProviderLinkValues[$availableSchema->get_id()]))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param string[] $submittedSchemaValues
     */
    public function updateEntityProviderLinksForSchema(ElementService $elementService, Schema $schema,
        $submittedSchemaValues)
    {
        $elements = $elementService->getElementsForSchema($schema);

        while ($element = $elements->next_result())
        {
            if (isset($submittedSchemaValues[$element->get_id()]))
            {
                if (! $this->updateEntityProviderLinkForElement($element, $submittedSchemaValues[$element->get_id()]))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function updateEntityProviderLinkForElement(Element $element, $submittedProviderRegistrationId)
    {
        if ($submittedProviderRegistrationId)
        {
            try
            {
                $providerLink = $this->getProviderLinkForElement($element);
                $providerLink->set_provider_registration_id($submittedProviderRegistrationId);
                return $providerLink->update();
            }
            catch (NoProviderAvailableException $exception)
            {
                $providerLink = new ProviderLink();
                $providerLink->set_entity_type($this->getEntity()->getDataClassName());
                $providerLink->set_element_id($element->get_id());
                $providerLink->set_provider_registration_id($submittedProviderRegistrationId);

                return $providerLink->create();
            }
        }
        else
        {
            try
            {
                $providerLink = $this->getProviderLinkForElement($element);
                return $providerLink->delete();
            }
            catch (NoProviderAvailableException $exception)
            {
                return true;
            }
        }
    }
}