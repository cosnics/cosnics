<?php

namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Storage\DataClass\Element;

/**
 * Service to manage content object publications for this application
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationService
{
    /**
     * @var ContentObjectPublicationRepository
     */
    protected $contentObjectPublicationRepository;

    /**
     * ContentObjectPublicationService constructor.
     *
     * @param ContentObjectPublicationRepository $contentObjectPublicationRepository
     */
    public function __construct(ContentObjectPublicationRepository $contentObjectPublicationRepository)
    {
        $this->contentObjectPublicationRepository = $contentObjectPublicationRepository;
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
            throw new \RuntimeException('Could not clear the publications for element ' . $element->getId());
        }

        $this->publishContentObject($element, $contentObjectId);
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
            throw new \RuntimeException(
                sprintf('Could not publish the content object %s in element %s', $contentObjectId, $element->getId())
            );
        }
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
     * Returns the amount of content object publications by a given content object id
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
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
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectIds(
            $contentObjectIds
        );
    }

    /**
     * Deletes content object publications for a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByContentObjectId($contentObjectId)
    {
        $this->contentObjectPublicationRepository->deleteContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    /**
     * Returns a single content object publication by a given id
     *
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function getContentObjectPublicationById($publicationId)
    {
        return $this->contentObjectPublicationRepository->findContentObjectPublicationById($publicationId);
    }

    /**
     * Deletes a single content object publication by a given id
     *
     * @param int $publicationId
     *
     * @return bool
     */
    public function deleteContentObjectPublicationById($publicationId)
    {
        $contentObjectPublication = $this->getContentObjectPublicationById($publicationId);

        if (!$contentObjectPublication)
        {
            return false;
        }

        return $contentObjectPublication->delete();
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
     * Count content object publications by a given content object owner id
     *
     * @param int $ownerId
     *
     * @return int
     */
    public function countContentObjectPublicationsByContentObjectOwnerId($ownerId)
    {
        return $this->contentObjectPublicationRepository->countContentObjectPublicationsByContentObjectOwnerId(
            $ownerId
        );
    }
}
