<?php
namespace Chamilo\Core\Home\Repository;

use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data side for ContentObjectPublications
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationRepository
{
    protected DataClassRepository $dataClassRepository;

    protected PublicationRepository $publicationRepository;

    public function __construct(DataClassRepository $dataClassRepository, PublicationRepository $publicationRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->publicationRepository = $publicationRepository;
    }

    public function countContentObjectPublicationsByContentObjectId($contentObjectId): int
    {
        return $this->getDataClassRepository()->count(
            ContentObjectPublication::class,
            new DataClassCountParameters($this->getConditionByContentObjectId($contentObjectId))
        );
    }

    public function countContentObjectPublicationsByContentObjectIds($contentObjectIds = []): int
    {
        return $this->getDataClassRepository()->count(
            ContentObjectPublication::class,
            new DataClassCountParameters($this->getConditionByContentObjectIds($contentObjectIds))
        );
    }

    public function countContentObjectPublicationsByContentObjectOwnerId(string $ownerId): int
    {
        return $this->getPublicationRepository()->countPublicationsWithContentObjects(
            new DataClassCountParameters($this->getConditionByContentObjectOwnerId($ownerId)),
            ContentObjectPublication::class
        );
    }

    public function createContentObjectPublication(ContentObjectPublication $contentObjectPublication): bool
    {
        return $this->getDataClassRepository()->create($contentObjectPublication);
    }

    public function deleteContentObjectPublication(ContentObjectPublication $contentObjectPublication): bool
    {
        return $this->getDataClassRepository()->delete($contentObjectPublication);
    }

    public function deleteContentObjectPublicationsByContentObjectId(string $contentObjectId): bool
    {
        return $this->getDataClassRepository()->deletes(
            ContentObjectPublication::class, $this->getConditionByContentObjectId($contentObjectId)
        );
    }

    public function deleteContentObjectPublicationsForElement(string $elementId): bool
    {
        return $this->getDataClassRepository()->deletes(
            ContentObjectPublication::class, $this->getConditionByElementId($elementId)
        );
    }

    public function findContentObjectPublicationById(string $publicationId): ?ContentObjectPublication
    {
        return $this->getDataClassRepository()->retrieveById(ContentObjectPublication::class, $publicationId);
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByContentObjectId(string $contentObjectId): array
    {
        return $this->getPublicationRepository()->getPublicationsWithContentObjects(
            new RetrievesParameters(condition: $this->getConditionByContentObjectId($contentObjectId)),
            ContentObjectPublication::class
        );
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByContentObjectOwnerId(string $ownerId): array
    {
        return $this->getPublicationRepository()->getPublicationsWithContentObjects(
            new RetrievesParameters(condition: $this->getConditionByContentObjectOwnerId($ownerId)),
            ContentObjectPublication::class
        );
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByElementId(string $elementId): array
    {
        return $this->getPublicationRepository()->getPublicationsWithContentObjects(
            new RetrievesParameters(condition: $this->getConditionByElementId($elementId)),
            ContentObjectPublication::class
        );
    }

    public function findFirstContentObjectPublicationByElementId(string $elementId): ?ContentObjectPublication
    {
        return $this->getDataClassRepository()->retrieve(
            ContentObjectPublication::class, new RetrieveParameters($this->getConditionByElementId($elementId))
        );
    }

    protected function getConditionByContentObjectId(string $contentObjectId): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, Publication::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObjectId)
        );
    }

    protected function getConditionByContentObjectIds(array $contentObjectIds = []): InCondition
    {
        return new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, Publication::PROPERTY_CONTENT_OBJECT_ID
            ), $contentObjectIds
        );
    }

    protected function getConditionByContentObjectOwnerId(string $ownerId): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($ownerId)
        );
    }

    protected function getConditionByElementId(string $elementId): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ELEMENT_ID
            ), new StaticConditionVariable($elementId)
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function getPublicationRepository(): PublicationRepository
    {
        return $this->publicationRepository;
    }

    public function updateContentObjectPublication(ContentObjectPublication $contentObjectPublication): bool
    {
        return $this->getDataClassRepository()->update($contentObjectPublication);
    }
}