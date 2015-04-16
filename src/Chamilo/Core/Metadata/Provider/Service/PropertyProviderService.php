<?php
namespace Chamilo\Core\Metadata\Provider\Service;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Metadata\Provider\Storage\DataClass\Link;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Core\Metadata\Provider\Storage\DataClass\Registration;

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
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @var \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance
     */
    private $schemaInstance;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     */
    public function __construct(DataClass $entity, SchemaInstance $schemaInstance)
    {
        $this->entity = $entity;
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

        return $provider->renderProperty($providerRegistration->get_property_name(), $this->getEntity());
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getProviderLink(Element $element)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Link :: class_name(), Link :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->getEntity()->class_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Link :: class_name(), Link :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));

        $condition = new AndCondition($conditions);

        $providerLink = DataManager :: retrieve(Link :: class_name(), new DataClassRetrieveParameters($condition));

        if ($providerLink instanceof Link)
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
    public function getPropertyProviderFromRegistration(Registration $registration)
    {
        $className = $registration->get_provider_class();
        return new $className();
    }
}