<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;

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
     * @param string $dataClass
     * @param string $typeProperty
     * @param string $identifierProperty
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    public function getRelationInstanceConditionForEntities($dataClass, $typeProperty, $identifierProperty, $entities)
    {
        $entityConditions = array();

        foreach ($entities as $entity)
        {
            $entityConditions[] = $this->getRelationInstanceConditionForEntity(
                $dataClass,
                $typeProperty,
                $identifierProperty,
                $entity);
        }

        return new OrCondition($entityConditions);
    }

    /**
     *
     * @param string $dataClass
     * @param string $typeProperty
     * @param string $identifierProperty
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getRelationInstanceConditionForEntity($dataClass, $typeProperty, $identifierProperty, $entity)
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable($dataClass :: class_name(), $typeProperty),
            new StaticConditionVariable($entity->getDataClassName()));

        if (! $entity->isDataClassType())
        {
            $entityConditions[] = new EqualityCondition(
                new PropertyConditionVariable($dataClass :: class_name(), $identifierProperty),
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
            $dataClassInstances = DataManager :: retrieves($entity->getDataClassName());

            while ($dataClassInstance = $dataClassInstances->next_result())
            {
                $expandedEntities[] = DataClassEntityFactory :: getInstance()->getEntityFromDataClass(
                    $dataClassInstance);
            }
        }

        return $expandedEntities;
    }
}
