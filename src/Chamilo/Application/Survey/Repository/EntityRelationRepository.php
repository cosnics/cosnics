<?php
namespace Chamilo\Application\Survey\Repository;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Survey\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\OrCondition[]
     */
    private static $entitiesConditions = array();

    /**
     *
     * @param integer[] $entities
     * @param integer $right
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function findEntitiesWithRight($entities, $right, Publication $publication)
    {
        $entityRelationConditions = array();

        $entityRelationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationEntityRelation :: class_name(),
                PublicationEntityRelation :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication->getId()));

        $entityRelationConditions[] = new EqualityCondition(
            new OperationConditionVariable(
                new PropertyConditionVariable(
                    PublicationEntityRelation :: class_name(),
                    PublicationEntityRelation :: PROPERTY_RIGHTS),
                OperationConditionVariable :: BITWISE_AND,
                new StaticConditionVariable($right)),
            new StaticConditionVariable($right));

        $entityRelationConditions[] = $this->getEntitiesCondition($entities);

        $entityRelationCondition = new AndCondition($entityRelationConditions);

        return DataManager :: count(
            PublicationEntityRelation :: class_name(),
            new DataClassCountParameters($entityRelationCondition)) > 0;
    }

    /**
     *
     * @param integer[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getEntitiesCondition($entities)
    {
        $entitiesHash = md5(serialize($entities));

        if (! isset(self :: $entitiesConditions[$entitiesHash]))
        {
            $entityTypeConditions = array();

            foreach ($entities as $entityType => $entityIdentifiers)
            {
                foreach ($entityIdentifiers as $entityIdentifier)
                {

                    $entityConditions = array();

                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            PublicationEntityRelation :: class_name(),
                            PublicationEntityRelation :: PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($entityType));
                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            PublicationEntityRelation :: class_name(),
                            PublicationEntityRelation :: PROPERTY_ENTITY_ID),
                        new StaticConditionVariable($entityIdentifier));

                    $entityTypeConditions[] = new AndCondition($entityConditions);
                }
            }

            self :: $entitiesConditions[$entitiesHash] = new OrCondition($entityTypeConditions);
        }

        return self :: $entitiesConditions[$entitiesHash];
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
     */
    public function findEntityRelationForPublicationEntityTypeAndIdentifier(Publication $publication, $entityType,
        $entityIdentifier)
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationEntityRelation :: class_name(),
                PublicationEntityRelation :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication->getId()));

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationEntityRelation :: class_name(),
                PublicationEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType));
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationEntityRelation :: class_name(),
                PublicationEntityRelation :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityIdentifier));

        $entityCondition = new AndCondition($entityConditions);

        return DataManager :: retrieve(
            PublicationEntityRelation :: class_name(),
            new DataClassRetrieveParameters($entityCondition));
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
     */
    public function findEntityRelationByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(PublicationEntityRelation :: class_name(), $identifier);
    }
}