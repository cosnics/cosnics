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
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     * @param string $dataClass
     * @param string $typeProperty
     * @param string $identifierProperty
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
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
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getEntityCondition($entity, $dataClass, $typeProperty, $identifierProperty = null)
    {
        $entityConditions = array();
        
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable($dataClass::class_name(), $typeProperty), 
            new StaticConditionVariable($entity->getDataClassName()));
        
        if (! $entity->isDataClassType() && $identifierProperty)
        {
            $entityConditions[] = new EqualityCondition(
                new PropertyConditionVariable($dataClass::class_name(), $identifierProperty), 
                new StaticConditionVariable($entity->getDataClassIdentifier()));
        }
        
        return new AndCondition($entityConditions);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity[]
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
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     */
    public function expandEntity($entity)
    {
        $expandedEntities = array();
        
        if (! $entity->isDataClassType())
        {
            $expandedEntities[] = $entity;
        }
        else
        {
            $dataClassInstances = DataManager::retrieves(
                $entity->getDataClassName(), 
                new DataClassRetrievesParameters());
            
            while ($dataClassInstance = $dataClassInstances->next_result())
            {
                $expandedEntities[] = DataClassEntityFactory::getInstance()->getEntityFromDataClass($dataClassInstance);
            }
        }
        
        return $expandedEntities;
    }
}
