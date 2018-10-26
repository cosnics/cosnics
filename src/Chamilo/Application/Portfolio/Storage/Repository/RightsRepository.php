<?php
namespace Chamilo\Application\Portfolio\Storage\Repository;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     *
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight $locationEntityRight
     * @return boolean
     */
    public function createRightsLocationEntityRight(RightsLocationEntityRight $locationEntityRight)
    {
        return $this->getDataClassRepository()->create($locationEntityRight);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation $location
     * @param integer[] $rights
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRightsForLocationAndRights(RightsLocation $location, $rights)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location->get_node_id()));
        $conditions[] = new InCondition(
            new PropertyConditionVariable(RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_RIGHT_ID),
            $rights);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($location->get_publication_id()));

        $condition = new AndCondition($conditions);

        $order = new OrderBy(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
            SORT_ASC);

        return $this->getDataClassRepository()->retrieves(
            RightsLocationEntityRight::class,
            new DataClassRetrievesParameters($condition, null, null, $order));
    }

    /**
     *
     * @param integer $right
     * @param integer $entityId
     * @param integer $entityType
     * @param string $locationId
     * @param integer $publicationId
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight
     */
    public function findRightsLocationEntityRight($right, $entityId, $entityType, $locationId, $publicationId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($locationId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationId));

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            RightsLocationEntityRight::class,
            new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation $location
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $entitiesCondition
     * @return string[]
     */
    public function findRightsLocationEntityRightsRecordsForLocation(RightsLocation $location,
        Condition $entitiesCondition)
    {
        $conditions = array();

        $conditions[] = $entitiesCondition;
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location->get_node_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class,
                RightsLocationEntityRight::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($location->get_publication_id()));

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->records(
            RightsLocationEntityRight::class,
            new RecordRetrievesParameters(
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            RightsLocationEntityRight::class,
                            RightsLocationEntityRight::PROPERTY_RIGHT_ID))),
                $condition));
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $nodeIdentifier
     * @return string[]
     */
    public function findRightsLocationForPublicationIdentifierAndNodeIdentifier($publicationIdentifier, $nodeIdentifier)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_NODE_ID),
            new StaticConditionVariable($nodeIdentifier));
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->record(
            RightsLocation::class,
            new RecordRetrieveParameters(
                new DataClassProperties(
                    array(new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_INHERIT))),
                $condition));
    }
}

