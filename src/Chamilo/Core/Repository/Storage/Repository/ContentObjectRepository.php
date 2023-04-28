<?php
namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\ContentObjectAttachment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectRepository
{
    public const PROPERTY_USED_STORAGE_SPACE = 'used_storage_space';

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countContentObjectAttachmentsByIdentifierAndType(
        ContentObject $contentObject, ?int $attachmentIdentifier = null, string $type = ContentObject::ATTACHMENT_NORMAL
    ): int
    {
        $conditions = [];

        if (!is_null($attachmentIdentifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
                ), new StaticConditionVariable($attachmentIdentifier)
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );

        return $this->getDataClassRepository()->count(
            ContentObjectAttachment::class, new DataClassCountParameters(new AndCondition($conditions))
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createContentObject(ContentObject $contentObject): bool
    {
        return $this->getDataClassRepository()->create($contentObject);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createContentObjectAttachment(ContentObjectAttachment $contentObjectAttachment): bool
    {
        return $this->getDataClassRepository()->create($contentObjectAttachment);
    }

    public function deleteContentObjectAttachment(ContentObjectAttachment $contentObjectAttachment): bool
    {
        return $this->getDataClassRepository()->delete($contentObjectAttachment);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    protected function getUsedStorageSpaceConditionForContentObjectTypeAndUser(
        string $contentObjectType, ?User $user = null
    ): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable($contentObjectType)
        );

        if ($user instanceof User)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                new StaticConditionVariable($user->getId())
            );
        }

        return new AndCondition($conditions);
    }

    public function getUsedStorageSpaceForContentObjectType(string $contentObjectType): int
    {
        return $this->getUsedStorageSpaceForContentObjectTypeAndCondition(
            $contentObjectType, $this->getUsedStorageSpaceConditionForContentObjectTypeAndUser($contentObjectType)
        );
    }

    /**
     * @param class-string<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjectType
     */
    public function getUsedStorageSpaceForContentObjectTypeAndCondition(string $contentObjectType, Condition $condition
    ): int
    {
        $storageSpaceProperty = $contentObjectType::getStorageSpaceProperty();

        if (empty($storageSpaceProperty))
        {
            return 0;
        }

        $retrieveProperties = new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::SUM,
                    new PropertyConditionVariable($contentObjectType, $storageSpaceProperty),
                    self::PROPERTY_USED_STORAGE_SPACE
                )
            ]
        );

        if ($contentObjectType::isExtended())
        {
            $joins = new Joins(
                [
                    new Join(
                        ContentObject::class, new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
                            new PropertyConditionVariable(
                                $contentObjectType, $contentObjectType::PROPERTY_ID
                            )
                        )
                    )
                ]
            );
        }
        else
        {
            $joins = null;
        }

        $usedStorageSpaceRecord = $this->getDataClassRepository()->record(
            $contentObjectType, new RecordRetrieveParameters($retrieveProperties, $condition, null, $joins)
        );

        return (int) $usedStorageSpaceRecord[ContentObjectRepository::PROPERTY_USED_STORAGE_SPACE];
    }

    public function getUsedStorageSpaceForContentObjectTypeAndUser(string $contentObjectType, User $user): int
    {
        return $this->getUsedStorageSpaceForContentObjectTypeAndCondition(
            $contentObjectType,
            $this->getUsedStorageSpaceConditionForContentObjectTypeAndUser($contentObjectType, $user)
        );
    }

    public function retrieveContentObjectAttachmentByIdentifierAndType(
        ContentObject $contentObject, int $attachmentIdentifier, string $type = ContentObject::ATTACHMENT_NORMAL
    ): ?ContentObjectAttachment
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($attachmentIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );

        return $this->getDataClassRepository()->retrieve(
            ContentObjectAttachment::class, new DataClassRetrieveParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $type
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     * @throws \Exception
     */
    public function retrieveContentObjectAttachments(
        ContentObject $contentObject, string $type = ContentObject::ATTACHMENT_NORMAL, ?OrderBy $orderBy = null,
        ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );

        $join = new Join(
            ContentObjectAttachment::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
                ), new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID)
            )
        );

        $parameters = new DataClassRetrievesParameters(
            new AndCondition($conditions), $count, $offset, $orderBy, new Joins([$join])
        );

        return $this->retrieveContentObjects(ContentObject::class, $parameters);
    }

    public function retrieveContentObjectByIdentifier(string $identifier): ?ContentObject
    {
        return $this->getDataClassRepository()->retrieveById(ContentObject::class, $identifier);
    }

    /**
     * @param string $contentObjectType
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveContentObjects(string $contentObjectType, DataClassRetrievesParameters $parameters
    ): ArrayCollection
    {
        if ($parameters->getCondition() instanceof Condition)
        {
            $conditions[] = $parameters->getCondition();
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
            ContentObject::get_active_status_types()
        );

        $parameters->setCondition(new AndCondition($conditions));

        return $this->getDataClassRepository()->retrieves(
            $contentObjectType, $parameters
        );
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveContentObjectsByIdentifiers(array $identifiers): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(
            ContentObject::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param bool $includeLast
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveVersionsForContentObject(
        ContentObject $contentObject, bool $includeLast = true, bool $includeSelf = true
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($contentObject->get_object_number())
        );

        if (!$includeLast)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CURRENT),
                new StaticConditionVariable(ContentObject::CURRENT_OLD)
            );
        }

        if (!$includeSelf)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
                    new StaticConditionVariable($contentObject->getId())
                )
            );
        }

        $orderBy = new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID), SORT_DESC
            )
        ]);

        $parameters = new DataClassRetrievesParameters(new AndCondition($conditions), null, null, $orderBy);

        return $this->retrieveContentObjects(get_class($contentObject), $parameters);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateContentObject(ContentObject $contentObject): bool
    {
        return $this->getDataClassRepository()->update($contentObject);
    }
}