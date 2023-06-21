<?php
namespace Chamilo\Core\Admin\Announcement\Storage\Repository;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
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
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countPublications(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(Publication::class, new DataClassCountParameters($condition));
    }

    /**
     * @param string[] $contentObjectIdentifiers
     */
    public function countPublicationsForContentObjectIdentifiers(array $contentObjectIdentifiers): int
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIdentifiers
        );

        return $this->countPublications($condition);
    }

    public function countPublicationsForTypeAndIdentifier(
        int $type, string $objectIdentifier, ?Condition $condition = null
    ): int
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
                return 0;
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $publicationCondition]);
        }
        else
        {
            $condition = $publicationCondition;
        }

        return $this->countPublications($condition);
    }

    /**
     * @param string[] $publicationIdentifiers
     */
    public function countVisiblePublicationsForPublicationIdentifiers(
        array $publicationIdentifiers, ?Condition $condition = null
    ): int
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Publication::class, DataClass::PROPERTY_ID), $publicationIdentifiers
        );

        $conditions[] = $this->getTimeConditions();

        return $this->getDataClassRepository()->count(
            Publication::class, new DataClassCountParameters(new AndCondition($conditions))
        );
    }

    public function createPublication(Publication $publication): bool
    {
        return $this->getDataClassRepository()->create($publication);
    }

    public function deletePublication(Publication $publication): bool
    {
        return $this->getDataClassRepository()->delete($publication);
    }

    public function findPublicationByIdentifier(string $publicationIdentifier): ?Publication
    {
        return $this->getDataClassRepository()->retrieveById(Publication::class, $publicationIdentifier);
    }

    /**
     * @return string[]
     */
    public function findPublicationRecordByIdentifier(string $publicationIdentifier): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, DataClass::PROPERTY_ID),
            new StaticConditionVariable($publicationIdentifier)
        );

        return $this->getDataClassRepository()->record(
            Publication::class, new RecordRetrieveParameters(
                new RetrieveProperties([new PropertiesConditionVariable(Publication::class)]), $condition
            )
        );
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationRecords(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $retrieveProperties = [];

        $retrieveProperties[] = new PropertiesConditionVariable(Publication::class);

        $retrieveProperties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TITLE
        );

        $retrieveProperties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
        );

        $retrieveProperties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TYPE
        );

        $retrieveProperties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_CURRENT
        );

        $retrieveProperties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_OWNER_ID
        );

        $properties = new RetrieveProperties($retrieveProperties);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, $count, $offset, $orderBy, $this->getContentObjectPublicationJoins()
        );

        return $this->getDataClassRepository()->records(Publication::class, $parameters);
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        int $type, string $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
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
                return new ArrayCollection();
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition([$condition, $publicationCondition]);
        }
        else
        {
            $condition = $publicationCondition;
        }

        return $this->findPublicationRecords($condition, $count, $offset, $orderBy);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublications(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Publication::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @param string $contentObjectIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPublicationsForContentObjectIdentifier(string $contentObjectIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObjectIdentifier)
        );

        return $this->findPublications($condition);
    }

    /**
     * @param string[] $publicationIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findVisiblePublicationRecordsForPublicationIdentifiers(
        array $publicationIdentifiers, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Publication::class, DataClass::PROPERTY_ID), $publicationIdentifiers
        );

        $conditions[] = $this->getTimeConditions();

        return $this->findPublicationRecords(new AndCondition($conditions), $count, $offset, $orderBy);
    }

    protected function getContentObjectPublicationJoins(): Joins
    {
        $joins = [];

        $joins[] = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_CONTENT_OBJECT_ID),
                new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID)
            )
        );

        return new Joins($joins);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    protected function getTimeConditions(): AndCondition
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

        $timeConditions[] = new OrCondition([$foreverCondition, $betweenCondition]);

        return new AndCondition($timeConditions);
    }

    public function updatePublication(Publication $publication): bool
    {
        return $this->getDataClassRepository()->update($publication);
    }
}

