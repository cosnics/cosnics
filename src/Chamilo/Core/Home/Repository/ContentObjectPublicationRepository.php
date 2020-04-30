<?php
namespace Chamilo\Core\Home\Repository;

use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
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

    /**
     *
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * ContentObjectPublicationRepository constructor.
     *
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * Finds the content object publications by element id
     *
     * @param int $elementId
     *
     * @return ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByElementId($elementId)
    {
        return $this->publicationRepository->getPublicationsWithContentObjects(
            new RecordRetrievesParameters(null, $this->getConditionByElementId($elementId)),
            ContentObjectPublication::class);
    }

    /**
     * Finds a single content object publication by a given element id
     *
     * @param int $elementId
     *
     * @return ContentObjectPublication
     */
    public function findFirstContentObjectPublicationByElementId($elementId)
    {
        return DataManager::retrieve(
            ContentObjectPublication::class,
            new DataClassRetrieveParameters($this->getConditionByElementId($elementId)));
    }

    /**
     * Clears the content object publications for a given element
     *
     * @param int $elementId
     *
     * @return bool
     */
    public function deleteContentObjectPublicationsForElement($elementId)
    {
        return DataManager::deletes(ContentObjectPublication::class, $this->getConditionByElementId($elementId));
    }

    /**
     * Returns the content object publications by a given content object id
     *
     * @param int $contentObjectId
     *
     * @return ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        return $this->publicationRepository->getPublicationsWithContentObjects(
            new RecordRetrievesParameters(null, $this->getConditionByContentObjectId($contentObjectId)),
            ContentObjectPublication::class);
    }

    /**
     * Returns the amount of content object publications by a given content object id
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        return DataManager::count(
            ContentObjectPublication::class,
            new DataClassCountParameters($this->getConditionByContentObjectId($contentObjectId)));
    }

    /**
     * Returns the amount of content object publications by multiple content object ids
     *
     * @param int[] $contentObjectIds
     *
     * @return int
     */
    public function countContentObjectPublicationsByContentObjectIds($contentObjectIds = array())
    {
        return DataManager::count(
            ContentObjectPublication::class,
            new DataClassCountParameters($this->getConditionByContentObjectIds($contentObjectIds)));
    }

    /**
     * Deletes content object publications for a given content object id
     *
     * @param int $contentObjectId
     *
     * @return bool
     */
    public function deleteContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        return DataManager::deletes(
            ContentObjectPublication::class,
            $this->getConditionByContentObjectId($contentObjectId));
    }

    /**
     * Returns a single content object publication by a given id
     *
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function findContentObjectPublicationById($publicationId)
    {
        return DataManager::retrieve_by_id(ContentObjectPublication::class, $publicationId);
    }

    /**
     * Retrieves content object publications by a given content object owner id
     *
     * @param int $ownerId
     *
     * @return ContentObjectPublication[]
     */
    public function findContentObjectPublicationsByContentObjectOwnerId($ownerId)
    {
        return $this->publicationRepository->getPublicationsWithContentObjects(
            new RecordRetrievesParameters(null, $this->getConditionByContentObjectOwnerId($ownerId)),
            ContentObjectPublication::class);
    }

    /**
     * Count content object publications by a given content object owner id
     *
     * @param int $ownerId
     *
     * @return int
     */
    public function countContentObjectPublicationsByContentObjectOwnerId($ownerId)
    {
        return $this->publicationRepository->countPublicationsWithContentObjects(
            new DataClassCountParameters($this->getConditionByContentObjectOwnerId($ownerId)),
            ContentObjectPublication::class);
    }

    /**
     * Builds the condition for ContentObjectPublications based on an element id
     *
     * @param int $elementId
     *
     * @return EqualityCondition
     */
    protected function getConditionByElementId($elementId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($elementId));
    }

    /**
     * Builds the condition for ContentObjectPublications based on a content object id
     *
     * @param int $contentObjectId
     *
     * @return EqualityCondition
     */
    protected function getConditionByContentObjectId($contentObjectId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObjectId));
    }

    /**
     * Builds the condition for ContentObjectPublications based on multiple content object ids
     *
     * @param int[] $contentObjectIds
     *
     * @return EqualityCondition
     */
    protected function getConditionByContentObjectIds($contentObjectIds = array())
    {
        return new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIds);
    }

    /**
     * Builds the condition for ContentObjectPublications based on an content object owner id
     *
     * @param int $ownerId
     *
     * @return EqualityCondition
     */
    protected function getConditionByContentObjectOwnerId($ownerId)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($ownerId));
    }
}