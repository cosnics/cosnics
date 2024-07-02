<?php
namespace Chamilo\Application\Portfolio\Storage\Repository;

use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Portfolio\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationRepository
{

    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countPublication(Condition $condition = null)
    {
        $parameters =
            new DataClassCountParameters(condition: $condition, joins: $this->getContentObjectPublicationJoins());

        return $this->getDataClassRepository()->count(Publication::class, $parameters);
    }

    public function countPublications(Condition $condition)
    {
        return $this->getDataClassRepository()->count(
            Publication::class, new DataClassCountParameters(condition: $condition)
        );
    }

    /**
     * @param int $contentObjectIdentifiers
     *
     * @return int
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
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countPublicationsForTypeAndIdentifier(
        int $type = PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, string $objectIdentifier,
        ?Condition $condition = null
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
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function createPublication(Publication $publication)
    {
        return $this->getDataClassRepository()->create($publication);
    }

    /**
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function deletePublication(Publication $publication)
    {
        return $this->getDataClassRepository()->delete($publication);
    }

    /**
     * @param string $publicationIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier(string $publicationIdentifier)
    {
        return $this->getDataClassRepository()->retrieveById(Publication::class, $publicationIdentifier);
    }

    /**
     * @param int $userIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function findPublicationForUserIdentifier(int $userIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->getDataClassRepository()->retrieve(
            Publication::class, new RetrieveParameters(condition: $condition)
        );
    }

    /**
     * @param int $publicationIdentifier
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
            Publication::class, new RetrieveParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
                [new PropertiesConditionVariable(Publication::class)]
            )
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     */
    public function findPublicationRecords(
        Condition $condition = null, int $count = null, int $offset = null, OrderBy $orderBy = new OrderBy()
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

        $parameters = new RetrievesParameters(
            condition: $condition, count: $count, offset: $offset, orderBy: $orderBy,
            joins: $this->getContentObjectPublicationJoins(), retrieveProperties: $properties
        );

        return $this->getDataClassRepository()->records(Publication::class, $parameters);
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return string[]
     */
    public function findPublicationRecordsForTypeAndIdentifier(
        $type = PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, string $objectIdentifier,
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, OrderBy $orderBy = new OrderBy()
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
            $condition = new AndCondition([$condition, $publicationCondition]);
        }
        else
        {
            $condition = $publicationCondition;
        }

        return $this->findPublicationRecords($condition, $count, $offset, $orderBy);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication[]
     * @throws \Exception
     */
    public function findPublications(
        Condition $condition = null, int $count = null, int $offset = null, OrderBy $orderBy = new OrderBy()
    )
    {
        return $this->getDataClassRepository()->retrieves(
            Publication::class, new RetrievesParameters(
                condition: $condition, count: $count, offset: $offset, orderBy: $orderBy
            )
        );
    }

    /**
     * @param int $contentObjectIdentifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication[]
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
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function updatePublication(Publication $publication)
    {
        return $this->getDataClassRepository()->update($publication);
    }
}

