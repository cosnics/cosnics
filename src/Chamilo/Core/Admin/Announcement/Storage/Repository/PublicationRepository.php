<?php
namespace Chamilo\Core\Admin\Announcement\Storage\Repository;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
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

    public function countPublications(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(Publication::class, new DataClassCountParameters($condition));
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return integer
     */
    public function countPublicationsForContentObjectIdentifiers(array $contentObjectIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIdentifiers
        );

        return $this->countPublications($condition);
    }

    /**
     * @param $condition
     * @param integer[] $publicationIdentifiers
     *
     * @return integer
     */
    public function countVisiblePublicationsForPublicationIdentifiers(
        $condition, array $publicationIdentifiers
    )
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ID), $publicationIdentifiers
        );

        $conditions[] = $this->getTimeConditions();

        return $this->getDataClassRepository()->count(
            Publication::class, new DataClassCountParameters(new AndCondition($conditions))
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getTimeConditions()
    {
        $fromDateVariables = new PropertyConditionVariable(
            Publication::class, Publication::PROPERTY_FROM_DATE
        );

        $toDateVariable = new PropertyConditionVariable(
            Publication::class, Publication::PROPERTY_TO_DATE
        );

        $timeConditions = [];
        $timeConditions[] = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_HIDDEN),
            new StaticConditionVariable(0)
        );

        $foreverConditions = [];
        $foreverConditions[] = new EqualityCondition($fromDateVariables, new StaticConditionVariable(0));
        $foreverConditions[] = new EqualityCondition($toDateVariable, new StaticConditionVariable(0));
        $foreverCondition = new AndCondition($foreverConditions);

        $betweenConditions = [];
        $betweenConditions[] = new ComparisonCondition(
            $fromDateVariables, ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );
        $betweenConditions[] = new ComparisonCondition(
            $toDateVariable, ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );
        $betweenCondition = new AndCondition($betweenConditions);

        $timeConditions[] = new OrCondition(array($foreverCondition, $betweenCondition));

        return new AndCondition($timeConditions);
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationsForTypeAndIdentifier(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier, Condition $condition = null
    )
    {
        switch ($type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publicationCondition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
                    new StaticConditionVariable($objectIdentifier)
                );
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publicationCondition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($objectIdentifier)
                );
                break;
            default :
                return 0;
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publicationCondition));
        }
        else
        {
            $condition = $publicationCondition;
        }

        return $this->countPublications($condition);
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function createPublication(Publication $publication)
    {
        return $this->getDataClassRepository()->create($publication);
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublication(Publication $publication)
    {
        return $this->getDataClassRepository()->delete($publication);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier(int $publicationIdentifier)
    {
        return $this->getDataClassRepository()->retrieveById(Publication::class, $publicationIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return string[]
     * @throws \Exception
     */
    public function findPublicationRecordByIdentifier(int $publicationIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ID),
            new StaticConditionVariable($publicationIdentifier)
        );

        return $this->getDataClassRepository()->record(
            Publication::class, new RecordRetrieveParameters(
                new DataClassProperties(new PropertiesConditionVariable(Publication::class)), $condition
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return string[]
     * @throws \Exception
     */
    public function findPublicationRecords(
        Condition $condition = null, int $count = null, int $offset = null, array $orderProperties = null
    )
    {
        $data_class_properties = [];

        $data_class_properties[] = new PropertiesConditionVariable(Publication::class);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TITLE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TYPE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_CURRENT
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_OWNER_ID
        );

        $properties = new DataClassProperties($data_class_properties);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, $count, $offset, $orderProperties, $this->getContentObjectPublicationJoins()
        );

        return $this->getDataClassRepository()->records(Publication::class, $parameters);
    }

    /**
     * @param integer[] $publicationIdentifiers
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return string[]
     * @throws \Exception
     */
    public function findVisiblePublicationRecordsForPublicationIdentifiers(
        array $publicationIdentifiers, Condition $condition = null, int $count = null, int $offset = null,
        array $orderProperties = null
    )
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ID), $publicationIdentifiers
        );

        $conditions[] = $this->getTimeConditions();

        return $this->findPublicationRecords(new AndCondition($conditions), $count, $offset, $orderProperties);
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return string[]
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        $type = PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier,
        Condition $condition = null, int $count = null, int $offset = null, array $orderProperties = null
    )
    {
        switch ($type)
        {
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT :
                $publicationCondition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
                    new StaticConditionVariable($objectIdentifier)
                );
                break;
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER :
                $publicationCondition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($objectIdentifier)
                );
                break;
            default :
                return [];
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publicationCondition));
        }
        else
        {
            $condition = $publicationCondition;
        }

        return $this->findPublicationRecords($condition, $count, $offset, $orderProperties);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication[]
     * @throws \Exception
     */
    public function findPublications(
        Condition $condition = null, int $count = null, int $offset = null, array $orderProperties = null
    )
    {
        return $this->getDataClassRepository()->retrieves(
            Publication::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderProperties)
        );
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication[]
     */
    public function findPublicationsForContentObjectIdentifier(int $contentObjectIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObjectIdentifier)
        );

        return $this->findPublications($condition);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getContentObjectPublicationJoins()
    {
        $joins = [];

        $joins[] = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        return new Joins($joins);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository): void
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getDataClassRepository()->update($publication);
    }
}

