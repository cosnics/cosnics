<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityConditionService
{

    /**
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    private $dataClassEntityFactory;

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntityFactory $dataClassEntityFactory
     */
    public function __construct(DataClassEntityFactory $dataClassEntityFactory)
    {
        $this->dataClassEntityFactory = $dataClassEntityFactory;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     * @throws \Exception
     */
    public function expandEntities($entities)
    {
        $expandedEntities = array();

        foreach ($entities as $entity)
        {

            $expandedEntities = array_merge($expandedEntities, $this->expandEntity($entity));
        }

        return $expandedEntities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     * @throws \Exception
     */
    public function expandEntity($entity)
    {
        $expandedEntities = array();

        if (!$entity->isDataClassType())
        {
            $expandedEntities[] = $entity;
        }
        else
        {
            $dataClassInstances = DataManager::retrieves(
                $entity->getDataClassName(), new DataClassRetrievesParameters()
            );

            while ($dataClassInstance = $dataClassInstances->next_result())
            {
                $expandedEntities[] = $this->getDataClassEntityFactory()->getEntityFromDataClass($dataClassInstance);
            }
        }

        return $expandedEntities;
    }

    /**
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public function getDataClassEntityFactory(): DataClassEntityFactory
    {
        return $this->dataClassEntityFactory;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntityFactory $dataClassEntityFactory
     */
    public function setDataClassEntityFactory(DataClassEntityFactory $dataClassEntityFactory): void
    {
        $this->dataClassEntityFactory = $dataClassEntityFactory;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     * @param string $dataClass
     * @param string $typeProperty
     * @param string $identifierProperty
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     * @throws \Exception
     */
    public function getEntitiesCondition($entities, $dataClass, $typeProperty, $identifierProperty = null)
    {
        $entityConditions = array();

        foreach ($entities as $entity)
        {
            $entityConditions[] = $this->getEntityCondition($entity, $dataClass, $typeProperty, $identifierProperty);
        }

        return new OrCondition($entityConditions);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string $dataClass
     * @param string $typeProperty
     * @param string $identifierProperty
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     * @throws \Exception
     */
    public function getEntityCondition($entity, $dataClass, $typeProperty, $identifierProperty = null)
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable($dataClass, $typeProperty),
            new StaticConditionVariable($entity->getDataClassName())
        );

        if (!$entity->isDataClassType() && $identifierProperty)
        {
            $entityConditions[] = new EqualityCondition(
                new PropertyConditionVariable($dataClass, $identifierProperty),
                new StaticConditionVariable($entity->getDataClassIdentifier())
            );
        }

        return new AndCondition($entityConditions);
    }
}
