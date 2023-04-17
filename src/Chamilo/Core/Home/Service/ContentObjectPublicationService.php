<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Storage\DataClass\Element;
use RuntimeException;

/**
 * Service to manage content object publications for this application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationService
{

    protected ContentObjectPublicationRepository $contentObjectPublicationRepository;

    public function __construct(ContentObjectPublicationRepository $contentObjectPublicationRepository)
    {
        $this->contentObjectPublicationRepository = $contentObjectPublicationRepository;
    }

    public function countContentObjectPublicationsByContentObjectId($contentObjectId): int
    {
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    /**
     * @param int[] $contentObjectIds
     */
    public function countContentObjectPublicationsByContentObjectIds($contentObjectIds = []): int
    {
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectIds(
            $contentObjectIds
        );
    }

    /**
     * @param int $ownerId
     */
    public function countContentObjectPublicationsByContentObjectOwnerId($ownerId): int
    {
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectOwnerId(
            $ownerId
        );
    }

    /**
     * @param int $publicationId
     */
    public function deleteContentObjectPublicationById($publicationId): bool
    {
        $contentObjectPublication = $this->getContentObjectPublicationById($publicationId);

        if (!$contentObjectPublication)
        {
            return false;
        }

        return $contentObjectPublication->delete();
    }

    /**
     * Deletes content object publications for a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        $this->contentObjectPublicationRepository->deleteContentObjectPublicationsByContentObjectId($contentObjectId);
    }

    /**
     * Returns a single content object publication by a given id
     *
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function getContentObjectPublicationById($publicationId): ?ContentObjectPublication
    {
        return $this->contentObjectPublicationRepository->findContentObjectPublicationById($publicationId);
    }

    /**
     * Returns the content object publications by a given content object id
     *
     * @param int $contentObjectId
     *
     * @return ContentObjectPublication[]
     */
    public function getContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        return $this->contentObjectPublicationRepository->findContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    /**
     * Retrieves content object publications by a given content object owner id
     *
     * @param int $ownerId
     *
     * @return ContentObjectPublication[]
     */
    public function getContentObjectPublicationsByContentObjectOwnerId($ownerId)
    {
        return $this->contentObjectPublicationRepository->findContentObjectPublicationsByContentObjectOwnerId($ownerId);
    }

    /**
     * Returns the content object publications for a given element
     *
     * @param Element $element
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function getContentObjectPublicationsForElement(Element $element)
    {
        return $this->contentObjectPublicationRepository->findContentObjectPublicationsByElementId($element->getId());
    }

    /**
     * Returns the first content object publication for a given element
     *
     * @param Element $element
     *
     * @return ContentObjectPublication
     */
    public function getFirstContentObjectPublicationForElement(Element $element)
    {
        return $this->contentObjectPublicationRepository->findFirstContentObjectPublicationByElementId(
            $element->getId()
        );
    }

    /**
     * Publish a content object by a given element and content object id
     *
     * @param Element $element
     * @param int $contentObjectId
     */
    public function publishContentObject(Element $element, $contentObjectId)
    {
        if ($contentObjectId == 0)
        {
            return;
        }

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->set_element_id($element->getId());
        $contentObjectPublication->set_content_object_id($contentObjectId);

        if (!$contentObjectPublication->create())
        {
            throw new RuntimeException(
                sprintf('Could not publish the content object %s in element %s', $contentObjectId, $element->getId())
            );
        }
    }

    /**
     * Clears the content object publications for a given element and publishes a new content object
     *
     * @param Element $element
     * @param int $contentObjectId
     */
    public function setOnlyContentObjectForElement(Element $element, $contentObjectId)
    {
        if (!$this->contentObjectPublicationRepository->deleteContentObjectPublicationsForElement($element->getId()))
        {
            throw new RuntimeException('Could not clear the publications for element ' . $element->getId());
        }

        $this->publishContentObject($element, $contentObjectId);
    }
}
