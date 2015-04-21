<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration;
use Chamilo\Core\Metadata\Entity\DataClassEntity;

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
     * @var \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance
     */
    private $schemaInstance;

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     */
    public function __construct(DataClassEntity $entity, SchemaInstance $schemaInstance)
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
     * @return \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance
     */
    public function getSchemaInstance()
    {
        return $this->schemaInstance;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     */
    public function setSchemaInstance($schemaInstance)
    {
        $this->schemaInstance = $schemaInstance;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getPropertyValues(Element $element)
    {
        $providerLink = $this->getProviderLink($element);
        $providerRegistration = $providerLink->getProviderRegistration();
        $provider = $this->getPropertyProviderFromRegistration($providerRegistration);

        return $provider->renderProperty($providerRegistration->get_property_name(), $this->getEntity()->getDataClass());
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getProviderLink(Element $element)
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
}